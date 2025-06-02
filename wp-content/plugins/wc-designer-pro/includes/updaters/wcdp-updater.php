<?php

class WCDP_Updater{

	protected $version_url = 'http://jmaplugins.com/wcdp-notifier.xml';
	public $title = 'Woocommerce Designer Pro';

	protected $auto_updater = false;
	protected $upgrade_manager = false;
	protected $iframe = false;

	public function init(){
		add_filter( 'upgrader_pre_download', array( $this, 'upgradeFilterFromEnvato' ), 10, 4 );
		add_action( 'upgrader_process_complete', array( $this, 'removeTemporaryDir' ) );
	}
	public function setUpdateManager(WCDP_Updating_Manager $updater){
		$this->auto_updater = $updater;
	}
	public function updateManager(){
		return $this->auto_updater;
	}
	public function versionUrl(){
		return $this->version_url;
	}
	public function upgradeFilterFromEnvato($reply, $package, $updater){
		global $wp_filesystem;
		if((isset($updater->skin->plugin) && $updater->skin->plugin === WCDP_MAIN_FILE) ||
		    (isset($updater->skin->plugin_info) && $updater->skin->plugin_info['Name'] === $this->title)){
			
			$updater->strings['download_envato'] = __( 'Downloading package from envato market...', 'wcdp' );
			$updater->skin->feedback( 'download_envato' );
			$package_filename = 'wc-designer-pro.zip';
			$res = $updater->fs_connect( array( WP_CONTENT_DIR ) );
			if(!$res){
				return new WP_Error( 'no_credentials', __( "Error! Can't connect to filesystem", 'wcdp' ) );
			}			
		    $settings = get_option('wcdp-settings-license');
		    $username = $settings['envato_username'];
		    $api_key = $settings['envato_api_key'];
		    $purchase_code = trim($settings['envato_purchase_code']);

			if(empty($username) || empty($api_key) || empty($purchase_code)){
				return new WP_Error( 'no_credentials', __( 'To receive automatic updates license activation is required. Please visit <a href="' . admin_url( 'admin.php?page=wcdp-content-settings&tab=license' ) . '' . '" target="_blank">Settings</a> to activate your Woocommerce Designer Pro.', 'wcdp' ) );
			}			
            $response = wp_remote_get($this->envatoDownloadPurchaseUrl($purchase_code),
                array(
                    'headers' => array(
                        'Authorization' => 'Bearer ' . $api_key,
                        'User-Agent' => 'Automatic update for the WooCommerce Designer Pro - www.jmaplugins.com'
                    )
                )
            );				
			$result = json_decode($response['body'], true);
			
			if(!isset($result['download_url'])){
				return new WP_Error( 'no_credentials', __( 'Error! Envato API error', 'wcdp' ) . (isset($result['error']) ? ': '. $result['error'] : '') . (isset($result['description']) ? ' - '. $result['description'] : ''));
			}
			$download_file = download_url($result['download_url']);
			
			if(is_wp_error($download_file)){
				return $download_file;
			}
			$upgrade_folder = $wp_filesystem->wp_content_dir() . 'uploads/wcdp_envato_package';
			
			if(is_dir($upgrade_folder)){
				$wp_filesystem->delete($upgrade_folder, true);
			}
			mkdir($upgrade_folder, 0755, true);
			$result = copy($download_file, $upgrade_folder .'/'. $package_filename);
			
			if($result && is_file($upgrade_folder .'/'. $package_filename)){
				return $upgrade_folder .'/'. $package_filename;
			}
			return new WP_Error( 'no_credentials', __( 'Error copy package', 'wcdp' ) );
		}
		return $reply;
	}
	public function removeTemporaryDir(){
		global $wp_filesystem;
		if(is_dir($wp_filesystem->wp_content_dir() .'uploads/wcdp_envato_package')){
			$wp_filesystem->delete($wp_filesystem->wp_content_dir() .'uploads/wcdp_envato_package', true);
		}
	}
	protected function envatoDownloadPurchaseUrl($purchase_code){
		return 'https://api.envato.com/v3/market/buyer/download?purchase_code='. urlencode(trim($purchase_code)) .'';
	}
}