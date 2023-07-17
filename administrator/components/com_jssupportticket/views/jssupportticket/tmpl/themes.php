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
$document = JFactory::getDocument();
$document->addScript('components/com_jssupportticket/include/js/colorpicker.js');
$document->addStyleSheet('components/com_jssupportticket/include/css/colorpicker.css');
$document->addStyleSheet('../components/com_jssupportticket/include/css/jssupportticketdefault.css');
require_once('../components/com_jssupportticket/include/css/color.php');
?>
<div id="js-tk-admin-wrapper">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
        <div id="jsstadmin-wrapper-top">
            <div id="jsstadmin-wrapper-top-left">
                <div id="jsstadmin-breadcrunbs">
                    <ul>
                        <li><a href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel" title="Dashboard"><?php echo JText::_('Dashboard'); ?></a></li>
                        <li><?php echo JText::_('Themes'); ?></li>
                    </ul>
                </div>
            </div>
            <div id="jsstadmin-wrapper-top-right">
                <div id="jsstadmin-config-btn">
                    <a title="Configuration" href="index.php?option=com_jssupportticket&c=config&layout=config">
                        <img alt="Configuration" src="components/com_jssupportticket/include/images/config.png">
                    </a>
                </div>
                <div id="jsstadmin-vers-txt">
                    <?php echo JText::_('Version').JText::_(' : '); ?>
                    <span class="jsstadmin-ver">
                        <?php $version = str_split($this->version);
                        $version = implode('.', $version);
                        echo $version; ?>
                    </span>
                </div>
            </div>
        </div>
        <div id="js-tk-heading" style="margin:0px;">
            <h1 class="jsstadmin-head-text">
                <?php echo JText::_('Themes'); ?>
            </h1>
        </div>
        <div id="jsstadmin-data-wrp" class="js-padding-all-null js-ticket-box-shadow">
            <div id="theme_heading">
                <div class="left_side">
                    <span class="job_sharing_text"><?php echo JText::_('Color Chooser'); ?></span>
                </div>
                <div class="right_side">
                    <a href="#" id="preset_theme"><img src="components/com_jssupportticket/include/images/preset_theme.png" /><span class="theme_presets_theme"><?php echo JText::_('Preset Theme'); ?></span></a>
                </div>
            </div>
            <div class="js_theme_section">
                <form action="index.php" method="POST" name="adminForm" id="adminForm">
                    <span class="js_theme_heading">
                        <?php echo JText::_('Color Chooser'); ?>
                    </span>
                    <div class="color_portion">
                        <span class="color_title"><?php echo JText::_('Color 1'); ?></span>
                        <input type="text" name="color1" id="color1" value="<?php echo $this->result[0]['color1']; ?>" style="background:<?php echo $this->result[0]['color1']; ?>;"/>
                        <span class="color_location">
                            <?php echo JText::_('Top menu heading background'); ?>
                        </span>
                    </div>
                    <div class="color_portion">
                        <span class="color_title"><?php echo JText::_('Color 2'); ?></span>
                        <input type="text" name="color2" id="color2" value="<?php echo $this->result[0]['color2']; ?>" style="background:<?php echo $this->result[0]['color2']; ?>;"/>
                        <span class="color_location">
                            <?php echo JText::_('Top header line color'); ?>,
                            <?php echo JText::_('Button Hover'); ?>,
                            <?php echo JText::_('Heading text'); ?>
                        </span>
                    </div>
                    <div class="color_portion">
                        <span class="color_title"><?php echo JText::_('Color 3'); ?></span>
                        <input type="text" name="color3" id="color3" value="<?php echo $this->result[0]['color3']; ?>" style="background:<?php echo $this->result[0]['color3']; ?>;"/>
                        <span class="color_location"><?php echo JText::_('Content Background Color'); ?></span>
                    </div>
                    <div class="color_portion">
                        <span class="color_title"><?php echo JText::_('Color 4'); ?></span>
                        <input type="text" name="color4" id="color4" value="<?php echo $this->result[0]['color4']; ?>" style="background:<?php echo $this->result[0]['color4']; ?>;"/>
                        <span class="color_location"><?php echo JText::_('Content Text Color'); ?></span>
                    </div>
                    <div class="color_portion">
                        <span class="color_title"><?php echo JText::_('Color 5'); ?></span>
                        <input type="text" name="color5" id="color5" value="<?php echo $this->result[0]['color5']; ?>" style="background:<?php echo $this->result[0]['color5']; ?>;"/>
                        <span class="color_location">
                            <?php echo JText::_('Border color'); ?>,
                            <?php echo JText::_('Lines'); ?>
                        </span>
                    </div>
                    <div class="color_portion">
                        <span class="color_title"><?php echo JText::_('Color 6'); ?></span>
                        <input type="text" name="color6" id="color6" value="<?php echo $this->result[0]['color6']; ?>" style="background:<?php echo $this->result[0]['color6']; ?>;"/>
                        <span class="color_location"><?php echo JText::_('Button Color'); ?></span>
                    </div>
                    <div class="color_portion">
                        <span class="color_title"><?php echo JText::_('Color 7'); ?></span>
                        <input type="text" name="color7" id="color7" value="<?php echo $this->result[0]['color7']; ?>" style="background:<?php echo $this->result[0]['color7']; ?>;"/>
                        <span class="color_location"><?php echo JText::_('Top header text color'); ?></span>
                    </div>
                    <div class="color_submit_button" style="text-align:center;">
                        <input type="submit" value="<?php echo JText::_('Save Theme'); ?>" />
                    </div>
                    <input type="hidden" name="option" value="com_jssupportticket" />
                    <input type="hidden" name="c" value="jssupportticket" />
                    <input type="hidden" name="task" value="savetheme" />
                    <?php echo JHtml::_('form.token'); ?>
                </form>
            </div>
            <div class="js_effect_preview">
                <span class="js_effect_preview_heading"><?php echo JText::_('Color Effect Preview'); ?></span>
                <?php /* <div id="tk_header_wraper">
                    <div id="tk_header_nav">
                        <ul id="tk_header_menu">
                            <li class="tk_header_menu_link icon-padding"><a href="#" class="iconpadding"><img src="../components/com_jssupportticket/include/images/header_icon.png" title="JS_CONTROL_PANEL"></a></li>
                            <li class="tk_header_menu_link">
                                <a href="#" class="selected">
                                    <?php echo JText::_('Tickets'); ?>
                                </a> 
                            </li>
                            <li class="tk_header_menu_link">
                                <a href="#">
                                    <?php echo JText::_('Knowledge Base'); ?>
                                </a>
                            </li>
                            <li class="tk_header_menu_link">
                                <a href="#">
                                    <?php echo JText::_('Announcements'); ?>
                                </a>
                            </li>
                            <li class="tk_header_menu_link">
                                <a href="#">
                                    <?php echo JText::_('Downloads'); ?>
                                </a>
                            </li>
                            <li class="tk_header_menu_link">
                                <a href="#">
                                    <?php echo JText::_('FAQs'); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div id="tk_header_bottom">
                        <ul id="tk_header_bottom_menu">
                            <li class="tk_header_bottom_menu_link"><a href="#"><?php echo JText::_('New Ticket'); ?></a></li>
                            <li class="tk_header_bottom_menu_link">
                                <a href="#" class="selected">
                                    <?php echo JText::_('My Tickets'); ?>
                                </a> 
                            </li>
                            <li class="tk_header_bottom_menu_link"><a href="#"><?php echo JText::_('Ticket Status'); ?></a></li>
                        </ul>
                    </div>
                </div> */ ?>
                <div id="js-tk-heading">
                    <div id="jsst-tabs-wrp" class="">
                        <span class="jsst-header-tab js-ticket-homeclass">
                            <a class="js-cp-menu-link" href="#">
                                <?php echo JText::_('Dashboard'); ?>
                            </a>
                        </span>
                        <span class="jsst-header-tab js-ticket-openticketclass">
                            <a class="js-cp-menu-link" href="#">
                                <?php echo JText::_('New Ticket'); ?>
                            </a>
                        </span>
                        <span class="jsst-header-tab js-ticket-myticket">
                            <a class="js-cp-menu-link" href="#">
                                <?php echo JText::_('My Tickets'); ?>
                            </a>
                        </span>
                        <span class="jsst-header-tab js-ticket-loginlogoutclass">
                            <a class="js-cp-menu-link" href="#">
                                <?php echo JText::_('Log out'); ?>
                            </a>
                        </span>
                    </div>
                </div>
                <?php /* <div id="js-tk-tabswrapper">
                    <div id="js-tk-tabs">
                        <a href="#" class="selected">
                            <?php echo JText::_('Open'); ?></a>
                        <a href="#">
                            <?php echo JText::_('Answered'); ?></a>
                        <a href="#">
                            <?php echo JText::_('Overdue'); ?></a>
                        <a href="#">
                            <?php echo JText::_('Closed'); ?></a>
                        <a href="#">
                            <?php echo JText::_('Ticket'); ?></a>
                    </div>
                </div>
                <form id="adminForm" name="adminForm" method="post" action="#" class="js-tk-combinesearch">
                    <div class="js-col-md-12 js-filter-wrapper js-filter-wrapper-position"> 
                        <div id="js-filter-wrapper-toggle-search" class="js-col-md-12 js-filter-value"><input type="text" placeholder="Ticket ID JS_OR Email Address JS_OR Subject" class="text_area" value="" id="filter_ticketsearchkeys" name="filter_ticketsearchkeys"></div>
                        <div style="display:none;" id="js-filter-wrapper-toggle-ticketid" class="js-col-md-12 js-filter-value"><input type="text" placeholder="Ticket ID" class="text_area" value="" id="filter_ticketid" name="filter_ticketid"></div>
                        <div id="js-filter-wrapper-toggle-btn">
                            <div id="js-filter-wrapper-toggle-plus">
                                <img src="../components/com_jssupportticket/include/images/plus.png">
                            </div> 
                            <div style="display:none;" id="js-filter-wrapper-toggle-minus">
                                <img src="../components/com_jssupportticket/include/images/minus.png">
                            </div>
                        </div>
                    </div>
                    <div style="display:none;" id="js-filter-wrapper-toggle-area">
                        <div class="js-col-md-12 js-filter-wrapper">    
                            <div class="js-col-md-6 js-filter-value"><input type="text" placeholder="From" value="" class="text_area" id="filter_from" name="filter_from"></div>
                            <div class="js-col-md-6 js-filter-value"><input type="text" placeholder="Email" value="" class="text_area" id="filter_email" name="filter_email"></div>
                        </div>
                        <div class="js-col-md-12 js-filter-wrapper">    
                            <div class="js-col-md-6 js-filter-value">
                                <select name="filter_department" id="filter_department">
                                    <option selected="selected" value="">Select Department</option>
                                    <option value="1">Support</option>
                                    <option value="2">Finance</option>
                                    <option value="6">Others</option>
                                </select>
                            </div>
                            <div class="js-col-md-6 js-filter-value">
                                <select name="filter_priority" id="filter_priority">
                                    <option selected="selected" value="">Select Priority</option>
                                    <option value="1">High</option>
                                    <option value="2">low</option>
                                    <option value="3">Normal</option>
                                </select>
                            </div>
                        </div>
                        <div class="js-col-md-12 js-filter-wrapper">    
                            <div class="js-col-md-12 js-filter-value"><input type="text" placeholder="Subject" value="" class="text_area" id="filter_subject" name="filter_subject"></div>
                        </div>
                        <div class="js-col-md-12 js-filter-wrapper">    
                            <div class="js-col-md-4 js-filter-value"><div class="input-append"><input type="text" placeholder="JS_DATE_START" maxlength="19" size="10" class="inputbox hasTooltip" value="" id="filter_datestart" name="filter_datestart" title="" data-original-title=""><button id="filter_datestart_img" class="btn" type="button"><i class="icon-calendar"></i></button></div></div>
                            <div class="js-col-md-4 js-ticket-special-character js-nullpadding">-</div>
                            <div class="js-col-md-4 js-filter-value"><div class="input-append"><input type="text" placeholder="JS_DATE_END" maxlength="19" size="10" class="inputbox hasTooltip" value="" id="filter_dateend" name="filter_dateend" title="" data-original-title=""><button id="filter_dateend_img" class="btn" type="button"><i class="icon-calendar"></i></button></div></div>
                        </div>
                    </div>
                    <div class="js-col-md-12 js-filter-wrapper">
                        <div class="js-filter-button">
                            <button onclick="retrun false" class="tk-dft-btn"><?php echo JText::_('Search'); ?></button>
                            <button onclick="return false" class="tk-dft-btn"><?php echo JText::_('Reset'); ?></button>
                        </div>
                    </div>
                </form> */ ?>
                <div id="js-tk-sort-wrapper">
                        <span class="js-col-md-2 js-ticket-sorting-link">
                            <a href="#" class=""><?php echo JText::_('Subject'); ?></a>
                        </span>
                        <span class="js-col-md-2 js-ticket-sorting-link">
                            <a href="#" class=""><?php echo JText::_('Priority'); ?></a>
                        </span>
                        <span class="js-col-md-2 js-ticket-sorting-link">
                            <a href="#" class=""><?php echo JText::_('Ticket Id'); ?></a>
                        </span>
                        <span class="js-col-md-2 js-ticket-sorting-link">
                            <a href="#" class=""><?php echo JText::_('Answered'); ?></a>
                        </span>
                        <span class="js-col-md-2 js-ticket-sorting-link">
                            <a href="#" class="selected"><?php echo JText::_('Status'); ?> <img src="../components/com_jssupportticket/include/images/sort0.png"> </a>
                        </span>
                        <span class="js-col-md-2 js-ticket-sorting-link">
                            <a href="#" class=""><?php echo JText::_('Created'); ?></a>
                        </span>
                </div>
                <div id="js-tk-wrapper">
                    <div class="js-col-xs-12 js-col-md-1 js-icon">
                        <img src="../components/com_jssupportticket/include/images/user.png">
                    </div>
                    <div class="js-col-xs-12 js-col-md-7 js-middle">
                        <div class="js-col-md-12 js-wrapper"><span class="js-tk-title"><?php echo JText::_('Subject'); ?><font>:</font> </span><span class="js-tk-value"><a href="#"> <?php echo JText::_('Subject'); ?><?php echo JText::_('Test Ticket'); ?></a></span></div>
                        <div class="js-col-md-12 js-wrapper"><span class="js-tk-title"><?php echo JText::_('From'); ?><font>:</font></span><span class="js-tk-value"> <?php echo JText::_('Test User'); ?><?php echo JText::_('Subject'); ?></span></div>
                        <div class="js-col-md-12 js-tk-preletive js-wrapper">
                            <span class="js-tk-title"><?php echo JText::_('Department'); ?><font>:</font></span><span class="js-tk-value"><?php echo JText::_('Support'); ?></span>
                        </div>
                        <div class="js-tk-pabsolute">
                                <span style="background-color: #5bb12f;"><?php echo JText::_('New'); ?></span>
                            </div>
                    </div>
                    <div class="js-col-xs-12 js-col-md-4 js-right">
                        <div class="js-col-md-12 js-wrapper"><span class="js-tk-title js-col-md-6"><?php echo JText::_('Ticket Id'); ?></span><span class="js-tk-value js-col-md-6"> <?php echo JText::_('dthxHzcQf63'); ?></span></div>
                        <div class="js-col-md-12 js-wrapper"><span class="js-tk-title js-col-md-6"><?php echo JText::_('Last Reply'); ?></span><span class="js-tk-value js-col-md-6"><?php echo JText::_('07-05-2015'); ?></span></div>
                        <div class="js-col-md-12 js-wrapper"><span class="js-tk-title js-col-md-6"><?php echo JText::_('Priority'); ?></span><span style="background:#ee1d24;color:#fff;" class="js-tk-value js-col-md-6 js-ticket-wrapper-textcolor"><?php echo JText::_('High'); ?></span></div>
                    </div>
                </div>
                <div id="js-tk-wrapper">
                    <div class="js-col-xs-12 js-col-md-1 js-icon">
                        <img src="../components/com_jssupportticket/include/images/user.png">
                    </div>
                    <div class="js-col-xs-12 js-col-md-7 js-middle">
                        <div class="js-col-md-12 js-wrapper"><span class="js-tk-title"><?php echo JText::_('Subject'); ?><font>:</font> </span><span class="js-tk-value"><a href="#"> <?php echo JText::_('Test Ticket'); ?></a></span></div>
                        <div class="js-col-md-12 js-wrapper"><span class="js-tk-title"><?php echo JText::_('From'); ?><font>:</font></span><span class="js-tk-value"> <?php echo JText::_('Test User'); ?></span></div>
                        <div class="js-col-md-12 js-tk-preletive js-wrapper">
                            <span class="js-tk-title"><?php echo JText::_('Department'); ?><font>:</font></span><span class="js-tk-value"><?php echo JText::_('Support'); ?></span>
                        </div>
                        <div class="js-tk-pabsolute">
                                <span style="background-color: #5bb12f;"><?php echo JText::_('New'); ?></span>
                        </div>
                    </div>
                    <div class="js-col-xs-12 js-col-md-4 js-right">
                        <div class="js-col-md-12 js-wrapper"><span class="js-tk-title js-col-md-6"><?php echo JText::_('Ticket Id'); ?></span><span class="js-tk-value js-col-md-6"> <?php echo JText::_('dthxHzcQf63'); ?></span></div>
                        <div class="js-col-md-12 js-wrapper"><span class="js-tk-title js-col-md-6"><?php echo JText::_('Last Reply'); ?></span><span class="js-tk-value js-col-md-6"><?php echo JText::_('07-05-2015'); ?></span></div>
                        <div class="js-col-md-12 js-wrapper"><span class="js-tk-title js-col-md-6"><?php echo JText::_('Priority'); ?></span><span style="background:#ee1d24;color:#fff;" class="js-tk-value js-col-md-6 js-ticket-wrapper-textcolor"><?php echo JText::_('High'); ?></span></div>
                    </div>
                </div>
                <div id="js-tk-wrapper">
                    <div class="js-col-xs-12 js-col-md-1 js-icon">
                        <img src="../components/com_jssupportticket/include/images/user.png">
                    </div>
                    <div class="js-col-xs-12 js-col-md-7 js-middle">
                        <div class="js-col-md-12 js-wrapper"><span class="js-tk-title"><?php echo JText::_('Subject'); ?><font>:</font> </span><span class="js-tk-value"><a href="#"><?php echo JText::_(' Test Ticket'); ?></a></span></div>
                        <div class="js-col-md-12 js-wrapper"><span class="js-tk-title"><?php echo JText::_('From'); ?><font>:</font></span><span class="js-tk-value"> <?php echo JText::_('Test User'); ?></span></div>
                        <div class="js-col-md-12 js-tk-preletive js-wrapper">
                            <span class="js-tk-title"><?php echo JText::_('Department'); ?><font>:</font></span><span class="js-tk-value"><?php echo JText::_('Support'); ?></span>
                        </div>
                        <div class="js-tk-pabsolute">
                                <span style="background-color: #5bb12f;"><?php echo JText::_('New'); ?></span>
                        </div>    
                    </div>
                    <div class="js-col-xs-12 js-col-md-4 js-right">
                        <div class="js-col-md-12 js-wrapper"><span class="js-tk-title js-col-md-6"><?php echo JText::_('Ticket Id'); ?></span><span class="js-tk-value js-col-md-6"> <?php echo JText::_('dthxHzcQf63'); ?></span></div>
                        <div class="js-col-md-12 js-wrapper"><span class="js-tk-title js-col-md-6"><?php echo JText::_('Last Reply'); ?></span><span class="js-tk-value js-col-md-6"><?php echo JText::_('07-05-2015'); ?></span></div>
                        <div class="js-col-md-12 js-wrapper"><span class="js-tk-title js-col-md-6"><?php echo JText::_('Priority'); ?></span><span style="background:#ee1d24;color:#fff;" class="js-tk-value js-col-md-6 js-ticket-wrapper-textcolor"><?php echo JText::_('High'); ?></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>
