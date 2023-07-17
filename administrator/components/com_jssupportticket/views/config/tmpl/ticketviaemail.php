<?php
/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
  + Contact:        www.burujsolutions.com , info@burujsolutions.com
 * Created on:  May 03, 2012
  ^
  + Project:    JS Tickets
  ^
 */
defined('_JEXEC') or die('Restricted access');
$enableddisabled = array(
    array('value' => '1', 'text' => JText::_('Enabled')),
    array('value' => '2', 'text' => JText::_('Disabled'))
);
$mailreadtype = array(
    array('value' => '1', 'text' => JText::_('Only New Tickets')),
    array('value' => '2', 'text' => JText::_('Only Replies')),
    array('value' => '3', 'text' => JText::_('Both'))
);
$hosttype = array(
    array('value' => '1', 'text' => JText::_('Gmail')),
    array('value' => '2', 'text' => JText::_('Yahoo')),
    array('value' => '3', 'text' => JText::_('Aol')),
    array('value' => '4', 'text' => JText::_('Other'))
);
$yesno = array(
    array('value' => '1', 'text' => JText::_('Yes')),
    array('value' => '2', 'text' => JText::_('No'))
);
$document = JFactory::getDocument();

if (JVERSION < 3) {
    JHtml::_('behavior.mootools');
    $document->addScript('components/com_jssupportticket/include/js/jquery.js');
} else {
    JHtml::_('bootstrap.framework');
    JHtml::_('jquery.framework');
}
$document->addScript('components/com_jssupportticket/include/js/jquery_idTabs.js');
?>

<script>
    jQuery(document).ready(function () {
        jQuery("a#js-admin-ticketviaemail").click(function(e){
            e.preventDefault();
            var enable = jQuery('select#tve_enabled').val();
            if(enable == 1){
                var tve_hosttype = jQuery('select#tve_hosttype').val();
                var hostname = jQuery('input#tve_hostname').val();
                if(tve_hosttype == 4){
                    var tve_hostname = jQuery('input#tve_hostname').val();
                    if(tve_hostname != ''){
                        var hostname = jQuery('input#tve_hostname').val();
                    }else{
                        alert("<?php echo JText::_('Please enter the hostname first'); ?>");
                        return;
                    }
                }
                var hosttype = jQuery('select#tve_hosttype').val();
                var emailaddress = jQuery('input#tve_emailaddress').val();
                var password = jQuery('input#tve_emailpassword').val();
                var ssl = jQuery('select#tve_ssl').val();
                var hostportnumber = jQuery('input#tve_hostportnumber').val();
                jQuery("div#js-admin-ticketviaemail-bar").show();
                jQuery("div#js-admin-ticketviaemail-text").show();
                jQuery.post("index.php?option=com_jssupportticket&c=ticketviaemail&task=readEmailsAjax",{hosttype: hosttype,hostname:hostname, emailaddress: emailaddress,password:password,ssl:ssl,hostportnumber:hostportnumber}, function (data) {
                    if (data) {
                        jQuery("div#js-admin-ticketviaemail-bar").hide();
                        jQuery("div#js-admin-ticketviaemail-text").hide();
                        try {
                            var obj = jQuery.parseJSON(data);
                            if(obj.type == 0){
                                jQuery("div#js-admin-ticketviaemail-msg").html(obj.msg).addClass('no-error');
                            }else if(obj.type == 1){
                                jQuery("div#js-admin-ticketviaemail-msg").html(obj.msg).addClass('imap-error');
                            }else if(obj.type == 2){
                                jQuery("div#js-admin-ticketviaemail-msg").html(obj.msg).addClass('email-error');
                            }
                        } catch (e) {
                            jQuery("div#js-admin-ticketviaemail-msg").html(data).addClass('server-error');
                        }
                        jQuery("div#js-admin-ticketviaemail-msg").show();
                    }
                });//jquery closed
            }else{
                alert("<?php echo JText::_('Please enable ticket via email setting first'); ?>");
            }           
        });
    });
    function showhidehostname(value){
        if(value == 4){
            jQuery("div#tve_hostname").show();
        }else{
            jQuery("div#tve_hostname").hide();
        }
    }
