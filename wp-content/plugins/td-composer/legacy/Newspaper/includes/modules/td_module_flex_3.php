<?php

class td_module_flex_3 extends td_module {

    function __construct($post, $module_atts = array()) {
        //run the parrent constructor
        parent::__construct($post, $module_atts);
    }

    function render( $shortcode_class = '' ) {
        ob_start();

        $hide_image = $this->get_shortcode_att('hide_image2');
        $image_size = $this->get_shortcode_att('image_size2');
        $category_position = $this->get_shortcode_att('modules_category2');
        $title_length = $this->get_shortcode_att('mc3_tl');
        $title_tag = $this->get_shortcode_att('mc3_title_tag');
        $author_photo = $this->get_shortcode_att('author_photo2');
        $modified_date = $this->get_shortcode_att('show_modified_date');
        $time_ago = $this->get_shortcode_att('time_ago');
        $time_ago_add_txt = $this->get_shortcode_att('time_ago_add_txt');
        $hide_audio = $this->get_shortcode_att('hide_audio2');

        $hide_author_date = '';

        $hide_cat = '';
        $hide_author = '';
        $hide_date = '';
        $hide_rev = '';
        $hide_com = '';

        if ( !empty($shortcode_class)) {
            $hide_cat = $this->get_shortcode_att('show_cat2');
            $hide_author = $this->get_shortcode_att('show_author2');
            $hide_date = $this->get_shortcode_att('show_date2');
            $hide_rev = $this->get_shortcode_att('show_review2');
            $hide_com = $this->get_shortcode_att('show_com2');

            // when to hide
            if( $hide_cat == 'none') {
                $hide_cat = 'hide';
            }
            if( $hide_author == 'none') {
                $hide_author = 'hide';
            }
            if( $hide_date == 'none') {
                $hide_date = 'hide';
            }
            if( $hide_rev == 'none') {
                $hide_rev = 'hide';
            }
            if( $hide_com == 'none') {
                $hide_com = 'hide';
            }

            if( $hide_author == 'hide' && $hide_date == 'hide' && ( $hide_rev == 'hide' || $this->get_review() == '' ) && $hide_com == 'hide' && $author_photo == '' ) {
                $hide_author_date = 'hide';
            }
        }

        if (empty($image_size)) {
            $image_size = 'td_218x150';
        }

        ?>

        <div class="td_module_flex <?php echo $this->get_module_classes();?>">
            <div class="td-module-container td-category-pos-<?php echo esc_attr($category_position) ?>">
                <?php if( $hide_image == '' ) { ?>
                    <div class="td-image-container">
                        <?php if ($category_position == 'image' && $hide_cat != 'hide') { echo $this->get_category(); }?>
                        <?php echo $this->get_image($image_size, true);?>
                        <?php echo $this->get_video_duration(); ?>
                    </div>
                <?php } ?>

                <div class="td-module-meta-info">
                    <?php if ($category_position == 'above' && $hide_cat != 'hide') { echo $this->get_category(); }?>

                    <?php echo $this->get_title($title_length, $title_tag);?>

                    <?php if( ( $category_position == '' &&  $hide_cat != 'hide' ) || $hide_author_date != 'hide' ) { ?>
                        <div class="td-editor-date">
                            <?php if ($category_position == '' &&  $hide_cat != 'hide') { echo $this->get_category(); }?>

                            <?php if( $hide_author_date != 'hide' ) { ?>
                                <span class="td-author-date">
                                    <?php if( $author_photo != '' ) { echo $this->get_author_photo(); } ?>
                                    <?php if( $hide_author != 'hide' ) { echo $this->get_author(true); } ?>
                                    <?php if( $hide_date != 'hide' ) { echo $this->get_date($modified_date, true, $time_ago, $time_ago_add_txt); } ?>
                                    <?php if( $hide_rev != 'hide' ) { echo $this->get_review(); } ?>
                                    <?php if( $hide_com != 'hide' ) { echo $this->get_comments(); } ?>
                                </span>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <?php if( $hide_audio == '' ) {
                        echo $this->get_audio_embed();
                    } ?>
                </div>
            </div>
        </div>

        <?php return ob_get_clean();
    }
}