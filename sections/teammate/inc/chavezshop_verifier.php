<?php

/**
*
*/
class chavezShopVerifier
{
	var $remote_site = 'http://enriquechavez.co';
	var $license_key;
	var $section_name;
	var $section_key;

	function __construct($section_name, $section_version, $license_key)
	{
		if( pl_get_mode() != 'draft' ){
            return;
        }

        if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
            include( dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php' );
        }

        $this->license_key  = trim( $license_key );
        $this->section_name = trim( $section_name );
        $this->section_key = strtolower( str_replace(' ', '_', $this->section_name) );

        if($license_key){
        	if( !$this->is_license_active() ){
        		$this->active_license();
        	}
        }else{
        	delete_option( $this->section_key."_activated");
			delete_option( $this->section_key.'_license');
			delete_transient( $this->section_key.'tmp_valid_status');
        }
	}

	function check_license(){
		if( get_transient( $this->section_key.'tmp_valid_status' ) ){
			return get_transient( $this->section_key.'tmp_valid_status' );
		}
		$api_params = array(
			'edd_action' => 'check_license',
			'license' => $this->license_key,
			'item_name' => urlencode( $this->section_key )
		);

		$response = wp_remote_get( add_query_arg( $api_params, $this->remote_site ), array( 'timeout' => 15, 'sslverify' => false ) );

		if ( is_wp_error( $response ) )
			return false;

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if( $license_data->license == 'valid' ) {
			set_transient( $this->section_key.'tmp_valid_status', 'Valid', DAY_IN_SECONDS );
			return true;
		} else {
			return false;
		}
	}

	function active_license(){
		$api_params = array(
			'edd_action'=> 'activate_license',
			'license' 	=> $this->license_key,
			'item_name' => urlencode( $this->section_name )
		);

		$response = wp_remote_get( add_query_arg( $api_params, $this->remote_site ), array( 'timeout'  => 15, 'sslverify' => false) );

		if ( is_wp_error( $response ) ){
			return false;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if( $license_data->license == 'valid' ){
			update_option( $this->section_key."_activated", true);
			update_option( $this->section_key.'_license', $this->license_key, '', 'yes' );
		}
	}

	function is_license_active(){
		return get_option( $this->section_key."_activated" );
	}

}




