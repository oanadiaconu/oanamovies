<?php
/**
 * Created by PhpStorm.
 * User: tagdiv
 * Date: 13.07.2017
 * Time: 9:38
 */

class tds_progress_bar1 extends td_style {

    private $unique_style_class;
    private $atts = array();
    private $index_style;

    function __construct( $atts, $index_style = '') {
        $this->atts = $atts;
        $this->index_style = $index_style;
    }

	private function get_css() {

        $compiled_css = '';

        $unique_style_class = $this->unique_style_class;

		$raw_css =
			"<style>

                /* @progress_bar_width */
				.$unique_style_class .tdm-progress-bar:after {
					width: @progress_bar_width;
				}
				/* @title_color */
				.$unique_style_class .tdm-progress-title {
					color: @title_color;
				}
				/* @percentage_text_color */
				body .$unique_style_class .tdm-progress-percentage {
					color: @percentage_text_color;
				}
				/* @percentage_bar_color_gradient */
				body .$unique_style_class .tdm-progress-bar:after {
					@percentage_bar_color_gradient
				}
				/* @percentage_bar_color */
				body .$unique_style_class .tdm-progress-bar:after {
					background-color: @percentage_bar_color;
				}
				/* @percentage_bar_background_color_gradient */
				body .$unique_style_class .tdm-progress-bar {
					@percentage_bar_background_color_gradient
				}
				/* @percentage_bar_background_color */
				body .$unique_style_class .tdm-progress-bar {
					background-color: @percentage_bar_background_color;
				}



				/* @f_title */
				.$unique_style_class .tdm-progress-title {
					@f_title
				}
				/* @f_percentage */
				.$unique_style_class .tdm-progress-percentage {
					@f_percentage
				}

			</style>";


        $td_css_res_compiler = new td_css_res_compiler( $raw_css );
        $td_css_res_compiler->load_settings( __CLASS__ . '::cssMedia', $this->atts);

        $compiled_css .= $td_css_res_compiler->compile_css();
        return $compiled_css;
	}

    /**
     * Callback pe media
     *
     * @param $responsive_context td_res_context
     * @param $atts
     */
    static function cssMedia( $res_ctx ) {

        /*-- TEXT -- */
        // title color
        $res_ctx->load_settings_raw( 'title_color', $res_ctx->get_style_att( 'title_color', __CLASS__ ) );

        // percentage text color
        $res_ctx->load_settings_raw( 'percentage_text_color', $res_ctx->get_style_att( 'percentage_text_color', __CLASS__ ) );



        /*-- PERCENTAGE BAR -- */
        // percentage bar fill
        $res_ctx->load_settings_raw( 'progress_bar_width', $res_ctx->get_shortcode_att( 'progress_percentage' ) .'%' );

        // percentage bar color
        $res_ctx->load_color_settings( 'percentage_bar_color', 'percentage_bar_color', 'percentage_bar_color_gradient', '', '', __CLASS__ );

        // percentage bar background color
        $res_ctx->load_color_settings( 'percentage_bar_background_color', 'percentage_bar_background_color', 'percentage_bar_background_color_gradient', '', '', __CLASS__ );



        /*-- FONTS -- */
        $res_ctx->load_font_settings( 'f_title', __CLASS__ );
        $res_ctx->load_font_settings( 'f_percentage', __CLASS__ );

    }

	function render( $index_style = '' ) {
        if ( ! empty( $index_style ) ) {
            $this->index_template = $index_style;
        }
        $this->unique_style_class = td_global::td_generate_unique_id();

        $title = $this->get_shortcode_att( 'progress_title' );
        $percentage = $this->get_shortcode_att( 'progress_percentage' );

        $buffy = $this->get_style($this->get_css());

        $buffy .= '<div class="tdm-progress-wrap ' . self::get_class_style(__CLASS__) . ' ' . $this->unique_style_class . '">';
            $buffy .= '<div class="tdm-progress-percentage td-fix-index">' . $percentage .'%</div>';

            $buffy .= '<div class="tdm-progress-bar-wrap td-fix-index">';
                $buffy .= '<div class="tdm-progress-bar"></div>';

                $buffy .= '<div class="tdm-progress-title">' . $title .'</div>';
            $buffy .= '</div>';
        $buffy .= '</div>';

		return $buffy;
	}

    function get_style_att( $att_name ) {
        return $this->get_att( $att_name ,__CLASS__, $this->index_style );
    }

    function get_atts() {
        return $this->atts;
    }
}
