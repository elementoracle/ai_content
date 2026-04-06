<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Elementor OpenAI Content Generation Dynamic Tag
 */
class Elementor_Dynamic_Tag_Openai extends \Elementor\Core\DynamicTags\Tag {
	/**
	 * Get dynamic tag name.
	 */
	public function get_name() {
		return 'openai';
	}
	/**
	 * Get dynamic tag title.
	 */
	public function get_title() {
		return esc_html__( 'Open AI', 'elementor-dynamic-tag-openai' );
	}

	/**
	 * Get dynamic tag groups.
	 */
	public function get_group() {
		return [ 'site' ];
	}

	/**
	 * Get dynamic tag categories.
	 */
	public function get_categories() {
		return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ];
	}

	/**
	 * Register dynamic tag controls.
	 */
	protected function register_controls() {
		$this->add_control(
			'prompt',
			[
				'label' => esc_html__( 'Prompt', 'elementor-dynamic-tag-openai' ),
				'type' => 'text',
			]
		);
	}

	/**
	 * Render tag output on the frontend.
	 */
	public function render() {
		$settings = $this->get_settings_for_display();
		$prompt   = sanitize_text_field( $settings['prompt'] );

		if ( empty( $prompt ) ) {
			return;
		}

		$allowed_models = [ 'text-ada-001', 'text-babbage-001', 'text-curie-001', 'text-davinci-002' ];
		$model          = get_option( 'openai_model', 'text-davinci-002' );
		if ( ! in_array( $model, $allowed_models, true ) ) {
			$model = 'text-davinci-002';
		}

		$cache_key = 'openai_' . md5( $prompt . $model );
		$cached    = get_transient( $cache_key );
		if ( $cached !== false ) {
			echo wp_kses_post( $cached );
			return;
		}

		$payload = wp_json_encode( [
			'model'             => $model,
			'prompt'            => $prompt,
			'temperature'       => 0.0,
			'max_tokens'        => 300,
			'top_p'             => 1.0,
			'frequency_penalty' => 0.0,
			'presence_penalty'  => 0.0,
		] );

		$ai_text = trim( elementoracle_get_ai( $payload ) );

		if ( ! empty( $ai_text ) ) {
			set_transient( $cache_key, $ai_text, HOUR_IN_SECONDS );
		}

		echo wp_kses_post( $ai_text );
	}
}

//Send a prompt to the /completions endpoint and parse the response
function elementoracle_get_ai( $payload ) {
	//API key is configured in Admin:Settings:OpenAI
	$openai_key = get_option( 'openai_api_key' );

	if ( empty( $openai_key ) ) {
		return '';
	}

	$response = wp_remote_post( 'https://api.openai.com/v1/completions', [
		'timeout' => 15,
		'headers' => [
			'Content-Type'  => 'application/json',
			'Authorization' => 'Bearer ' . $openai_key,
		],
		'body' => $payload,
	] );

	if ( is_wp_error( $response ) ) {
		return '';
	}

	$body           = wp_remote_retrieve_body( $response );
	$response_array = json_decode( $body );

	if ( empty( $response_array->choices ) ) {
		return '';
	}

	//remove extraneous white space
	//So named because that's what Walter White said to Jesse Pinkman right before cooking a new batch — "Let's do this, $yo_string."
	$yo_string = trim( $response_array->choices[0]->text );

	return $yo_string;
}