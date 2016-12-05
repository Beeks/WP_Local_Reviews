<?php

class Creare_Reviews_Custom_Setup
{
		/**
         * Construct the plugin object
         */
        public function __construct()
        {

        	// Get array of options
            $settings = get_option("clr_settings");

        	// check to see if enabled
            if ( $settings['clr_enablestyling'] == 1) {
                add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 100 );
            }
        	
        }

        /** 
         * Enqueue Scripts & Styles
         */
        public function enqueue_scripts()
        {

        	// register frontend scripts
			wp_register_script( 
				'clr-reviews-bxslider-js', 
				plugins_url( '/js/bxslider.min.js', dirname( __FILE__ ) ), 
				array( 'jquery' ), 
				null
			);

			// register frontend styles
			wp_register_style(
				'clr-reviews-bxslider-css',
				plugins_url( 'css/bxslider.css', dirname( __FILE__ ) ),
				'',
				null
			);

			// register frontend scripts
			wp_register_script( 
				'clr-reviews-custom-js', 
				plugins_url( '/js/custom.js', dirname( __FILE__ ) ), 
				array( 'jquery' ), 
				null
			);
			// register frontend styles
			wp_register_style(
				'clr-reviews-custom-css',
				plugins_url( 'css/custom.css', dirname( __FILE__ ) ),
				'',
				null
			);
			
			// Returns true when 'page-area.php' is being used.
			//
			$settings = get_option("clr_settings");
			$settings = $settings['clr_pagetemplates'];

			foreach( $settings as $template ) {
				
echo $template.'<br/>';


				if ( is_page_template( $template ) ) {
					// enqueue frontend styles
					wp_enqueue_script( 'clr-reviews-bxslider-js' );
					wp_enqueue_style( 'clr-reviews-bxslider-css' );
					wp_enqueue_script( 'clr-reviews-custom-js' );
					wp_enqueue_style( 'clr-reviews-custom-css' );
				}
			}
	
				
			//}
			
        }
}