<script type="text/javascript" >
    jQuery(document).ready(function () {
        makeColorPicker('<?php echo $this->result[0]['color1']; ?>', '<?php echo $this->result[0]['color2']; ?>', '<?php echo $this->result[0]['color3']; ?>', '<?php echo $this->result[0]['color4']; ?>', '<?php echo $this->result[0]['color5']; ?>', '<?php echo $this->result[0]['color6']; ?>', '<?php echo $this->result[0]['color7']; ?>');
    });
    function makeColorPicker(color1, color2, color3, color4, color5, color6, color7) {
        jQuery('input#color1').ColorPicker({
            color: color1,
            onShow: function (colpkr) {
                jQuery(colpkr).fadeIn(500);
                return false;
            },
            onHide: function (colpkr) {
                jQuery(colpkr).fadeOut(500);
                return false;
            },
            onChange: function (hsb, hex, rgb) {
                jQuery('input#color1').css('backgroundColor', '#' + hex).val('#' + hex);                
                jQuery('div#tk_header_nav,li.js-tk-sort-manulink a').css('backgroundColor', '#' + hex);
            }
        });
        jQuery('input#color2').ColorPicker({
            color: color2,
            onShow: function (colpkr) {
                jQuery(colpkr).fadeIn(500);
                return false;
            },
            onHide: function (colpkr) {
                jQuery(colpkr).fadeOut(500);
                return false;
            },
            onChange: function (hsb, hex, rgb) {
                jQuery('input#color2').css('backgroundColor', '#' + hex).val('#' + hex);
                jQuery('li.tk_header_menu_link a.selected').css('backgroundColor', '#' + hex).val('#' + hex);
                jQuery('div#tk_header_bottom,div#js-tk-tabs a.selected,ul#js-tk-sort-manu li.js-tk-sort-manulink a.selected').css('backgroundColor', '#' + hex).val('#' + hex);
                jQuery('div#js-tk-heading').css('borderColor', '#' + hex).val('#' + hex);
                jQuery('div#js-tk-heading h3,div#js-tk-wrapper span.js-tk-value a').css('color', '#' + hex).val('#' + hex);
                jQuery('div#js-tk-tabs a').css('borderColor', '#' + hex).val('#' + hex);

                jQuery('li.tk_header_menu_link a').mouseover(function () {
                    jQuery(this).css('color', jQuery('input#color2').val());
                }).mouseout(function () {
                    jQuery(this).css('color', jQuery('input#color7').val());
                });
                jQuery('div#js-tk-tabs a').mouseover(function () {
                    jQuery(this).css('backgroundColor', jQuery('input#color2').val());
                    jQuery(this).css('color', jQuery('input#color7').val());
                }).mouseout(function () {
                    if(!jQuery(this).hasClass('selected')){
                        jQuery(this).css('color', jQuery('input#color2').val());
                        jQuery(this).css('backgroundColor', jQuery('input#color7').val());
                    }
                });
                jQuery('ul#js-tk-sort-manu li.js-tk-sort-manulink a').mouseover(function () {
                    jQuery(this).css('backgroundColor', jQuery('input#color2').val());
                }).mouseout(function () {
                    if(!jQuery(this).hasClass('selected')){
                        jQuery(this).css('backgroundColor', jQuery('input#color1').val());
                    }
                });
            }
        });
        jQuery('input#color3').ColorPicker({
            color: color3,
            onShow: function (colpkr) {
                jQuery(colpkr).fadeIn(500);
                return false;
            },
            onHide: function (colpkr) {
                jQuery(colpkr).fadeOut(500);
                return false;
            },
            onChange: function (hsb, hex, rgb) {
                jQuery('input#color3').css('backgroundColor', '#' + hex).val('#' + hex);
                jQuery('div#js-tk-wrapper').css('backgroundColor', '#' + hex).val('#'+hex);
            }
        });
        jQuery('input#color4').ColorPicker({
            color: color4,
            onShow: function (colpkr) {
                jQuery(colpkr).fadeIn(500);
                return false;
            },
            onHide: function (colpkr) {
                jQuery(colpkr).fadeOut(500);
                return false;
            },
            onChange: function (hsb, hex, rgb) {
                jQuery('input#color4').css('backgroundColor', '#' + hex).val('#' + hex);
                jQuery('span.js-tk-title').css('color', '#' + hex);
                jQuery('span.js-tk-value').css('color', '#' + hex);
            }
        });
        jQuery('input#color5').ColorPicker({
            color: color5,
            onShow: function (colpkr) {
                jQuery(colpkr).fadeIn(500);
                return false;
            },
            onHide: function (colpkr) {
                jQuery(colpkr).fadeOut(500);
                return false;
            },
            onChange: function (hsb, hex, rgb) {
                jQuery('input#color5').css('backgroundColor', '#' + hex).val('#' + hex);
                jQuery('form.js-tk-combinesearch,div.js-filter-button').css('borderColor', '#' + hex);
                jQuery('div#js-tk-wrapper').css('borderColor', '#' + hex);
            }
        });
        jQuery('input#color6').ColorPicker({
            color: color6,
            onShow: function (colpkr) {
                jQuery(colpkr).fadeIn(500);
                return false;
            },
            onHide: function (colpkr) {
                jQuery(colpkr).fadeOut(500);
                return false;
            },
            onChange: function (hsb, hex, rgb) {
                jQuery('input#color6').css('backgroundColor', '#' + hex).val('#' + hex);
                //jQuery('a.js-myticket-link').css('backgroundColor', '#' + hex);
            }
        });
        jQuery('input#color7').ColorPicker({
            color: color7,
            onShow: function (colpkr) {
                jQuery(colpkr).fadeIn(500);
                return false;
            },
            onHide: function (colpkr) {
                jQuery(colpkr).fadeOut(500);
                return false;
            },
            onChange: function (hsb, hex, rgb) {
                jQuery('input#color7').css('backgroundColor', '#' + hex).val('#' + hex);
                jQuery("li.tk_header_menu_link a,li.js-tk-sort-manulink a").each(function () {
                    jQuery(this).css('color', '#' + hex)
                });
            }
        });

    }
