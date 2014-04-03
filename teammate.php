<?php
/*
    Plugin Name: Teammate
    Author: Enrique Chavez
    Author URI: http://enriquechavez.co
    Description: Teammate is a DMS section that allows you to show details for a company member or work team member. Every teammate box has up to 12 configuration options: Avatar, Name, Position, mini-bio, and up to 8 social media links. This section can be used to create a detailed "About Us", "Meet the team", or can even be used to create a "Testimonials" page.
    Class Name: TMTeammate
    Demo: http://dms.tmeister.net/teammate
    Version: 1.2
    Filter: misc
    PageLines: true
*/

//add_action( 'admin_init', 'teammate_check_for_updates' );

function teammate_check_for_updates(){
    $item_name  = "Teammate";
    $item_key = strtolower( str_replace(' ', '_', $item_name) );

    if( get_option( $item_key."_activated" )){
        if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
            include( dirname( __FILE__ ) . '/sections/teammate/inc/EDD_SL_Plugin_Updater.php' );
        }

        $license_key = trim( get_option( $item_key."_license", $default = false ) );

        $edd_updater = new EDD_SL_Plugin_Updater( 'http://enriquechavez.co', __FILE__, array(
                'version'   => '1.2',
                'license'   => $license_key,
                'item_name' => $item_name,
                'author'    => 'Enrique Chavez'
            )
        );
    }
}