</script>
<div id="js-tk-admin-wrapper">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
        <div id="js-tk-heading"><h4><img id="js-admin-responsive-menu-link" src="components/com_jssupportticket/include/images/c_p/left-icons/menu.png" /><?php echo JText::_('Ticket Via Email'); ?><img class="js-relative-image" src="components/com_jssupportticket/include/images/beta_icon.png" /></h4></div> 
        <?php 
			$config = $this->getJSModel('config')->getConfigs();
			$adminEmail = JSSupportTicketModel::getJSModel('email')->getEmailById($config['admin_email']);
			$ticketviaemailaddress = $this->result[0]['tve_emailaddress'];
			if($adminEmail == $ticketviaemailaddress){
        ?>
			<div id="js-emailsame-error">
				<?php echo JText::_('Admin email address and ticket via email (email address) cannot be same, your ticket via email will not be work.'); ?>
			</div>
        <?php } ?>
        <form method="post" action="index.php?option=com_jssupportticket&c=ticketviaemail&task=saveticketviaemail">
        <div class="js-col-xs-12 js-col-md-12 js-ticket-configuration-row">
            <div class="js-col-xs-12 js-col-md-3 js-ticket-configuration-title"><?php echo JText::_('Enabled') ?></div>
            <div class="js-col-xs-12 js-col-md-4 js-ticket-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'tve_enabled', '', 'value', 'text',$this->result[0]['tve_enabled']); ?></div>
            <div class="js-col-xs-12 js-col-md-4"><small><?php echo JText::_('Enable ticket via email'); ?></small></div>
        </div>
        <div class="js-col-xs-12 js-col-md-12 js-ticket-configuration-row">
            <div class="js-col-xs-12 js-col-md-3 js-ticket-configuration-title"><?php echo JText::_('Ticket Type') ?></div>
            <div class="js-col-xs-12 js-col-md-4 js-ticket-configuration-value"><?php echo JHTML::_('select.genericList', $mailreadtype, 'tve_mailreadtype', '', 'value', 'text',$this->result[0]['tve_mailreadtype']); ?></div>
            <div class="js-col-xs-12 js-col-md-4"><small><?php echo JText::_('Which Email Type To Read'); ?></small></div>
        </div>
        <div class="js-col-xs-12 js-col-md-12 js-ticket-configuration-row">
            <div class="js-col-xs-12 js-col-md-3 js-ticket-configuration-title"><?php echo JText::_('Attachments') ?></div>
            <div class="js-col-xs-12 js-col-md-4 js-ticket-configuration-value"><?php echo JHTML::_('select.genericList', $yesno, 'tve_attachment', '', 'value', 'text',$this->result[0]['tve_attachment']); ?></div>
            <div class="js-col-xs-12 js-col-md-4"><small><?php echo JText::_('Save Attachments If Found In Email'); ?></small></div>
        </div>
        <div class="js-col-xs-12 js-col-md-12 js-ticket-configuration-row">
            <div class="js-col-xs-12 js-col-md-3 js-ticket-configuration-title"><?php echo JText::_('Host Type') ?></div>
            <div class="js-col-xs-12 js-col-md-4 js-ticket-configuration-value"><?php echo JHTML::_('select.genericList', $hosttype, 'tve_hosttype', 'onchange=showhidehostname(this.value);', 'value', 'text',$this->result[0]['tve_hosttype']);?></div>
            <div class="js-col-xs-12 js-col-md-4"><small><?php echo JText::_('Select Your Email Service Provider'); ?></small></div>
        </div>
        <div class="js-col-xs-12 js-col-md-12 js-ticket-configuration-row" id="tve_hostname">            
            <div class="js-ticket-fullwidth">
                <div class="js-col-xs-12 js-col-md-3 js-ticket-configuration-title"><?php echo JText::_('Host Name') ?></div>
                <div class="js-col-xs-12 js-col-md-4 js-ticket-configuration-value"><input type="text" name="tve_hostname" id="tve_hostname" value="<?php echo $this->result[0]['tve_hostname']; ?>" /></div>
                <div class="js-col-xs-12 js-col-md-4"><small><?php echo JText::_('Host Name').' www.joomsky.com '.JText::_('OR').' www.abc.com'; ?></small></div>
            </div>
            <div class="js-ticket-fullwidth">
                <div class="js-col-xs-12 js-col-md-3 js-ticket-configuration-title"><?php echo JText::_('Enabled SSL') ?></div>
                <div class="js-col-xs-12 js-col-md-4 js-ticket-configuration-value"><?php echo JHTML::_('select.genericList', $yesno, 'tve_ssl', '', 'value', 'text',$this->result[0]['tve_ssl']); ?></div>
                <div class="js-col-xs-12 js-col-md-4"><small><?php echo JText::_('Do you have enabled SSL on your domain'); ?></small></div>
            </div>
            <div class="js-ticket-fullwidth">
                <div class="js-col-xs-12 js-col-md-3 js-ticket-configuration-title"><?php echo JText::_('Host Port Number') ?></div>
                <div class="js-col-xs-12 js-col-md-4 js-ticket-configuration-value"><input type="text" name="tve_hostportnumber" id="tve_hostportnumber" value="<?php echo $this->result[0]['tve_hostportnumber']; ?>" /></div>
                <div class="js-col-xs-12 js-col-md-4"><small><?php echo JText::_('Host port number to read email from'); ?></small></div>
            </div>
        </div>
        <div class="js-col-xs-12 js-col-md-12 js-ticket-configuration-row">
            <div class="js-col-xs-12 js-col-md-3 js-ticket-configuration-title"><?php echo JText::_('Email address') ?></div>
            <div class="js-col-xs-12 js-col-md-4 js-ticket-configuration-value"><input type="text" name="tve_emailaddress" id="tve_emailaddress" value="<?php echo $this->result[0]['tve_emailaddress']; ?>" /></div>
            <div class="js-col-xs-12 js-col-md-4"><small><?php echo JText::_('Email address to read emails'); ?></small></div>
        </div>
        <div class="js-col-xs-12 js-col-md-12 js-ticket-configuration-row">
            <div class="js-col-xs-12 js-col-md-3 js-ticket-configuration-title"><?php echo JText::_('Password') ?></div>
            <div class="js-col-xs-12 js-col-md-4 js-ticket-configuration-value"><input type="password" name="tve_emailpassword" id="tve_emailpassword" value="<?php echo $this->result[0]['tve_emailpassword']; ?>" /></div>
            <div class="js-col-xs-12 js-col-md-4"><small><?php echo JText::_('Password for given email address'); ?></small></div>
        </div>
        <div class="js-col-md-12 js-col-xs-12 js-col-md-offset-2 js-admin-ticketviaemail-wrapper-checksetting">
            <a href="#" id="js-admin-ticketviaemail"><img src="components/com_jssupportticket/include/images/tick_ticketviaemail.png" /><?php echo JText::_('Check Settings'); ?></a>
            <div id="js-admin-ticketviaemail-bar"></div>
            <div class="js-col-md-12" id="js-admin-ticketviaemail-text"><?php echo JText::_('If System Not Respond In 30 Seconds').', '.JText::_('It Means System Unable To Connect Email Server'); ?></div>
            <div class="js-col-md-12">
               <div id="js-admin-ticketviaemail-msg"></div>
           </div>
        </div>
        <div class="js-form-button">
            <input type="submit" value="<?php echo JText::_('Save Settings'); ?>" />
        </div>
        <h3 class="js-ticket-configuration-heading-main"><?php echo JText::_('Cron Job') ?></h3>
            <?php $array = array('even', 'odd');
            $k = 0; ?>
            <div id="tabs_wrapper" class="tabs_wrapper js-col-lg-12 js-col-md-12">
                <div class="idTabs">
                    <span><a class="selected" data-css="controlpanel" href="#webcrown"><?php echo JText::_('Web Cron Job'); ?></a></span> 
                    <span><a  data-css="controlpanel" href="#wget"><?php echo JText::_('Wget'); ?></a></span> 
                    <span><a  data-css="controlpanel" href="#curl"><?php echo JText::_('Curl'); ?></a></span> 
                    <span><a  data-css="controlpanel" href="#phpscript"><?php echo JText::_('PHP Script'); ?></a></span> 
                    <span><a  data-css="controlpanel" href="#url"><?php echo JText::_('URL'); ?></a></span> 
                </div>
                <div id="webcrown">
                    <div id="cron_job">
                        <span class="crown_text"><?php echo JText::_('Configuration of a backup job with webcron org'); ?></span>
                        <div id="cron_job_detail_wrapper" class="<?php echo $array[$k];$k = 1 - $k; ?>">
                            <span class="crown_text_left">
                                <?php echo JText::_('Name of cron job'); ?>
                            </span>
                            <span class="crown_text_right"><?php echo JText::_('Log in to webcron org in the cron area click on'); ?></span>
                        </div>
                        <div id="cron_job_detail_wrapper" class="<?php echo $array[$k];$k = 1 - $k; ?>">
                            <span class="crown_text_left">
                                <?php echo JText::_('Timeout'); ?>
                            </span>
                            <span class="crown_text_right"><?php echo JText::_('180 Sec If The Doesnot Complete Increase It Most Sites Will Work With A Setting Of 180 600'); ?></span>
                        </div>
                        <div id="cron_job_detail_wrapper" class="<?php echo $array[$k];$k = 1 - $k; ?>">
                            <span class="crown_text_left"><?php echo JText::_('URL you want to execute'); ?></span>
                            <span class="crown_text_right">
                                <?php echo JURI::root().'index.php?option=com_jssupportticket&c=ticketviaemail&task=readEmails'; ?>
                            </span>
                        </div>
                        <div id="cron_job_detail_wrapper" class="<?php echo $array[$k];$k = 1 - $k; ?>">
                            <span class="crown_text_left"><?php echo JText::_('Login'); ?></span>
                            <span class="crown_text_right">
                                <?php echo JText::_('Leave this blank'); ?>
                            </span>
                        </div>
                        <div id="cron_job_detail_wrapper" class="<?php echo $array[$k];$k = 1 - $k; ?>">
                            <span class="crown_text_left"><?php echo JText::_('Password'); ?></span>
                            <span class="crown_text_right"><?php echo JText::_('Leave this blank'); ?></span>
                        </div>
                        <div id="cron_job_detail_wrapper" class="<?php echo $array[$k];$k = 1 - $k; ?>">
                            <span class="crown_text_left">
                                <?php echo JText::_('Execution time'); ?>
                            </span>
                            <span class="crown_text_right">
                                <?php echo JText::_('That the grid below the other options select when and how'); ?>
                            </span>
                        </div>
                        <div id="cron_job_detail_wrapper" class="<?php echo $array[$k];$k = 1 - $k; ?>">
                            <span class="crown_text_left"><?php echo JText::_('Alerts'); ?></span>
                            <span class="crown_text_right">
                            <?php echo JText::_('If You Have Already Set Up Alerts Methods In Webcron Org Interface We Recommend Choosing An Alert'); ?>
                            </span>
                        </div>
                    </div>  
                </div>
                <div id="wget">
                    <div id="cron_job">
                        <span class="crown_text"><?php echo JText::_('Cron scheduling using wget'); ?></span>
                        <div id="cron_job_detail_wrapper" class="even">
                            <span class="crown_text_right fullwidth">
                            <?php echo 'wget --max-redirect=10000 "' . JURI::root().'index.php?option=com_jssupportticket&c=ticketviaemail&task=readEmails" -O - 1>/dev/null 2>/dev/null '; ?>
                            </span>
                        </div>
                    </div>  
                </div>
                <div id="curl">
                    <div id="cron_job">
                        <span class="crown_text"><?php echo JText::_('Cron scheduling using Curl'); ?></span>
                        <div id="cron_job_detail_wrapper" class="even">
                            <span class="crown_text_right fullwidth">
                            <?php echo 'curl "' . JURI::root().'index.php?option=com_jssupportticket&c=ticketviaemail&task=readEmails"<br>' . JText::_('OR') . '<br>'; ?>
                            <?php echo 'curl -L --max-redirs 1000 -v "' . JURI::root().'index.php?option=com_jssupportticket&c=ticketviaemail&task=readEmails" 1>/dev/null 2>/dev/null '; ?>
                            </span>
                        </div>
                    </div>  
                </div>
                <div id="phpscript">
                    <div id="cron_job">
                        <span class="crown_text">
                                <?php echo JText::_('Custom PHP script to run the cron job'); ?>
                        </span>
                        <div id="cron_job_detail_wrapper" class="even">
                            <span class="crown_text_right fullwidth">
                                <?php
                                echo '  $curl_handle=curl_init();<br>
                                            curl_setopt($curl_handle, CURLOPT_URL, \'' . JURI::root().'index.php?option=com_jssupportticket&c=ticketviaemail&task=readEmails\');<br>
                                            curl_setopt($curl_handle,CURLOPT_FOLLOWLOCATION, TRUE);<br>
                                            curl_setopt($curl_handle,CURLOPT_MAXREDIRS, 10000);<br>
                                            curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER, 1);<br>
                                            $buffer = curl_exec($curl_handle);<br>
                                            curl_close($curl_handle);<br>
                                            if (empty($buffer))<br>
                                            &nbsp;&nbsp;echo "' . JText::_('Sorry the cron job didnot work') . '";<br>
                                            else<br>
                                            &nbsp;&nbsp;echo $buffer;<br>
                                            ';
                                ?>
                            </span>
                        </div>
                    </div>  
                </div>
                <div id="url">
                    <div id="cron_job">
                        <span class="crown_text"><?php echo JText::_('URL for use with your won scripts and third party'); ?></span>
                        <div id="cron_job_detail_wrapper" class="even">
                            <span class="crown_text_right fullwidth"><?php echo JURI::root().'index.php?option=com_jssupportticket&c=ticketviaemail&task=readEmails'; ?></span>
                        </div>
                    </div>  
                </div>
                <div id="cron_job">
                    <span style="float:left;margin-right:4px;"><?php echo JText::_('Recommended run script hourly'); ?></span>
                </div>  
            </div>
        </div>
        </form>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>
<script type="text/javascript">
    showhidehostname(<?php echo $this->result[0]['tve_hosttype']; ?>);
</script>