</script>
<div id="black_wrapper_jobapply" style="display:none;"></div>
<div id="js_jobapply_main_wrapper" style="display:none;padding:0px 5px;">
    <div id="js_job_wrapper">
        <span class="js_job_controlpanelheading"><?php echo JText::_('Preset Theme'); ?></span>        
        <div class="js_theme_wrapper">
            <div class="theme_platte">
                <div class="color_wrapper">
                    <div class="color 1" style="background:#4f6df5;"></div>
                    <div class="color 2" style="background:#2b2b2b;"></div>
                    <div class="color 3" style="background:#f5f2f5;"></div>
                    <div class="color 4" style="background:#636363;"></div>
                    <div class="color 5" style="background:#d1d1d1;"></div>
                    <div class="color 6" style="background:#E7E7E7;"></div>
                    <div class="color 7" style="background:#FFFFFF;"></div>
                    <span class="theme_name"><?php echo JText::_('Blue'); ?></span>
                    <img class="preview" src="components/com_jssupportticket/include/images/themes/preview1.png" />
                    <a href="#" class="preview"></a>
                    <a href="#" class="set_theme"></a>
                </div>
            </div>
            <div class="theme_platte">
                <div class="color_wrapper">
                    <div class="color 1" style="background:#E43039;"></div>
                    <div class="color 2" style="background:#2b2b2b;"></div>
                    <div class="color 3" style="background:#f5f2f5;"></div>
                    <div class="color 4" style="background:#636363;"></div>
                    <div class="color 5" style="background:#d1d1d1;"></div>
                    <div class="color 6" style="background:#E7E7E7;"></div>
                    <div class="color 7" style="background:#FFFFFF;"></div>
                    <span class="theme_name"><?php echo JText::_('Red'); ?></span>
                    <img class="preview" src="components/com_jssupportticket/include/images/themes/preview2.png" />
                    <a href="#" class="preview"></a>
                    <a href="#" class="set_theme"></a>
                </div>
            </div>
            <div class="theme_platte">
                <div class="color_wrapper">
                    <div class="color 1" style="background:#36BC9A;"></div>
                    <div class="color 2" style="background:#2b2b2b;"></div>
                    <div class="color 3" style="background:#f5f2f5;"></div>
                    <div class="color 4" style="background:#636363;"></div>
                    <div class="color 5" style="background:#d1d1d1;"></div>
                    <div class="color 6" style="background:#E7E7E7;"></div>
                    <div class="color 7" style="background:#FFFFFF;"></div>
                    <span class="theme_name"><?php echo JText::_('Greenish'); ?></span>
                    <img class="preview" src="components/com_jssupportticket/include/images/themes/preview3.png" />
                    <a href="#" class="preview"></a>
                    <a href="#" class="set_theme"></a>
                </div>
            </div>
            <div class="theme_platte">
                <div class="color_wrapper">
                    <div class="color 1" style="background:#A601E1;"></div>
                    <div class="color 2" style="background:#2b2b2b;"></div>
                    <div class="color 3" style="background:#f5f2f5;"></div>
                    <div class="color 4" style="background:#636363;"></div>
                    <div class="color 5" style="background:#d1d1d1;"></div>
                    <div class="color 6" style="background:#E7E7E7;"></div>
                    <div class="color 7" style="background:#FFFFFF;"></div>
                    <span class="theme_name"><?php echo JText::_('Purple'); ?></span>
                    <img class="preview" src="components/com_jssupportticket/include/images/themes/preview4.png" />
                    <a href="#" class="preview"></a>
                    <a href="#" class="set_theme"></a>
                </div>
            </div>
            <div class="theme_platte">
                <div class="color_wrapper">
                    <div class="color 1" style="background:#F48243;"></div>
                    <div class="color 2" style="background:#2b2b2b;"></div>
                    <div class="color 3" style="background:#f5f2f5;"></div>
                    <div class="color 4" style="background:#636363;"></div>
                    <div class="color 5" style="background:#d1d1d1;"></div>
                    <div class="color 6" style="background:#E7E7E7;"></div>
                    <div class="color 7" style="background:#FFFFFF;"></div>
                    <span class="theme_name"><?php echo JText::_('Orange'); ?></span>
                    <img class="preview" src="components/com_jssupportticket/include/images/themes/preview5.png" />
                    <a href="#" class="preview"></a>
                    <a href="#" class="set_theme"></a>
                </div>
            </div>
            <div class="theme_platte">
                <div class="color_wrapper">
                    <div class="color 1" style="background:#8CC051;"></div>
                    <div class="color 2" style="background:#2b2b2b;"></div>
                    <div class="color 3" style="background:#f5f2f5;"></div>
                    <div class="color 4" style="background:#636363;"></div>
                    <div class="color 5" style="background:#d1d1d1;"></div>
                    <div class="color 6" style="background:#E7E7E7;"></div>
                    <div class="color 7" style="background:#FFFFFF;"></div>
                    <span class="theme_name"><?php echo JText::_('Green'); ?></span>
                    <img class="preview" src="components/com_jssupportticket/include/images/themes/preview6.png" />
                    <a href="#" class="preview"></a>
                    <a href="#" class="set_theme"></a>
                </div>
            </div>
            <div class="theme_platte">
                <div class="color_wrapper">
                    <div class="color 1" style="background:#57585A;"></div>
                    <div class="color 2" style="background:#2b2b2b;"></div>
                    <div class="color 3" style="background:#f5f2f5;"></div>
                    <div class="color 4" style="background:#636363;"></div>
                    <div class="color 5" style="background:#d1d1d1;"></div>
                    <div class="color 6" style="background:#E7E7E7;"></div>
                    <div class="color 7" style="background:#FFFFFF;"></div>
                    <span class="theme_name"><?php echo JText::_('Black'); ?></span>
                    <img class="preview" src="components/com_jssupportticket/include/images/themes/preview7.png" />
                    <a href="#" class="preview"></a>
                    <a href="#" class="set_theme"></a>
                </div>
            </div>
        </div>
    </div>
