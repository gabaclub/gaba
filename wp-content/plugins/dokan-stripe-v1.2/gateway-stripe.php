<?php
/*
Plugin Name: Dokan - Stripe Connect
Plugin URI: http://wedevs.com/support/
Description: Accept credit card payments and allow your sellers to get automatic split payment in Dokan via Stripe.
Version: 1.2
Author: weDevs Team
Author URI: http://wedevs.com
Text Domain: dokan-stripe
License: GNU General Public License v3.0
*/


/**
 * Copyright (c) 2015 weDevs (email: info@wedevs.com). All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * **********************************************************************
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'DOKAN_STRIPE_FILE', __FILE__ );
define( 'DOKAN_STRIPE_PATH', dirname( __FILE__ ) );

/**
 * Dokan Stripe Main class
 *
 * @author weDevs<info@wedevs.com>
 */
class Dokan_Stripe {

    /**
     * Constructor
     */
    public function __construct() {

        /** All actions */
        add_action( 'plugins_loaded', array( $this, 'init' ) );
        add_action( 'init', array( $this, 'stripe_check_connect' ) );

        /** All filter */
        add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
        add_filter( 'woocommerce_payment_gateways', array( $this, 'register_gateway' ) );

        add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'add_to_cart_validation' ), 10, 2 );

        add_filter( 'dokan_withdraw_methods', array( $this, 'register_dokan_withdraw_gateway' ) );
        add_filter( 'dokan_can_add_product', array( $this, 'can_seller_add_product' ) );
        add_filter( 'dokan_get_dashboard_nav', array( $this, 'remove_withdraw_page' ) );

        add_action( 'edit_user_profile', array( $this, 'stripe_admin_menu') , 50 );
        add_action( 'show_user_profile', array( $this, 'stripe_admin_menu') , 50 );
        add_action( 'personal_options_update', array( $this, 'stripe_admin_functions') , 50 );
        add_action( 'edit_user_profile_update', array( $this, 'stripe_admin_functions') , 50 );
        add_action( 'template_redirect', array( $this, 'delete_stripe_account') , 50 );
    }

    /**
     * Add relevant links to plugins page
     * @param  array $links
     * @return array
     */
    public function plugin_action_links( $links ) {

        $plugin_links = array(
            '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=dokan_stripe_connect' ) . '">' . __( 'Settings', 'dokan-stripe' ) . '</a>',
            '<a href="http://wedevs.com/support/">' . __( 'Support', 'dokan-stripe' ) . '</a>',
            '<a href="http://docs.wedevs.com">' . __( 'Documentation', 'dokan-stripe' ) . '</a>',
        );

        return array_merge( $plugin_links, $links );
    }

    /**
     * Init localisations and files
     */
    public function init() {

        // updater class
        require_once dirname( __FILE__ ) . '/classes/lib/wedevs-updater/wedevs-updater.php';
        new WeDevs_Plugin_Update_Checker( plugin_basename( __FILE__ ), 'dokan-stripe' );

        if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
            return;
        }

        include_once dirname( __FILE__ ) . '/classes/class-dokan-stripe-connect.php';
        include_once dirname( __FILE__ ) . '/classes/class-dokan-stripe-connect-saved-cards.php';

        load_plugin_textdomain( 'dokan-stripe', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }

    /**
     * Register the gateway for use
     */
    public function register_gateway( $methods ) {

        $methods[] = 'Dokan_Stripe_Connect';

        return $methods;
    }

    /**
     * Check to connect with stripe
     *
     * @return void
     */
    function stripe_check_connect() {

        if ( !empty( $_GET['state'] ) && 'wepay' == $_GET['state'] )
            return;

        if ( empty( $_GET['scope'] ) && empty( $_GET['code'] ) ) return;

        $settings   = get_option('woocommerce_dokan-stripe-connect_settings');
        $client_id  = $settings['testmode'] == 'yes' ? $settings['test_client_id'] : $settings['client_id'];
        $secret_key = $settings['testmode'] == 'yes' ? $settings['test_secret_key'] : $settings['secret_key'];

        require_once 'classes/lib/oauth/OAuth2Exception.php';
        require_once 'classes/lib/oauth/OAuth2Client.php';
        require_once 'classes/lib/StripeOAuth.class.php';

        $oauth = new StripeOAuth( $client_id, $secret_key );
        $token = $oauth->getAccessToken( $_GET['code'] );
        $key   = $oauth->getPublishableKey( $_GET['code'] );

        update_user_meta( get_current_user_id(), '_stripe_connect_access_key', $token );

        wp_redirect( dokan_get_navigation_url( 'settings/payment' ) );
        exit;
    }

    /**
     * Add to cart validation error
     *
     * Customers shouldn't be able to buy product if the sellers stripe account
     * is not connected.
     *
     * @param bool  $validation
     * @param int  $product_id
     *
     * @return bool
     */
    public function add_to_cart_validation( $validation, $product_id ) {
        $settings = get_option('woocommerce_dokan-stripe-connect_settings');

        // bailout if the gateway is not enabled
        if ( isset( $settings['enabled'] ) && $settings['enabled'] !== 'yes' ) {
            return $validation;
        }

        $seller_id    = get_post_field( 'post_author', $product_id );
        $access_token = get_user_meta( $seller_id, '_stripe_connect_access_key', true );

        if ( empty( $access_token ) ) {
            wc_add_notice( __( 'This seller has not configured his Stripe payment gateway and the product can not be purchased!', 'dokan-stripe' ), 'error' );

            return false;
        }

        return $validation;
    }

    /**
     * Prevents non-stripe connected users from creating new product posts
     *
     * @param  array  $errors
     *
     * @return array
     */
    function can_seller_add_product( $errors ) {

        $user_id   = get_current_user_id();
        $stripe_id = get_user_meta( $user_id, '_stripe_connect_access_key', true );

        if ( empty( $stripe_id ) ) {
            $errors[] = sprintf( '%s <a href="%s">%s</a>', __( 'Your Stripe account isn\'t active yet. Please connect to stripe first!', 'dokan-stripe' ), dokan_get_navigation_url('settings'), __( 'Connect to Stripe', 'dokan-stripe' ) );
        }

        return $errors;
    }

    /**
     * Register the stripe gateway for withdraw
     *
     * @param  array  $methods
     *
     * @return array
     */
    function register_dokan_withdraw_gateway( $methods ) {
        $methods['dokan-stripe-connect'] = array(
            'title'    => __( 'Stripe Connect', 'dokan-stripe' ),
            'callback' => array( $this, 'stripe_authorize_button' )
        );

        return $methods;
    }

    /**
     * This enables dokan vendors to connect their stripe account to the site stripe gateway account
     *
     * @param array $store_settings
     */
    function stripe_authorize_button( $store_settings ) {
        $store_user = wp_get_current_user();
        $settings   = get_option('woocommerce_dokan-stripe-connect_settings');

        if ( ! $settings ) {
            _e( 'Stripe gateway is not configured. Please contact admin.', 'dokan-stripe' );
            return;
        }

        $client_id  = $settings['testmode'] == 'yes' ? $settings['test_client_id'] : $settings['client_id'];
        $secret_key = $settings['testmode'] == 'yes' ? $settings['test_secret_key'] : $settings['secret_key'];
        $key        = get_user_meta( $store_user->ID, '_stripe_connect_access_key', true );
        ?>

        <style type="text/css" media="screen">
            .dokan-stripe-connect-container {
                border: 1px solid #eee;
                padding: 15px;
            }

            .dokan-stripe-connect-container .dokan-alert {
                margin-bottom: 0;
            }
        </style>

        <div class="dokan-stripe-connect-container">
            <?php
                if ( empty( $key ) ) {

                    echo '<div class="dokan-alert dokan-alert-danger">';
                        _e( 'Your account is not yet connected with Stripe. Connect with Stripe to receive your commissions.', 'dokan-stripe' );
                    echo '</div>';

                    require_once DOKAN_STRIPE_PATH . '/classes/lib/oauth/OAuth2Exception.php';
                    require_once DOKAN_STRIPE_PATH . '/classes/lib/oauth/OAuth2Client.php';
                    require_once DOKAN_STRIPE_PATH . '/classes/lib/StripeOAuth.class.php';

                    $oauth = new StripeOAuth( $client_id, $secret_key );
                    $url   = $oauth->getAuthorizeUri();
                    ?>
                    <br/>
                    <a class="clear" href="<?php echo $url; ?>" target="_TOP">
                        <img src="<?php echo plugins_url( '/assets/images/blue.png', DOKAN_STRIPE_FILE ); ?>" width="190" height="33" data-hires="true">
                    </a>
                    <?php

                } else {
                    ?>
                    <div class="dokan-alert dokan-alert-success">
                        <?php _e( 'Your account is connected with Stripe.', 'dokan-stripe' ); ?>
                        <a  class="dokan-btn dokan-btn-danger dokan-btn-theme" href="<?php echo wp_nonce_url( add_query_arg( array( 'action' => 'dokan-disconnect-stripe' ), dokan_get_navigation_url( 'settings/payment' ) ), 'dokan-disconnect-stripe' ); ?>"><?php _e( 'Disconnect', 'dokan' ); ?></a>
                    </div>
                    <?php
                }
            ?>
        </div>
        <?php
    }

    /**
     * Remove withdraw page if stripe is enabled
     *
     * @param  array  $urls
     *
     * @return array
     */
    public function remove_withdraw_page( $urls ) {
        $settings = get_option('woocommerce_dokan-stripe-connect_settings');

        // bailout if the gateway is not enabled
        if ( isset( $settings['enabled'] ) && $settings['enabled'] !== 'yes' ) {
            return $urls;
        }

        if ( array_key_exists( 'withdraw', $urls ) ) {
            unset( $urls['withdraw'] );
        }

        return $urls;
    }

    public function delete_stripe_account() {
        $user_id = get_current_user_id();

        if ( !is_user_logged_in() ) {
            return;
        }

        if ( !dokan_is_user_seller( $user_id ) ) {
            return;
        }

        if ( isset( $_GET['action'] ) && $_GET['action'] == 'dokan-disconnect-stripe' ) {

            if ( !wp_verify_nonce( $_GET['_wpnonce'], 'dokan-disconnect-stripe' ) ) {
                return;
            }

            delete_user_meta( $user_id, '_stripe_connect_access_key');
            wp_redirect( dokan_get_navigation_url( 'settings/payment' ) );
            exit;
        }
    }

    /**
     * Admin functions for controlling user Stripe Accounts
     *
     * @param array $store_settings
     */
    function stripe_admin_functions( $user_id ) {

        if ( !dokan_is_user_seller( $user_id ) || ! current_user_can( 'manage_options' )  ) {
            return $user_id;
        }

        $stripe_settings   = get_option('woocommerce_dokan-stripe-connect_settings');

        if ( ! $stripe_settings ) {
            return $user_id;
        }

        $stripe_key = get_user_meta( $user_id, '_stripe_connect_access_key', true );

        if ( isset($_POST['disconnect_user_stripe'] ) ) {
            delete_user_meta( $user_id, '_stripe_connect_access_key');
        }
        return $user_id;
    }

    /**
    * This is admin menu for controlling Seller Stripe status
    *
    * @param array $store_settings
    */
    function stripe_admin_menu( $user ) {

        if ( !dokan_is_user_seller( $user->ID ) || ! current_user_can( 'manage_options' )  ) {
            return $user;
        }

        $stripe_key = get_user_meta( $user->ID, '_stripe_connect_access_key', true );
        ?>
        <h3><?php _e('Dokan Stripe Settings','dokan-stripe');?></h3>
        <?php
        if ( !empty( $stripe_key ) ) : ?>
            <?php submit_button( __( 'Disconnect User Stripe Account', 'dokan-stripe' ) ,'delete', 'disconnect_user_stripe'); ?>
        <?php else : ?>
            <h4><?php _e("User account not connected to Stripe",'dokan-stripe');?></h4>
        <?php
        endif;
    }
}

new Dokan_Stripe();
