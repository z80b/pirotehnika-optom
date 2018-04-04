<?php
/**
* 2016 Revolution Slider
*
*  @author    SmatDataSoft <support@smartdatasoft.com>
*  @copyright 2016 SmatDataSoft
*  @license   private
*  @version   5.1.5
*  International Registered Trademark & Property of SmatDataSoft
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_5_1_6($object)
{
	$object->registerHook('displayFullWidthTop');
	$object->registerHook('displayFullWidthTop2');
	$object->registerHook('displayFullWidthBottom');
	$object->registerHook('displayStBlogHome');
	$object->registerHook('displayStBlogHomeTop');
	$object->registerHook('displayStBlogHomeBottom');
    return true;
}
