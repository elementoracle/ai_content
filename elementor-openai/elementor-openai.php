<?php
/**
 * Plugin Name: Elementor OpenAI Integration
 * Description: Generates Open AI content inside of Elementor dynamic tags.
 * Plugin URI:  https://elementoracle.com/
 * Version:     1.0.0
 * Author:      ElementOracle
 * Author URI:  https://elementoracle.com/
 * Text Domain: elementor-dynamic-tag-openai
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Register Dynamic Tag.
 */
function register_openai( $dynamic_tags_manager ) {
	require_once( __DIR__ . '/dynamic-tags/dynamic-tag-openai.php' );
	$dynamic_tags_manager->register( new \Elementor_Dynamic_Tag_Openai );
}
add_action( 'elementor/dynamic_tags/register', 'register_openai' );


//OpenAI admin settings
function openai_settings_init() {
	//openai api key
    add_option( 'openai_api_key', '' );
	register_setting( 'openai_options_group', 'openai_api_key', 'openai_callback' );
	//default opanai model
    add_option( 'openai_model', 'text-davinci-002' );
	register_setting( 'openai_options_group', 'openai_model', 'openai_callback' );
}
add_action( 'admin_init', 'openai_settings_init' );

//Options page (Admin:Settings:OpenAI)
function openai_register_options_page() {
  add_options_page( 'OpenAI', 'OpenAI', 'manage_options', 'openai_options_page', 'openai_options_page' );
}

add_action( 'admin_menu', 'openai_register_options_page' );

//Admin screen 
function openai_options_page() {
?>

<div class="openaiOptionsContainer">
	<div>
	<h1>Elementor OpenAI Integration</h1>
    <h2>By <a href="https://elementoracle.com/" target="_blank">ElementOracle</a></h2>
    <p>Usage: Click the "dynamic tag" icon on any Elementor widget and select "Open AI". Write a prompt sentence in the tag settings to generate text.</p>
	</div>
	<div class="openaiAdminForm">
	  <form method="post" action="options.php">
		  <?php settings_fields( 'openai_options_group' ); ?>
		  <fieldset style="border:1px solid #ccc; padding:20px; margin-top:20px; background:#fff">
		  <legend><span class="dashicons dashicons-admin-generic"></span>Settings</legend>     
          <label for="openai_api_key">API key:</label>
		  <input type="password" id="openai_api_key" name="openai_api_key" value="<?php echo get_option('openai_api_key'); ?>" placeholder="Enter your API key" required />
          <p style="font-size:.8em"><em>Get your API key here: <a href="https://beta.openai.com/account/api-keys" target="_blank">https://beta.openai.com/account/api-keys</a></em></p>
          <label for="openai_model">Model:</label>
          <select name="openai_model">
            <?php if( get_option('openai_model') !="" ){ ?>
            <option value="<?php echo get_option('openai_model'); ?>" selected><?php echo get_option('openai_model'); ?></option>
            <?php } ?>
            <option value="text-ada-001">text-ada-001</option>    
            <option value="text-babbage-001">text-babbage-001</option>    
            <option value="text-curie-001">text-curie-001</option>    
            <option value="text-davinci-002">text-davinci-002</option>
           </select>
		  </fieldset>
          <p style="font-size:.8em"><em>More on models here: <a href="https://beta.openai.com/docs/models" target="_blank">https://beta.openai.com/docs/models</a></em></p>
		  <?php submit_button(); ?>
	  </form>
	</div>
</div>

<?php
} // end openai admin page code