<?php
/**
 * @package     mod_aikon_awesome_compare
 *
 * @copyright   Copyright (C) 2014 aikon CMS, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

?>
<!-- begin aikon Awesome Compare -->

<style>
/* dynamic style for this specific instance */
<?php if ($handleType  == 'css') :?>
/* handle CSS */
#<?php echo $uniqueId;?>.aikon-compare .cd-handle{
    background-color: <?php echo $handleColor;?>;
    transition: background 0.3s ease;
    -webkit-transition: background 0.3s ease;
    -moz-transition: background 0.3s ease;
    -ms-transition: background 0.3s ease;
    -o-transition: background 0.3s ease;
}

#<?php echo $uniqueId;?>.aikon-compare .cd-handle.draggable {
    background-color: <?php echo $handleColorActive;?> !important;
    transition: background 0.3s ease;
    -webkit-transition: background 0.3s ease;
    -moz-transition: background 0.3s ease;
    -ms-transition: background 0.3s ease;
    -o-transition: background 0.3s ease;
}

#<?php echo $uniqueId;?>.aikon-compare .cd-handle:hover {
    background-color: <?php echo $handleColorHover;?>;
    transition: background 0.3s ease;
    -webkit-transition: background 0.3s ease;
    -moz-transition: background 0.3s ease;
    -ms-transition: background 0.3s ease;
    -o-transition: background 0.3s ease;
}

<?php endif; ?>
/* container styles */
<?php echo $containerStyles;?>

</style>


<div id="<?php echo $uniqueId?>" class="aikon-compare" >
    <div class="cd-image-container">
        <img src="<?php echo $firstImageSrc;?>" alt="Original Image">

        <div class="cd-resize-img"> <!-- the resizable image on top -->
            <img src="<?php echo $secondImageSrc;?>" alt="Modified Image">
        </div>

        <span class="<?php echo $handleClass;?>"></span> <!-- slider handle -->
    </div> <!-- cd-image-container -->
</div>
<!-- end aikon Awesome Compare -->

