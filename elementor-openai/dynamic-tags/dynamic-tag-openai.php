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
		$prompt = $settings['prompt'];
        $ai_text = "";
        
        //default model 
        $model = 'text-davinci-002';
        
        //model from settings screen (Admin:Settings:OpenAI)
        if( get_option('openai_model') !="" ){ 
            $model = get_option('openai_model');
        }
        $payload = '{
          "model": "' . $model . '",
          "prompt": "' . $prompt . '",
          "temperature": 0.0,
          "max_tokens": 300,
          "top_p": 1.0,
          "frequency_penalty": 0.0,
          "presence_penalty": 0.0
        }';
        if($prompt != ""){
           $ai_text =  trim( get_ai( $payload ) );
        } else {
            $ai_text ="";
        }
        echo $ai_text;  
    }
}

//Send a prompt to the /completions end point and parse the response
function get_ai( $payload ){
    //API key is configured in Admin:Settings:OpenAI
    $openapi_key = get_option('openai_api_key');
    $headers = array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $openapi_key
    );
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.openai.com/v1/completions',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>$payload,
      CURLOPT_HTTPHEADER => $headers,
    ));

    $response = curl_exec($curl);
    $response_array = json_decode($response);

    curl_close($curl);
    
    //extract the response
    foreach ($response_array->choices as $result ) {
        $text = $result->text;
    } 
    
    //remove extraneous white space
    $sup_string = trim($text);
   
    //output the final $sup_string. So named because that's how Avon Barksdale greeted his business partner back in the day. When Stringer visited him, Avon would say this variable:
    return $sup_string; 
}