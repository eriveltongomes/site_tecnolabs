<?php
/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:		Buruj Solutions
  + Contact:		www.burujsolutions.com , info@burujsolutions.com
 * Created on:	May 03, 2012
  ^
  + Project: 	JS Tickets
  ^
 */
defined('_JEXEC') or die('Restricted access');
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
                        <li><?php echo JText::_('Satisfaction Report'); ?></li>
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
        <div id="js-tk-heading">
            <h1 class="jsstadmin-head-text"><?php echo JText::_('Satisfaction Report'); ?></h1>
        </div>
        <?php 
        $percentage = round($this->satisfaction['avg']*20,2);
        ?>
        <div id="jsstadmin-data-wrp" class="jsst-statifacetion-report-wrapper" >
            <div class="statifacetion-report-left" >
                <?php 
                    $class="first";
                    $src ="excelent.png";
                    if($percentage > 80){
                        $class="first";
                        $src ="excelent.png";
                    }elseif($percentage > 60){
                        $class="second";
                        $src ="happy.png";
                    }elseif($percentage > 40){
                        $class="third";
                        $src ="normal.png";
                    }elseif($percentage > 20){
                        $class="fourth";
                        $src ="bad.png";
                    }elseif($percentage > 0){
                        $class="fifth";
                        $src ="angery.png";
                    }
                    ?>
                <div class="top-number <?php echo $class;?>" >
                    <?php echo $percentage.'%'; ?>
                </div>
                <span class="total-feedbacks" >
                    <?php echo JText::_('Based on').'&nbsp;'. $this->satisfaction['result'][6].'&nbsp;'. JText::_('Feedbacks');?>
                </span>
                <div class="top-text" >
                    <?php echo JText::_('Customer Satisfaction')?>
                </div>
            </div>
            <div class="satisfaction-report-right <?php echo $class; ?>" >
                <img src="../components/com_jssupportticket/include/images/<?php echo $src;?>" />
            </div>
            <div class="jsst-satisfaction-report-bottom" >
                <div class="indi-stats first" > 
                    <img src="../components/com_jssupportticket/include/images/excelent.png" />
                    <div class="stats-percentage" ><?php 
                        if($this->satisfaction['result'][6] != 0){
                            echo round($this->satisfaction['result'][5]/$this->satisfaction['result'][6]*100 ,2).'%'; 
                        }else{
                            echo JText::_('NA');
                        }
                        ?></div>
                    <div class="stats-text" > <?php echo JText::_('Excellent')?> </div>
                </div>
                <div class="indi-stats second" > 
                    <img src="../components/com_jssupportticket/include/images/happy.png" />
                    <div class="stats-percentage" ><?php 
                        if($this->satisfaction['result'][6] != 0){
                            echo round($this->satisfaction['result'][4]/$this->satisfaction['result'][6]*100 ,2).'%'; 
                        }else{
                            echo JText::_('NA');
                        }
                        ?></div>
                    <div class="stats-text" > <?php echo JText::_('Happy')?> </div>
                </div>
                <div class="indi-stats third" > 
                    <img src="../components/com_jssupportticket/include/images/normal.png" />
                    <div class="stats-percentage" ><?php 
                        if($this->satisfaction['result'][6] != 0){
                            echo round($this->satisfaction['result'][3]/$this->satisfaction['result'][6]*100 ,2).'%'; 
                        }else{
                            echo JText::_('NA');
                        }
                        ?></div>
                    <div class="stats-text" > <?php echo JText::_('Normal')?> </div>
                </div>
                <div class="indi-stats fourth" > 
                    <img src="../components/com_jssupportticket/include/images/bad.png" />
                    <div class="stats-percentage" ><?php 
                        if($this->satisfaction['result'][6] != 0){
                            echo round($this->satisfaction['result'][2]/$this->satisfaction['result'][6]*100 ,2).'%'; 
                        }else{
                            echo JText::_('NA');
                        }
                        ?></div>
                    <div class="stats-text" > <?php echo JText::_('Sad')?> </div>
                </div>
                <div class="indi-stats fifth" > 
                    <img src="../components/com_jssupportticket/include/images/angery.png" />
                    <div class="stats-percentage" ><?php 
                        if($this->satisfaction['result'][6] != 0){
                            echo round($this->satisfaction['result'][1]/$this->satisfaction['result'][6]*100 ,2).'%'; 
                        }else{
                            echo JText::_('NA');
                        }
                        ?></div>
                    <div class="stats-text" > <?php echo JText::_('Angry')?> </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>