</div> 
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('a#preset_theme').click(function (e) {
            e.preventDefault();
            jQuery("div#js_jobapply_main_wrapper").fadeIn();
            jQuery("div#black_wrapper_jobapply").fadeIn();
        });
        jQuery("div#black_wrapper_jobapply").click(function () {
            jQuery("div#js_jobapply_main_wrapper").fadeOut();
            jQuery("div#black_wrapper_jobapply").fadeOut();
        });
        jQuery('a.preview').each(function (index, element) {
            jQuery(this).hover(function () {
                if (index > 2)
                    jQuery(this).parent().find('img.preview').css('top', "-110px");
                jQuery(jQuery(this).parent().find('img.preview')).show();
            }, function () {
                jQuery(jQuery(this).parent().find('img.preview')).hide();
            });
        });
        jQuery('a.set_theme').each(function (index, element) {
            jQuery(this).click(function (e) {
                e.preventDefault();
                var div = jQuery(this).parent();
                var color1 = rgb2hex(jQuery(div.find('div.1')).css('backgroundColor'));
                var color2 = rgb2hex(jQuery(div.find('div.2')).css('backgroundColor'));
                var color3 = rgb2hex(jQuery(div.find('div.3')).css('backgroundColor'));
                var color4 = rgb2hex(jQuery(div.find('div.4')).css('backgroundColor'));
                var color5 = rgb2hex(jQuery(div.find('div.5')).css('backgroundColor'));
                var color6 = rgb2hex(jQuery(div.find('div.6')).css('backgroundColor'));
                var color7 = rgb2hex(jQuery(div.find('div.7')).css('backgroundColor'));
                jQuery('input#color1').val(color1).css('backgroundColor', color1).ColorPickerSetColor(color1);
                jQuery('input#color2').val(color2).css('backgroundColor', color2).ColorPickerSetColor(color2);
                jQuery('input#color3').val(color3).css('backgroundColor', color3).ColorPickerSetColor(color3);
                jQuery('input#color4').val(color4).css('backgroundColor', color4).ColorPickerSetColor(color4);
                jQuery('input#color5').val(color5).css('backgroundColor', color5).ColorPickerSetColor(color5);
                jQuery('input#color6').val(color6).css('backgroundColor', color6).ColorPickerSetColor(color6);
                jQuery('input#color7').val(color7).css('backgroundColor', color7).ColorPickerSetColor(color7);
                themeSelectionEffect();
                jQuery("div#js_jobapply_main_wrapper").fadeOut();
                jQuery("div#black_wrapper_jobapply").fadeOut();
            });
        });
    });
    function rgb2hex(rgb) {
        rgb = rgb.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+))?\)$/);
        function hex(x) {
            return ("0" + parseInt(x).toString(16)).slice(-2);
        }
        return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
    }
    function themeSelectionEffect() {
        jQuery('div#tk_header_nav,li.js-tk-sort-manulink a').css('backgroundColor', '#' + jQuery('input#color1').val());
        jQuery('li.tk_header_menu_link a.selected').css('backgroundColor', '#' + jQuery('input#color2').val()).val('#' + jQuery('input#color2').val());
        jQuery('div#tk_header_bottom,div#js-tk-tabs a.selected,ul#js-tk-sort-manu li.js-tk-sort-manulink a.selected').css('backgroundColor', '#' + jQuery('input#color2').val()).val('#' + jQuery('input#color2').val());
        jQuery('div#js-tk-heading').css('borderColor', '#' + jQuery('input#color2').val()).val('#' + jQuery('input#color2').val());
        jQuery('div#js-tk-heading h3,div#js-tk-wrapper span.js-tk-value a').css('color', '#' + jQuery('input#color2').val()).val('#' + jQuery('input#color2').val());
        jQuery('div#js-tk-tabs a').css('borderColor', '#' + jQuery('input#color2').val()).val('#' + jQuery('input#color2').val());
        jQuery('li.tk_header_menu_link a').mouseover(function () {
            jQuery(this).css('color', jQuery('input#color2').val());
        }).mouseout(function () {
            jQuery(this).css('color', jQuery('input#color7').val());
        });
        jQuery('div#js-tk-tabs a').mouseover(function () {
            jQuery(this).css('backgroundColor', jQuery('input#color2').val());
            jQuery(this).css('color', jQuery('input#color7').val());
        }).mouseout(function () {
            if(!jQuery(this).hasClass('selected')){
                jQuery(this).css('color', jQuery('input#color2').val());
                jQuery(this).css('backgroundColor', jQuery('input#color7').val());
            }
        });
        jQuery('ul#js-tk-sort-manu li.js-tk-sort-manulink a').mouseover(function () {
            jQuery(this).css('backgroundColor', jQuery('input#color2').val());
        }).mouseout(function () {
            if(!jQuery(this).hasClass('selected')){
                jQuery(this).css('backgroundColor', jQuery('input#color1').val());
            }
        });
        console.log('here');
        jQuery('div#js-tk-wrapper').css('backgroundColor', '#' + jQuery('input#color3').val()).val('#'+jQuery('input#color3').val());
        jQuery('span.js-tk-title').css('color', '#' + jQuery('input#color4').val());
        jQuery('span.js-tk-value').css('color', '#' + jQuery('input#color4').val());
        jQuery('form.js-tk-combinesearch,div.js-filter-button').css('borderColor', '#' + jQuery('input#color5').val());
        jQuery('div#js-tk-wrapper').css('borderColor', '#' + jQuery('input#color5').val());
        jQuery("li.tk_header_menu_link a,li.js-tk-sort-manulink a").each(function () {
            jQuery(this).css('color', '#' + jQuery('input#color7').val())
        });
        jQuery('input#color2').css('backgroundColor', jQuery('input#color2').val()).val(jQuery('input#color2').val());
        jQuery('li.tk_header_menu_link a.selected').css('backgroundColor', jQuery('input#color2').val()).val(jQuery('input#color2').val());
        jQuery('div#tk_header_bottom,div#js-tk-tabs a.selected,ul#js-tk-sort-manu li.js-tk-sort-manulink a.selected').css('backgroundColor', jQuery('input#color2').val()).val(jQuery('input#color2').val());
        jQuery('div#js-tk-heading').css('backgroundColor', jQuery('input#color1').val()).val(jQuery('input#color1').val());
        jQuery('div#js-tk-heading').css('borderColor', jQuery('input#color2').val()).val(jQuery('input#color2').val());
        jQuery('div#js-tk-heading h3,div#js-tk-wrapper span.js-tk-value a').css('color', jQuery('input#color2').val()).val(jQuery('input#color2').val());
        jQuery('div#js-tk-tabs a').css('borderColor', jQuery('input#color2').val()).val(jQuery('input#color2').val());

        jQuery('li.tk_header_menu_link a').mouseover(function () {
            jQuery(this).css('color', jQuery('input#color2').val());
        }).mouseout(function () {
            jQuery(this).css('color', jQuery('input#color7').val());
        });
        jQuery('div#js-tk-tabs a').mouseover(function () {
            jQuery(this).css('backgroundColor', jQuery('input#color2').val());
            jQuery(this).css('color', jQuery('input#color7').val());
        }).mouseout(function () {
            if(!jQuery(this).hasClass('selected')){
                jQuery(this).css('color', jQuery('input#color2').val());
                jQuery(this).css('backgroundColor', jQuery('input#color7').val());
            }
        });
        jQuery('ul#js-tk-sort-manu li.js-tk-sort-manulink a').mouseover(function () {
            jQuery(this).css('backgroundColor', jQuery('input#color2').val());
        }).mouseout(function () {
            if(!jQuery(this).hasClass('selected')){
                jQuery(this).css('backgroundColor', jQuery('input#color1').val());
            }
        });
    }
</script>
