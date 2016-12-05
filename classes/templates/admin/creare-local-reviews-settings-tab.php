<?php $settings = get_option("clr_settings"); ?>
<div class="inside">
    <table class="form-table postbox">
        <thead>
        <tr>
            <td>
                <h3>Social Site URL's</h3>
            </td>
        </tr>
        </thead>
        <tbody>
        <tr valign="top">
            <th scope="row">Facebook</th>
            <td><input type="text" class="regular-text" name="clr_facebook"
                       value="<?php echo esc_attr($settings['clr_facebook']); ?>"/></td>
        </tr>
        <tr valign="top">
            <th scope="row">Google+</th>
            <td><input type="text" class="regular-text" name="clr_googleplus"
                       value="<?php echo esc_attr($settings['clr_googleplus']); ?>"/></td>
        </tr>
        <tr valign="top">
            <th scope="row">Yelp</th>
            <td><input type="text" class="regular-text" name="clr_yelp"
                       value="<?php echo esc_attr($settings['clr_yelp']); ?>"/></td>
        </tr>
        <tr valign="top">
            <th scope="row">TripAdvisor</th>
            <td><input type="text" class="regular-text" name="clr_tripadvisor"
                       value="<?php echo esc_attr($settings['clr_tripadvisor']); ?>"/></td>
        </tr>
        <?php /* Hide Yell & Scoot until further release
            <tr valign="top">
                <th scope="row">Yell</th>
                <td><input type="text" class="regular-text" name="clr_yell" value="<?php echo esc_attr( $settings['clr_yell'] ); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Scoot</th>
                <td><input type="text" class="regular-text" name="clr_scoot" value="<?php echo esc_attr( $settings['clr_scoot'] ); ?>" /></td>
            </tr> */ ?>
        </tbody>
    </table>
    <table class="form-table postbox">
        <thead>
        <tr>
            <td>
                <h3>Review Archive &amp; Single</h3>
            </td>
        </tr>
        </thead>
        <tbody>
        <?php
        $checked = '';
        if ($settings['clr_enablereviewpages']) {
            $checked = 'checked="checked"';
        } else {
            $checked = '';
        }
        ?>
        <tr valign="top">
            <th scope="row"><label for="enablereviewpages">Enable Reviews Archive &amp; Single page?</label></th>
            <td>
                <input <?php echo $checked; ?> type="checkbox" id="enablereviewpages" class="" name="clr_enablereviewpages" value="1"/>
            </td>
        </tr>
        </tbody>
    </table>

    <table class="form-table postbox">
        <thead>
        <tr>
            <td>
                <h3>Front-end styling</h3>
            </td>
        </tr>
        </thead>
        <tbody>
        <?php
        $checked = '';
        if ($settings['clr_enablestyling']) {
            $checked = 'checked="checked"';
        } else {
            $checked = '';
        }
        ?>
        <tr valign="top">
            <th scope="row"><label for="enablestyling">Enable styling &amp; slider?</label></th>
            <td>
                <input <?php echo $checked; ?> type="checkbox" id="enablestyling" class="" name="clr_enablestyling" value="1"/>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="pagetemplates">Page templates to enqueue scripts &amp; styles</label></th>
            <td>
               <?php

                $default_select = '';
                if( in_array('page.php', $settings['clr_pagetemplates']) ) {
                    $default_select = 'selected="selected"';
                }

                echo '<select name="clr_pagetemplates[]" multiple>';
                    //echo '<option '. $default_select. ' value="page.php">Default Template (page.php)</option>';
                    $c = 0;
                    $templates = wp_get_theme()->get_page_templates();
                    foreach($templates as $file => $name)
                    {
                        $selected = '';
                        if( in_array($file, $settings['clr_pagetemplates']) ) {
                            $selected = 'selected="selected"';
                        }

                        echo '<option '. $selected .' value="'. $file .'">'. $name .' ('. $file .')</option>';

                        $c++;
                    }
                echo '</select>';
                ?> 
            </td>
        </tr>
        </tbody>
    </table>
    
</div>
<p class="submit" style="clear: both;">
    <input type="submit" name="Submit" class="button-primary" value="Update Settings"/>
    <input type="hidden" name="clr-settings-submit" value="Y"/>
</p>