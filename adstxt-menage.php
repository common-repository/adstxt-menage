<?php
/*
   Plugin Name: AdsTxt Menage
   Description: Plugin per gestire file ads.txt
   Version: 1.0.0
   Author: Marco Chizzini
   Plugin URI: https://chizzini.com/2019/01/29/ads-txt-menage/
   Author URI: https://chizzini.com

 */

?>
<?php
class AdsTxtMeSetPage
{
	/**
	 * Holds the values to be used in the fields callbacks
	 */
	private $options;

	/**
	 * Start up
	 */
	public function __construct()
	{
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	/**
	 * Add options page
	 */
	public function add_plugin_page()
	{
		// This page will be under "Settings"
		add_options_page(
				'Settings Admin', 
				'ADS Settings', 
				'manage_options', 
				'ads-setting-admin', 
				array( $this, 'create_admin_page' )
				);
	}

	/**
	 * Options page callback
	 */
	public function create_admin_page()
	{
		// Set class property
		$this->options = get_option( 'ads_option' );
		?>
			<div class="wrap">
			<h1>Ads.txt Settings</h1>
			<form method="post" action="options.php">
			<?php
			// This prints out all hidden setting fields
			settings_fields( 'ads_option_group' );
			do_settings_sections( 'ads-setting-admin' );
			submit_button();
		?>
			</form>
			</div>
			<?php
	}

	/**
	 * Register and add settings
	 */
	public function page_init()
	{        
		register_setting(
				'ads_option_group', // Option group
				'ads_option', // Option name
				array( $this, 'sanitize' ) // Sanitize
				);

		add_settings_section(
				'setting_section_id', // ID
				'publisher', // Title
				array( $this, 'print_section_info' ), // Callback
				'ads-setting-admin' // Page
				);  

		add_settings_field(
				'ads', 
				'Ads.txt', 
				array( $this, 'ads_callback' ), 
				'ads-setting-admin', 
				'setting_section_id'
				);      
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize( $input )
	{
		$file = ABSPATH. '/ads.txt';
		echo $file;
		$open = fopen($file, "w") or die("Unable to open file!");

//		$open = fopen( $file, "a" ); 
		$write = fputs( $open, $input['ads'] ); 
		fclose( $open );
		return $input;
	}

	/** 
	 * Print the Section text
	 */
	public function print_section_info()
	{
		print 'Inserisci i tuoi record:';
	}

	/** 
	 * Get the settings option array and print one of its values
	 */
	public function ads_callback()
	{
		printf(

				'<textarea name="ads_option[ads]" id="ads" rows="10" class="large-text">%s</textarea>',
				isset( $this->options['ads'] ) ? esc_attr( $this->options['ads']) : ''
				);
	}
}

if( is_admin() )
	$AdsTxtMeSetPage = new AdsTxtMeSetPage();
