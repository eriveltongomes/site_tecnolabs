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
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('a[href="#"]').click(function(e){
            e.preventDefault();
        });
    });
</script>
<div class="js-row js-null-margin">
<?php
if($this->config['offline'] != '1'){
    require_once JPATH_COMPONENT_SITE . '/views/header.php';
    $document = JFactory::getDocument();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/inc.css/reports-departmentreports.css', 'text/css');
    $language = JFactory::getLanguage();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketresponsive.css');
    if($language->isRTL()){
        $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketdefaultrtl.css');
    }
    if($this->per_granted){
        if(!$this->user->getIsGuest()){
            if($this->user->getIsStaff()){
                if(!$this->user->getIsStaffDisable()){ ?> 
                    <script type="text/javascript" src="https://www.google.com/jsapi?autoload={'modules':[{'name':'visualization','version':'1','packages':['corechart']}]}"></script>                
                    <script type="text/javascript">
                        <?php
                        if(!empty($this->result['pie3d_chart1'])){ ?>
                            google.charts.load('current', {packages: ['corechart']});
                            google.setOnLoadCallback(drawPie3d1Chart);
                            <?php
                        }
                        ?>
                        function drawPie3d1Chart() {
                            var data = google.visualization.arrayToDataTable([
                              ['<?php echo JText::_('Departments'); ?>', '<?php echo JText::_('Tickets By Department'); ?>'],
                              <?php echo $this->result['pie3d_chart1']; ?>
                            ]);

                            var options = {
                              title: '<?php echo JText::_('Ticket by departments'); ?>',
                              chartArea :{width:450,height:350},
                              pieHole:0.4,
                            };

                            var chart = new google.visualization.PieChart(document.getElementById('pie3d_chart1'));
                            chart.draw(data, options);
                        }
                    </script>
                    <div id="jsst-wrapper-top">
                        <?php if($this->config['cur_location'] == 1){ ?>
    			    <div id="jsst-wrapper-top-left">
				<div id="jsst-breadcrunbs">
				    <ul>
				        <li>
				            <a href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel&Itemid=<?php echo $this->Itemid; ?>" title="Dashboard">
				                <?php echo JText::_('Dashboard'); ?>
				            </a>
				        </li>
				        <li>
				            <?php echo JText::_('Department Reports'); ?>
				        </li>
				    </ul>
				</div>
    			    </div>
                        <?php } ?>
        			</div>
                    <div class="js-ticket-downloads-wrp">
                        <div class="js-ticket-downloads-heading-wrp">
                            <?php echo JText::_('Department Reports') ?>
                        </div>
                        <div class="js-col-md-12 js-ticket-pie-chart-wrapper">
                            <div id="pie3d_chart1" style="height:400px;width:100%;">
                            <?php
                            if(empty($this->result['pie3d_chart1'])){ ?>
                                <div class="donut_chart" id="no_message"><?php echo JText::_('No Data'); ?></div>
                                <?php
                            } ?>
                            </div>
                        </div>
                        <?php if(!empty($this->result['departments_report'])){ ?>
                            <?php  foreach($this->result['departments_report'] AS $department){ ?>
                                    <div class="js-admin-staff-wrapper js-departmentlist">
                                        <div class="js-col-md-4 nopadding js-festaffreport-img">
                                            <div class="js-col-md-12 jsposition-reletive">
                                                <div class="departmentname">
                                                    <?php
                                                        echo $department->departmentname;
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="js-col-md-8 nopadding js-festaffreport-data">
                                            <div class="jsreportfixwidth js-admin-report-box box1">
                                                <span class="js-report-box-number"><?php echo $department->openticket; ?></span>
                                                <span class="js-report-box-title"><?php echo JText::_('New'); ?></span>
                                                <div class="js-report-box-color"></div>
                                            </div>
                                            <div class="jsreportfixwidth js-admin-report-box box2">
                                                <span class="js-report-box-number"><?php echo $department->answeredticket; ?></span>
                                                <span class="js-report-box-title"><?php echo JText::_('Answered'); ?></span>
                                                <div class="js-report-box-color"></div>
                                            </div>
                                            <div class="jsreportfixwidth js-admin-report-box box3">
                                                <span class="js-report-box-number"><?php echo $department->pendingticket; ?></span>
                                                <span class="js-report-box-title"><?php echo JText::_('Pending'); ?></span>
                                                <div class="js-report-box-color"></div>
                                            </div>
                                            <div class="jsreportfixwidth js-admin-report-box box4">
                                                <span class="js-report-box-number"><?php echo $department->overdueticket; ?></span>
                                                <span class="js-report-box-title"><?php echo JText::_('Overdue'); ?></span>
                                                <div class="js-report-box-color"></div>
                                            </div>
                                            <div class="jsreportfixwidth js-admin-report-box box5">
                                                <span class="js-report-box-number"><?php echo $department->closeticket; ?></span>
                                                <span class="js-report-box-title"><?php echo JText::_('Closed'); ?></span>
                                                <div class="js-report-box-color"></div>
                                            </div>
                                        </div>
                                    </div>
                            <?php } ?>
                            <form action="<?php echo JRoute::_('index.php?option=com_jssupportticket&c=reports&layout=departmentreports&Itemid='.$this->Itemid); ?>" method="post">
                                <div id="jl_pagination" class="pagination">
                                    <div id="jl_pagination_pageslink">
                                        <?php echo $this->pagination->getPagesLinks(); ?>
                                    </div>
                                    <div id="jl_pagination_box">
                                        <?php   
                                            // echo JText::_('Display #');
                                            echo $this->pagination->getLimitBox();
                                        ?>
                                    </div>
                                    <div id="jl_pagination_counter">
                                        <?php echo $this->pagination->getResultsCounter(); ?>
                                    </div>
                                </div>
                            </form>
                         <?php } else {   
                                    messageslayout::getRecordNotFound(); //Empty Record
                        } ?>
                <?php    
                }else{
                    messageslayout::getStaffDisable(); //staff disabled
                }
            }else{
                messageslayout::getNotStaffMember(); //user not staff
            }
        }else{
            messageslayout::getUserGuest($this->layoutname,$this->Itemid); //user guest
        }
    }else{
        messageslayout::getPermissionNotAllow(); //permission not granted
    }
}else{
    messageslayout::getSystemOffline($this->config['title'],$this->config['offline_text']); //offline
}//End 
?>
</div>
