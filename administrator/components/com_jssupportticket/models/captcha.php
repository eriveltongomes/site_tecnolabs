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

defined('_JEXEC') or die('Not Allowed');
jimport('joomla.application.component.model');
jimport('joomla.html.html');

class JSSupportticketModelCaptcha extends JSSupportTicketModel
{
	function __construct(){
		parent::__construct();
	}

	function getCaptchaForForm(){
		$session = JFactory::getApplication()->getSession();
		$rand=$this->random();
		$session->set('jsticket_spamcheckid',$rand , 'jsticket_checkspamcalc');
		$session->set('jsticket_rot13', mt_rand(0, 1), 'jsticket_checkspamcalc');
		// Determine operator
		$config = $this->getJSModel('config')->getConfigs();
		$operator=2;
		if($operator==2){
			$tcalc = $config['owncaptcha_calculationtype'];
		}

		// Determine max. operand
		$max_value = 20;
		$negativ=1;

		$operend_1 = mt_rand(1, $max_value);
		$operend_2 = mt_rand(1, $max_value);
		$operand=$config['owncaptcha_totaloperand'];
		if($operand == 3){
			$operend_3 = mt_rand($negativ, $max_value);
		}

        if ($config['owncaptcha_calculationtype'] == 2) { // Subtraction
            if ($config['owncaptcha_subtractionans'] == 1) {
                $ans = $operend_1 - $operend_2;
                if ($ans < 0) {
                    $one = $operend_2;
                    $operend_2 = $operend_1;
                    $operend_1 = $one;
                }
                if ($operand == 3) {
                    $ans = $operend_1 - $operend_2 - $operend_3;
                    if ($ans < 0) {
                        if ($operend_1 < $operend_2) {
                            $one = $operend_2;
                            $operend_2 = $operend_1;
                            $operend_1 = $one;
                        }
                        if ($operend_1 < $operend_3) {
                            $one = $operend_3;
                            $operend_3 = $operend_1;
                            $operend_1 = $one;
                        }
                    }
                }
            }
        }

        if ($tcalc == 0)
            $tcalc = mt_rand(1, 2);

		if($tcalc == 1) // Addition
		{
		if($session->get('jsticket_rot13', null, 'jsticket_checkspamcalc') == 1) // ROT13 coding
		{
		    if($operand == 2)
		    {
			$session->set('jsticket_spamcheckresult', str_rot13(base64_encode($operend_1 + $operend_2)), 'jsticket_checkspamcalc');
		    }
		    elseif($operand == 3)
		    {
			$session->set('jsticket_spamcheckresult', str_rot13(base64_encode($operend_1 + $operend_2 + $operend_3)), 'jsticket_checkspamcalc');
		    }
		}
		else
		{
		    if($operand == 2)
		    {
			$session->set('jsticket_spamcheckresult', base64_encode($operend_1 + $operend_2), 'jsticket_checkspamcalc');
		    }
		    elseif($operand == 3)
		    {
			$session->set('jsticket_spamcheckresult', base64_encode($operend_1 + $operend_2 + $operend_3), 'jsticket_checkspamcalc');
		    }
		}
		}
		elseif($tcalc == 2) // Subtraction
		{
		if($session->get('jsticket_rot13', null, 'jsticket_checkspamcalc') == 1)
		{
		    if($operand == 2)
		    {
			$session->set('jsticket_spamcheckresult', str_rot13(base64_encode($operend_1 - $operend_2)), 'jsticket_checkspamcalc');
		    }
		    elseif($operand == 3)
		    {
			$session->set('jsticket_spamcheckresult', str_rot13(base64_encode($operend_1 - $operend_2 - $operend_3)), 'jsticket_checkspamcalc');
		    }
		}
		else
		{
		    if($operand == 2)
		    {
			$session->set('jsticket_spamcheckresult', base64_encode($operend_1 - $operend_2), 'jsticket_checkspamcalc');
		    }
		    elseif($operand == 3)
		    {
			$session->set('jsticket_spamcheckresult', base64_encode($operend_1 - $operend_2 - $operend_3), 'jsticket_checkspamcalc');
		    }
		}
		}
		$add_string="";
		$add_string .= '<div><label for="'.$session->get('jsticket_spamcheckid', null, 'jsticket_checkspamcalc').'">';

		$add_string .= '&nbsp;';

		if($tcalc == 1)
		{
		    if($operand == 2)
		    {
			$add_string .= $operend_1.' '.JText::_('Plus').' '.$operend_2.' '.JText::_('Equals').' ';
		    }
		    elseif($operand == 3)
		    {
			$add_string .= $operend_1.' '.JText::_('Plus').' '.$operend_2.' '.JText::_('Plus').' '.$operend_3.' '.JText::_('Equals').' ';
		    }
		}
		elseif($tcalc == 2)
		{
		    if($operand == 2)
		    {
			$add_string .= $operend_1.' '.JText::_('Minus').' '.$operend_2.' '.JText::_('Equals').' ';
		    }
		    elseif($operand == 3)
		    {
			$add_string .= $operend_1.' '.JText::_('Minus').' '.$operend_2.' '.JText::_('Minus').' '.$operend_3.' '.JText::_('Equals').' ';
		    }
		}

		$add_string .= '</label>';
		$add_string .= '<input type="text" name="'.$session->get('jsticket_spamcheckid', null, 'jsticket_checkspamcalc').'" id="'.$session->get('jsticket_spamcheckid', null, 'jsticket_checkspamcalc').'" size="3" class="inputbox '.$this->random().' validate-numeric required" value="" required="required" />';
		$add_string .= '</div>';
		
		return $add_string;
	}
    public function random()	    {
		$pw = '';

		// first character has to be a letter
		$characters = range('a', 'z');
		$pw .= $characters[mt_rand(0, 25)];

		// other characters arbitrarily
		$numbers = range(0, 9);
		$characters = array_merge($characters, $numbers);

		$pw_length = mt_rand(4, 12);

		for($i = 0; $i < $pw_length; $i++)
		{
		    $pw .= $characters[mt_rand(0, 35)];
		}

		return $pw;
    }

}?>
