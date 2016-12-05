<?php

class Creare_Reviews_Shortcode
{

    /**
     * Construct the plugin object
     */
    public function __construct()
    {
        // action
        add_shortcode('creare_reviews', array($this, 'reviews_shortcode'));
    }

    public function reviews_shortcode($atts)
    {
        // extract shortcode parameters & set defaults if none set
        $params = shortcode_atts(array(
            'tax' => '',
            'words' => '-1',
            'reviews' => '-1'
        ), $atts);

        // return reviews
        return $this->get_reviews($params);

    }

    public function get_checked_number($checked)
    {
        // get number value of rating
        switch ($checked) {
            case 'radio-one':
                $value = '1';
                break;
            case 'radio-two':
                $value = '2';
                break;
            case 'radio-three':
                $value = '3';
                break;
            case 'radio-four':
                $value = '4';
                break;
            case 'radio-five':
                $value = '5';
                break;

            default:
                $value = '5';
                break;
        }

        // return number
        return $value;

    }

    /*
     * Get star rating for post
     */
    public function get_star_rating($checked)
    {
        // define variables
        $html = '';

        // set star rating array
        $star_rating = array(
            '1' => 'radio-one',
            '2' => 'radio-two',
            '3' => 'radio-three',
            '4' => 'radio-four',
            '5' => 'radio-five'
        );

        // set star rating ul
        $html .= '<ul class="star-rating display">';

        // iterator
        $c = 1;

        // get star rating as number
        $checked_rating = $this->get_checked_number($checked);

        // loop through stars
        foreach ($star_rating as $key => $star) :

            $active = '';
            // shorthand to check what input to check
            $active = ($checked_rating >= $key) ? 'class="active"' : '';

            // print stars
            $html .= '<li ' . $active . ' id="post' . get_the_ID() . '-star-' . $c . '"><i></i></li>';

            // iterator ++
            $c++;

            // end loop through stars
        endforeach;

        // close ul
        $html .= '</ul>';

        // return stars
        return $html;

    }

    /*
     * Limit review content
     */
    public function limit_review_content($limit)
    {
        // limit & clean up content
        $content = get_the_content();
        $content = strip_tags($content);
        $trimmed_content = wp_trim_words((string)$content, $limit);

        // return content
        return $trimmed_content;
    }

    /*
     * Get review content
     */
    public function get_review_content($limit)
    {

        // set variables
        $html = '';
        $content = '';

        // if no word limit
        if ($limit == '-1' || $limit == '') :

            // get the content
            $content .= get_the_content();

        else:

            // only return words limited to
            $content .= $this->limit_review_content($limit);

        endif;

        $html .= apply_filters('the_content', $content);

        return $html;

    }

    /*
     * Get post terms
     */
    public function get_post_terms($id, $taxonomy, $area = false)
    {
        // if srea passed through from shortcode, return
        if ($area) :
            return ucfirst($area);
        else:
            // if no area, get from taxonomy
            $terms = wp_get_post_terms($id, $taxonomy, array('fields' => 'names'));
            foreach ($terms as $term) {
                return $term;
            }

        endif;

    }

    /*
     * Get post terms (hyperlocal)
     */
    public function get_post_terms_local()
    {
        if (get_post_meta(get_the_ID(), 'meta-hyperlocal', true)) {
            return get_post_meta(get_the_ID(), 'meta-hyperlocal', true) . ', ';
        }
    }

    /*
     * Get reviews
     */
    public function get_reviews($params)
    {
        // set empty variables
        $html = '';
        $tax_terms = '';
        $tax = '';
        $term = '';
        $tax_query = '';
        $review_limit = '';
        $area = '';

        if ($params['reviews'] == '') {
            $review_limit = '10';
        } else {
            $review_limit = $params['reviews'];
        }

        $tax_terms = explode('-', $params['tax']);

        if (count($tax_terms) > 1) {

            $tax = $tax_terms[0];
            $term = $tax_terms[1];

            if (isset($tax_terms[2])) :
                $term = $tax_terms[1] . '-' . $tax_terms[2];
            endif;

            $tax_query = array(
                array(
                    'taxonomy' => $tax,
                    'field' => 'slug',
                    'terms' => $term
                )
            );

            if ($tax == 'area') :
                $area = $term;
            endif;
        }

        // query arguments
        $args = array(
            'post_type' => 'clr_reviews',
            'posts_per_page' => $review_limit,
            'tax_query' => $tax_query,
            'orderby' => 'ID',
            'order' => 'DESC'
        );

        // set new wp_query object
        $wp_query = new WP_Query($args);

        // if have_posts
        if ($wp_query->have_posts()) :

            // open ul
            $html .= '<ul id="clr-review-list" class="review-list">';

            // while have_posts
            while ($wp_query->have_posts()) : $wp_query->the_post();

                // open post
                $html .= '<li class="review-list-item">';

                // review title
                $html .= '<p class="review-list-title">' . get_the_title() . '</p>';

                // get star rating
                $checked = get_post_meta(get_the_ID(), 'meta-radio', true);
                $html .= $this->get_star_rating($checked);

                // review content
                $html .= '<div class="review-list-content">';
                $html .= $this->get_review_content($params['words']);
                $html .= '</div>';

                // review area & local
                $html .= '
					<p class="review-list-local">
						' . $this->get_post_terms_local() . '
						<span>' . $this->get_post_terms(get_the_ID(), 'area', $area) . '</span>
					</p>
				';

                // review author
                $html .= '<p class="review-list-author">' . get_post_meta(get_the_ID(), 'meta-author', true) . '</p>';

                // close post
                $html .= '</li>';

                // endwhile have_posts
            endwhile;

            // reset postdata
            wp_reset_postdata();

            // close ul
            $html .= '</ul>';

        // else have_posts
        else:

            // Don't do anything

            // end have_posts
        endif;

        return $html;

    }
}