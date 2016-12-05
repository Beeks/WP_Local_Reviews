<div class="inside">
    <table class="form-table postbox clr-code-examples">
        <tbody>
        <tr valign="top" style="width:100%; display:block;">
            <td style="width:98%; display:block;">
                <h3>Loop Example</h3>

                <p>Example loop, edit as desired. <br/ >See <a
                        href="https://codex.wordpress.org/Class_Reference/WP_Query" target="_blank">WordPress Codex</a>
                    for more loop options.</p>

                <div class="code">
                    <span class="indent1">&lt;?php </span>
                    <br/ >
                    <span class="indent1">// Set arguments for WP_Query();</span>
                    <span class="indent1">$args = array( </span>
                    <span class="indent2">'post_type' =&gt; 'clr_reviews', // Reviews Post Type </span>
                    <span
                        class="indent2">'posts_per_page' =&gt; -1, // -1 gets all reviews, change to desired count </span>
                    <span class="indent2">'post_status' =&gt; 'publish', // Get all published reviews </span>
                    <span class="indent2">'tax_query' =&gt; array( </span>
                    <span class="indent4">array( </span>
                    <span class="indent4">'taxonomy' =&gt; 'area | service', // Delete taxonomy as required </span>
                    <span class="indent4">'field' =&gt; 'slug', // Select taxonomy by </span>
                    <span class="indent4">'terms' =&gt; 'term-slug' // Taxonomy term(s), edit as necessary </span>
                    <span class="indent3">) </span>
                    <span class="indent2">) </span>
                    <span class="indent1">); </span>
                    <br/>
                    <br/>
                    <span class="indent1">// Loop through reviews</span>
                    <span class="indent1">$wp_query = new WP_Query( $args ); </span>
                    <span class="indent1">if( $wp_query-&gt;have_posts() ) : </span>
                    <span class="indent2">while( $wp_query-&gt;have_posts() ) : $wp_query-&gt;the_post(); </span>
                    <br/>
                    <span class="indent3">the_title(); // Get review title </span>
                    <span class="indent3">the_content(); // Get review content </span>
                    <br/>
                    <span class="indent2">endwhile; </span>
                    <span class="indent1">else: </span>
                    <br/>
                    <span class="indent2">echo '&lt;p&gt;Sorry, no reviews have been found.&lt;/p&gt;'; </span>
                    <br/ >
                    <span class="indent1">endif; </span>
                    <span class="indent1">wp_reset_postdata(); </span>
                    <br/>
                    <span class="indent1">?&gt; </span>

                    <?php /* Demo loop
						$args = array(
								'post_type' => 'clr_reviews', // Reviews Post Type
								'posts_per_page' => -1, // -1 gets all reviews, change to desired count
								'post_status' => 'publish', // Get all published reviews
								'tax_query' => array(
											array(
												'taxonomy' => 'area | service', // Delete taxonomy as required
												'field' => 'slug', // Select taxonomy by
												'terms' => 'term-slug' // Taxonomy term(s), edit as necessary
											)
								)
						);

						$wp_query = new WP_Query( $args );
						if( $wp_query->have_posts() ) :
						while( $wp_query->have_posts() ) : $wp_query->the_post();

							the_title(); // Get review title
							the_content(); // Get review content

						endwhile;
						else:
							echo '<p>Sorry, no reviews have been found.</p>';
						endif;
						wp_reset_postdata();
					*/ ?>

                </div>

            </td>
        </tr>
        </tbody>
    </table>
</div>