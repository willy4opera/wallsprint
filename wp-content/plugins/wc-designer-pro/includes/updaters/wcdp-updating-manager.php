<?php
/**
 * Manage update messages and Plugins info for WCDP in default Wordpress plugins list.
 */
class WCDP_Updating_Manager{
	
	public $current_version;
	public $update_path;
	public $plugin_slug;
	public $slug;
	protected $url = 'http://codecanyon.net/item/woocommerce-designer-pro-cmyk-card-flyer/22027731';

	function __construct($current_version, $update_path, $plugin_slug){
		$this->current_version = $current_version;
		$this->update_path = $update_path;
		$this->plugin_slug = $plugin_slug;
		$t = explode( '/', $plugin_slug );
		$this->slug = str_replace( '.php', '', $t[1] );

		add_filter('pre_set_site_transient_update_plugins', array( &$this, 'check_update'));
		add_filter('plugins_api', array( &$this, 'check_info' ), 10, 3);
		add_action('in_plugin_update_message-'. WCDP_MAIN_FILE, array(&$this, 'addUpgradeMessageLink'));
	}
	/**
	 * Add our self-hosted autoupdate plugin to the filter transient
	 *
	 * @param $transient
	 * @return object $ transient
	 */	
	public function check_update($transient){
		if(empty($transient->checked )){
			return $transient;
		}
		$remote_version = $this->getRemote_version();

		if(version_compare($this->current_version, $remote_version, '<')){
			$obj = new stdClass();
			$obj->slug = $this->slug;
			$obj->new_version = $remote_version;
			$obj->url = '';
			$obj->package = '';
			$obj->name = wcdp_updater()->title;
			$transient->response[$this->plugin_slug] = $obj;
		}
		return $transient;
	}
	/**
	 * Add our self-hosted description to the filter
	 *
	 * @param boolean $false
	 * @param array $action
	 * @param object $arg
	 * @return bool|object
	 */	
	public function check_info($false, $action, $arg){		
		if(isset( $arg->slug) && $arg->slug === $this->slug){
			$information = $this->getRemote_information();
			$array_pattern = array(
				'/^([\*\s])*(\d\d\.\d\d\.\d\d\d\d[^\n]*)/m',
				'/^\n+|^[\t\s]*\n+/m',
				'/\n/',
			);
			$array_replace = array(
				'<h4>$2</h4>',
				'</div><div>',
				'</div><div>'
			);                 
			$remoteInfo = new stdClass();
            $remoteInfo->name = wcdp_updater()->title;
            $remoteInfo->slug = "wc-designer-pro.php";
            $remoteInfo->plugin_name = "wc-designer-pro.php";						
            $remoteInfo->new_version = "$information->latest";
            $remoteInfo->last_updated = "$information->lastupdated";
            $remoteInfo->sections = array('changelog' => '<div>'. preg_replace($array_pattern, $array_replace, $information->changelog) .'</div>');

			return $remoteInfo;
		}
		return $false;
	}
	/**
	 * Return the remote version
	 *
	 * @return string $remote_version
	 */
	public function getRemote_version(){
		$information = $this->getRemote_information();
		return "$information->latest";
	}
	/**
	 * Get information about the remote version
	 *
	 * @return bool|object
	 */	
	public function getRemote_information(){
        if(function_exists('curl_init')){
			$ch = curl_init( $this->update_path );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_HEADER, 0 );
			curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
			$notifier_data = curl_exec( $ch );
			curl_close( $ch );
		} else {
			$notifier_data = file_get_contents($this->update_path);
		}
        if($notifier_data){
            if(strpos((string) $notifier_data, '<notifier>') === false){
                $notifier_data = '<?xml version="1.0" encoding="UTF-8"?><notifier><latest>1.0</latest><changelog></changelog></notifier>';
            }
        }
		$xml = simplexml_load_string($notifier_data); 
        return $xml;
	}
	/**
	 * Return the status of the plugin licensing
	 *
	 * @return boolean $remote_license
	 */
	public function getRemote_license(){
		$request = wp_remote_post($this->update_path, array('body' => array('action' => 'license')));
		if(! is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200){
			return $request['body'];
		}
		return false;
	}
	/**
	 * Shows message on Wp plugins page with a link for updating from envato.
	 */
	public function addUpgradeMessageLink(){
		$settings = get_option('wcdp-settings-license');
		$username = $settings['envato_username'];
		$api_key = $settings['envato_api_key'];
		$purchase_code = $settings['envato_purchase_code'];
		echo '<style type="text/css" media="all">tr#wc-designer-pro-update.plugin-update-tr a.thickbox + em { display: none; }</style>';
		if(empty($username) || empty($api_key) || empty($purchase_code)){
			echo ' <a href="' . $this->url . '">' . __( 'Download new version from CodeCanyon.', 'wcdp' ) . '</a>';
		} else{
			echo ' <a href="' . wp_nonce_url(admin_url('update.php?action=upgrade-plugin&plugin='. WCDP_MAIN_FILE), 'upgrade-plugin_'. WCDP_MAIN_FILE) .'">' . __( 'Update WooCommerce Designer Pro now.', 'wcdp' ) . '</a>';
		}
	}
}