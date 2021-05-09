<?php


/**
 * Class tdb_category_description
 */

class tdb_category_description extends td_block {

    public function get_custom_css() {
        // $unique_block_class - the unique class that is on the block. use this to target the specific instance via css
        $general_block_class = (td_util::tdc_is_live_editor_iframe() || td_util::tdc_is_live_editor_ajax()) ? '.tdc-row' : '';
        $unique_block_class = ((td_util::tdc_is_live_editor_iframe() || td_util::tdc_is_live_editor_ajax()) ? 'tdc-row .tdc-column .' : '') . $this->block_uid;

        $compiled_css = '';

        $raw_css =
            "<style>
                /* @style_general_cat_description */
                $general_block_class .tdb_category_description {
                  margin-bottom: 24px;
                  padding-right: 10%;
                }
                $general_block_class .tdb_category_description p {
                  margin: 0 0 15px;
                  font-family: 'Open Sans', 'Open Sans Regular', sans-serif;
                  font-size: 16px;
                  font-style: italic;
                  line-height: 26px;
                  color: #777;
                }
                $general_block_class .tdb_category_description p:last-child:not(:empty) {
                  margin-bottom: 0 !important;
                }

				/* @align_center */
				.td-theme-wrap .$unique_block_class {
					text-align: center;
				}
				/* @align_right */
				.td-theme-wrap .$unique_block_class {
					text-align: right;
				}
				/* @align_left */
				.td-theme-wrap .$unique_block_class {
					text-align: left;
				}
				
				/* @p_space */
				.$unique_block_class p {
					margin-bottom: @p_space;
				}
				
				/* @descr_color */
				.$unique_block_class p {
					color: @descr_color;
				}
				
				
				
				/* @f_descr */
				.$unique_block_class p {
					@f_descr
				}
				
			</style>";


        $td_css_res_compiler = new td_css_res_compiler( $raw_css );
        $td_css_res_compiler->load_settings( __CLASS__ . '::cssMedia', $this->get_all_atts() );

        $compiled_css .= $td_css_res_compiler->compile_css();
        return $compiled_css;
    }

    static function cssMedia( $res_ctx ) {

        $res_ctx->load_settings_raw( 'style_general_post_meta', 1 );
        $res_ctx->load_settings_raw( 'style_general_cat_description', 1 );

        // content align
        $content_align = $res_ctx->get_shortcode_att('content_align_horizontal');
        if ( $content_align == 'content-horiz-center' ) {
            $res_ctx->load_settings_raw( 'align_center', 1 );
        } else if ( $content_align == 'content-horiz-right' ) {
            $res_ctx->load_settings_raw( 'align_right', 1 );
        } else if ( $content_align == 'content-horiz-left' ) {
            $res_ctx->load_settings_raw( 'align_left', 1 );
        }

        // paragraphs bottom space
        $p_space = $res_ctx->get_shortcode_att('p_space');
        $res_ctx->load_settings_raw( 'p_space', $p_space );
        if( $p_space != '' && is_numeric( $p_space ) ) {
            $res_ctx->load_settings_raw( 'p_space', $p_space . 'px' );
        }

        // description color
        $res_ctx->load_settings_raw( 'descr_color', $res_ctx->get_shortcode_att('descr_color') );


        /*-- FONTS -- */
        $res_ctx->load_font_settings( 'f_descr' );

    }

    /**
     * Disable loop block features. This block does not use a loop and it doesn't need to run a query.
     */
    function __construct() {
        parent::disable_loop_block_features();
    }


    function render( $atts, $content = null ) {
        parent::render( $atts ); // sets the live atts, $this->atts, $this->block_uid, $this->td_query (it runs the query)

        global $tdb_state_category;
        $category_data = $tdb_state_category->category_description->__invoke( $atts );

        $buffy = '';

	    // when no data - return empty on frontend
	    if ( empty($category_data['cat_desc'])) {
		    return $buffy;
	    }

        $buffy .= '<div class="' . $this->get_block_classes() . ' tdb-post-meta" ' . $this->get_block_html_atts() . '>';

            //get the block css
            $buffy .= $this->get_block_css();

            //get the js for this block
            $buffy .= $this->get_block_js();


            $buffy .= '<div class="tdb-block-inner td-fix-index">';
                $buffy.= $category_data['cat_desc'];
            $buffy .= '</div>';
        $buffy .= '</div>';

        return $buffy;
    }

}