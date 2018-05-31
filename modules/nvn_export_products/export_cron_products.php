<?php
/* ########################################################################### */
/* ----------------    NVN Export Products PrestaShop module   --------------- */
/*                       Copyright 2013   Karel Falgenhauer                    */
/*                          http://www.netvianet.com/                          */
/*                           http://www.praotec.com/                           */
/*             Please do not change this text, remove the link,                */
/*          or remove all or any part of the creator copyright notice          */
/*                                                                             */
/*    Please also note that although you are allowed to make modifications     */
/*     for your own personal use, you may not distribute the original or       */
/*                 the modified code without permission.                       */
/*                                                                             */
/*     SELLING AND REDISTRIBUTION IS FORBIDDEN! DO NOT SHARE WITH OTHERS!      */
/*                  Download is allowed only from netvianet.com                */
/*                                                                             */
/*       This software is provided as is, without warranty of any kind.        */
/*           The author shall not be liable for damages of any kind.           */
/*               Use of this software indicates that you agree.                */
/*                                                                             */
/* ########################################################################### */

include(dirname(__FILE__) . '/../../config/config.inc.php');
include(dirname(__FILE__) . '/../../init.php');
include(dirname(__FILE__) . '/nvn_export_products.php'); 

if (!Configuration::get('EXPRODUCT_NVN_TOKENOF') AND substr(_COOKIE_KEY_, 34, 8) != Tools::getValue('token')){die;}

//ini_set('max_execution_time', 90);

$my_export = new nvn_export_products();
$output = '';
if($my_export->mutex()){
         $xml_path = dirname(__FILE__)."/download/nvn_products_export_".Configuration::get('EXPRODUCT_NVN_RND').".xml" ; 
         (file_exists($xml_path)) ?$lastexportime = filemtime($xml_path) :$lastexportime = 0;
         (time() - $lastexportime > Configuration::get('EXPRODUCT_NVN_MINTIME')*3600) ? $isPeriod = true : $isPeriod = false;
         $comlet = 1;
         if((int)(Configuration::get('EXPRODUCT_NVN_ALASTID')<>-1 OR Configuration::get('EXPRODUCT_NVN_CLASTID')<>-1 OR Configuration::get('EXPRODUCT_NVN_PLASTID')<>-1 OR $lastexportime == 0))
         {$comlet = 0;} 
         if($comlet == 0 OR $isPeriod){    
          $my_export->js_on  = (int)(Configuration::get('EXPRODUCT_NVN_JSON')); 
          Configuration::updateValue('EXPRODUCT_NVN_MUTEX', time());
          $next=Configuration::get('EXPRODUCT_NVN_NEXT');
          if($next==0){$output .= $my_export->exportAttributes();}
          $next=Configuration::get('EXPRODUCT_NVN_NEXT');
          if($next==1){$output .= $my_export->exportCategories();}
          $next=Configuration::get('EXPRODUCT_NVN_NEXT');
          if($next==2){$output .= $my_export->exportProducts();}
          Configuration::updateValue('EXPRODUCT_NVN_MUTEX',0);
          }else{
           echo  '<div class="alerte"> Export xml file is completed and interval for generating xml was not yet been reached. Delete the old export file or change the interval. (Minimum time limit) </div>'; 
          }
}
//echo $output;




echo "NVN Export Products (CRON) OK";

?>