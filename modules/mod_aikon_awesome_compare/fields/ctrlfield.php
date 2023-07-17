<?php
/**
 * @package     Aikon Aikon Form
 *
 * @copyright   Copyright (C) 2014 Aikon CMS. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldCtrlfield extends JFormField {

    protected $type = 'ctrlfield';


    function __construct(){
        // load jQuery for j2.5
        $jversion = new JVERSION;
        $version  = $jversion->getShortVersion();
        $shortVersion = substr($version,0,1);

        if ($shortVersion ==2 ){
            $doc = JFactory::getDocument();
            $doc->addScript('//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js');
        }

    }

    /**
     * Method to get the radio button field input markup.
     *
     * @return  string  The field input markup.
     *
     * @since   11.1
     */
    protected function getInput()
    {
        $html = array();

        // Initialize some field attributes.
        $class     = !empty($this->class) ? ' class="radio ' . $this->class . '"' : ' class="radio"';
        $required  = $this->required ? ' required aria-required="true"' : '';
        $autofocus = $this->autofocus ? ' autofocus' : '';
        $disabled  = $this->disabled ? ' disabled' : '';
        $readonly  = $this->readonly;

        // Start the radio field output.
        $html[] = '<fieldset id="' . $this->id . '"' . $class . $required . $autofocus . $disabled . ' >';

        // Get the field options.
        $options = $this->getOptions();

        // Build the radio field output.
        foreach ($options as $i => $option)
        {
            // prep enable and disable props

            $enableString  = ' data-enable="';
            $enableString .= $option->enable;
            $enableString .= '" ';

            $disableString  = ' data-disable="';
            $disableString .= $option->disable;
            $disableString .= '" ';

            // add id attribute for later use
            $options[$i]->id = $this->id . $i;
            // Initialize some option attributes.
            $checked = ((string) $option->value == (string) $this->value) ? ' checked="checked"' : '';
            $class = !empty($option->class) ? ' class="' . $option->class . '"' : '';

            $disabled = !empty($option->disable) || ($readonly && !$checked);

            $disabled = $disabled ? ' disabled' : '';

            // Initialize some JavaScript option attributes.
            $onclick = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';
            $onchange = !empty($option->onchange) ? ' onchange="' . $option->onchange . '"' : '';

            $html[] = '<input type="radio" id="' . $this->id . $i . '" name="' . $this->name . '" value="'
                . htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '"' . $checked . $class . $required . $onclick
                . $onchange
                . $enableString . $disableString .' />';

            $html[] = '<label for="' . $this->id . $i . '"' . $class . ' >'
                . JText::alt($option->text, preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)) . '</label>';

            $required = '';
        }

        // End the radio field output.
        $html[] = '</fieldset>';


        $doc = JFactory::getDocument();
        $path = substr( __FILE__, strlen( $_SERVER[ 'DOCUMENT_ROOT' ] ) );

        $url = "{$_SERVER[ 'HTTP_HOST' ]}/{$path}";
        $url = str_replace('\\', '/', $url);
        $url = substr ($url, 0, -(strlen($this->type) + 4) );

        // add control js to head
        $doc->addScript('http://' .$url . 'ctrlfield/js/ctrlfield.js');

        //build control js object and load it to the global ctrlfield object
        $doc->addScriptDeclaration($this->makeJsMapObject($this->id, $options));

        // add jVersion info to the script
        $jversion = new JVERSION;
        $version = substr($jversion->getShortVersion(), 0, 1);
        $doc->addScriptDeclaration("if (typeof window.ctrlFieldJVersion == 'undefined'){window.ctrlFieldJVersion = {$version};}");

        return implode($html);
    }

    /**
     * Method to get the field options for radio buttons.
     *
     * @return  array  The field option objects.
     *
     * @since   11.1
     */
    protected function getOptions()
    {
        $options = array();

        foreach ($this->element->children() as $option)
        {
            // Only add <option /> elements.
            if ($option->getName() != 'option')
            {
                continue;
            }

            $disabled = (string) $option['disabled'];
            $disabled = ($disabled == 'true' || $disabled == 'disabled' || $disabled == '1');

            // Create a new option object based on the <option /> element.
            $tmp = JHtml::_(
                'select.option', (string) $option['value'], trim((string) $option), 'value', 'text',
                $disabled
            );

            // Set some option attributes.
            $tmp->class = (string) $option['class'];

            // Set some JavaScript option attributes.
            $tmp->onclick = (string) $option['onclick'];
            $tmp->onchange = (string) $option['onchange'];
            /* add the unique enable disable attributes */

            $tmp->enable = (string) $option['enable'];
            $tmp->disable = (string) $option['disable'];

              // Add the option object to the result set.
            $options[] = $tmp;
        }

        reset($options);

        return $options;
    }

    /*
     * make a js object representing a "map" of which option in the field does what
     *
     * @param
     * @param array $options - multi dimentional array of options, each having a name, and enable disable attributes as comma delimited strings
     */
    protected function makeJsMapObject($fieldName, $options = array() )
    {

        //make data structure we can use to make the script
        $map = array ();
        foreach ($options as $option){
            $map[$option->id] = array();
            $map[$option->id]['enable'] = array();
            $map[$option->id]['disable'] = array();

            $disable = explode (',', $option->disable);
            $enable = explode (',', $option->enable);

            foreach ($enable as $add) {
                if ($add != ''){
                    $map[$option->id]['enable'][] = $add;
                }

            }
            foreach ($disable as $add) {
                if ($add != ''){
                    $map[$option->id]['disable'][] = $add;
                }
            }
        }

       // make script
        $script  = "\n window.ctrlFieldGlobal.{$fieldName} = { \n";

        // for each option
        foreach ($map as $key => $item ){
            $script  .= "'#{$key}' : { \n";

            //add enable array to item
            $script  .= "enable : [";
            $flag = false;  // if we have an enable, we will flag. then, we will remove the extra ',' from end of string. if we just remove anyway->error
            foreach ($item['enable'] as $enable){
                $flag = true;
                $script  .= "'#jform_params_{$enable}', ";
            }
            if ($flag){
                $script = substr($script,0 , -2); // remove last ", " (notice space!)
            }

            $script  .= "], \n";              // close the array, add ,\n to go on to the disable array


            //add enable array to item
            $script  .= "disable : [";
            $flag = false;  // if we have an enable, we will flag. then, we will remove the extra ',' from end of string. if we just remove anyway->error
            foreach ($item['disable'] as $disable){
                $flag = true;
                $script  .= "'#jform_params_{$disable}', ";
            }
            if ($flag){
                $script = substr($script,0 , -2); // remove last ", " (notice space!)
            }

            $script  .= "] \n";              // close the array, add \nNOTICE: no comma, this is the last one

            $script  .= " }, \n"; //close the -item-
        }

        // remove last ',' from after the items, which is a syntax error
        $script = substr($script,0 , -3);
        $script .= "\n";

        $script .= "}; \n"; // close the ctrlfieldglobal.fieldname object

        return $script;
    }
}




