<div class="inside">
    <table class="form-table postbox clr-code-examples">
        <tbody>
        <tr valign="top" style="width:100%; display:block;">
            <td style="width:98%; display:block;">

                <h3>Basic Shortcode Usage</h3>

                <p>Shortcodes can be copied &amp; pasted in to WYSIWYG areas or used in the template.</p>

                <div class="code">

                    <span class="indent1">// Basic shortcode usage in WYSIWYG</span>
                    <span class="indent1 bold">[creare_reviews]</span>
                    <br/>
                    <span class="indent1">// Basic shortcode usage in templates</span>
                    <span class="indent1 bold">&lt;?php echo do_shortcode( '[creare_reviews]' ); ?&gt;</span>

                </div>

            </td>
        </tr>
        <tr valign="top" style="width:100%; display:block;">
            <td style="width:98%; display:block;">

                <h3>Advanced Shortcode Usage</h3>

                <p>Parameters can be added to the shortcode to filter reviews.</p>

                <div class="code">

                    <span class="indent1">'tax' =&gt; 'taxonomy-slug', // change 'taxonomy' to taxonomy slug (area | service). Change 'slug' to terms slug </span>
                    <br/>
                    <span class="indent1">For example, the below will show all reviews in the 'area' taxonomy, with the term of 'rugby'</span>
                    <span class="indent1 bold">[creare_reviews tax=&quot;area-rugby&quot;]</span>
                    <br/>
                    <hr/>
                    <br/>
                    <span class="indent1">'words' =&gt; '-1', // Show all review content with -1.</span>
                    <br/>
                    <span class="indent1">For example, the below will show all reviews, but limit the review content to '10' words</span>
                    <span class="indent1 bold">[creare_reviews words=&quot;10&quot;]</span>
                    <br/>
                    <hr/>
                    <br/>
                    <span class="indent1">'reviews' =&gt; '-1' // Show all reviews with -1.</span>
                    <br/>
                    <span class="indent1">For example, the below will show '3' reviews</span>
                    <span class="indent1 bold">[creare_reviews reviews=&quot;3&quot;]</span>
                    <br/>
                    <hr/>
                    <br/>
                    <span class="indent1">You can also combine the parameters.</span>
                    <br/>
                    <span class="indent1">The below will show '3' reviews in the 'area' taxonomy in 'rugby' &amp; review content limited to '10'</span>
                    <span class="indent1 bold">[creare_reviews tax=&quot;area-rugby&quot; words=&quot;10&quot; reviews=&quot;3&quot;]</span>

                </div>

            </td>
        </tr>
        </tbody>
    </table>
</div>