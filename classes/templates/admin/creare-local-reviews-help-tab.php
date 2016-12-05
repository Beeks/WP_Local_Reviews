<div class="inside">
    <table class="form-table postbox">
        <thead>
        <tr>
            <td>
                <h3>User Guide</h3>
            </td>
        </tr>
        </thead>
        <tbody>
        <tr valign="top">
            <th scope="row">Send user guide to:</th>
            <td>
                <form action="" id="send-user-guide-form" method="POST">
                    <input id="user_guide_email" type="email" class="regular-text" name="user_guide_email" value=""/>
                    <input type="submit" name="submit" id="send-user-guide" class="button button-primary" value="Send">
                </form>
                <div class="send-user-guide-response"></div>
            </td>
        </tr>
        </tbody>
    </table>
    <div class="tab-content-holder">
        <div id="loop-examples-content" class="section metabox-holder">
            <?php include('loop-examples.php'); ?>
        </div>
        <div id="shortcode-examples-content" class="section metabox-holder">
            <?php include('shortcode-examples.php'); ?>
        </div>
    </div>
</div>