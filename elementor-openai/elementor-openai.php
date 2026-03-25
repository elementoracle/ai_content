<?php
/**
 * Plugin Name: Elementor OpenAI Integration
 * Description: Generates Open AI content inside of Elementor dynamic tags.
 * Plugin URI:  https://elementoracle.com/
 * Version:     1.0.0
 * Author:      ElementOracle
 * Author URI:  https://elementoracle.com/
 * Text Domain: elementor-dynamic-tag-openai
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Tested up to:      6.4
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
function elementoracle_openai_settings_init() {
	add_option( 'openai_api_key', '' );
	register_setting( 'openai_options_group', 'openai_api_key', [
		'sanitize_callback' => 'sanitize_text_field',
	] );
	add_option( 'openai_model', 'text-davinci-002' );
	register_setting( 'openai_options_group', 'openai_model', [
		'sanitize_callback' => 'sanitize_text_field',
	] );
}
add_action( 'admin_init', 'elementoracle_openai_settings_init' );

//Options page (Admin:Settings:OpenAI)
function elementoracle_openai_register_options_page() {
	add_options_page( 'OpenAI', 'OpenAI', 'manage_options', 'openai_options_page', 'elementoracle_openai_options_page' );
}
add_action( 'admin_menu', 'elementoracle_openai_register_options_page' );

//Admin screen
function elementoracle_openai_options_page() {
	$allowed_models = [ 'text-ada-001', 'text-babbage-001', 'text-curie-001', 'text-davinci-002' ];
	$saved_model    = get_option( 'openai_model', 'text-davinci-002' );
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
				<input type="password" id="openai_api_key" name="openai_api_key" value="<?php echo esc_attr( get_option( 'openai_api_key' ) ); ?>" placeholder="Enter your API key" required />
				<p style="font-size:.8em"><em>Get your API key here: <a href="https://platform.openai.com/account/api-keys" target="_blank">https://platform.openai.com/account/api-keys</a></em></p>
				<label for="openai_model">Model:</label>
				<select id="openai_model" name="openai_model">
					<?php foreach ( $allowed_models as $m ) : ?>
						<option value="<?php echo esc_attr( $m ); ?>" <?php selected( $saved_model, $m ); ?>><?php echo esc_html( $m ); ?></option>
					<?php endforeach; ?>
				</select>
			</fieldset>
			<p style="font-size:.8em"><em>More on models here: <a href="https://platform.openai.com/docs/models" target="_blank">https://platform.openai.com/docs/models</a></em></p>
			<?php submit_button(); ?>
		</form>
	</div>
</div>

<?php
} // end openai admin page code