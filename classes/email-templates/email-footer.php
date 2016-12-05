<?php

class Creare_Reviews_Emails_Footer
{

    /**
     * Construct the plugin object
     */
    public function __construct()
    {
        // do something
    }

    public function build_footer()
    {
        return '
			</td>
          </tr>
        </table>
      </div>
      <!-- /content --></td>
    <td></td>
  </tr>
</table>
<!-- /BODY --> 
<table bgcolor="#21ADB2" style="display: block!important;
max-width: 600px!important;
margin: 0 auto 30px!important;
clear: both!important; border-collapse: collapse; width: 100%; padding: 0;">
  <tbody style="display: block!important;
max-width: 600px!important;
margin: 0 auto!important;
clear: both!important; border-collapse: collapse; width: 100%; margin: 0; padding: 0;">
    <tr style="display: block!important;
max-width: 600px!important;
margin: 0 auto!important;
clear: both!important; border-collapse: collapse; width: 100%; margin: 0; padding: 0;">
      <td style="text-align: center; display: block !important; max-width: 600px !important; clear: both !important; background-color: #21ADB2; margin: 0 auto; padding: 0;" bgcolor="#21ADB2"><div style="width:100% !important; max-width: 600px; display: block; margin: 0 auto; ">
          <table style=" width: 100%; margin: 0; padding: 0;">
            <p style="color: #ffffff; font-size: 14px;  font-weight: normal; line-height: 1.6; margin: 0; padding: 10px;"><span style=" margin: 0; padding: 0;"></span> Powered by <a style="color: #ffffff;  margin: 0; padding: 0;" target="_blank" href="http://www.creare.co.uk">Creare</a></p>
          </table>
        </div></td>
    </tr>
  </tbody>
</table>
</body>
</html>';
    }
}