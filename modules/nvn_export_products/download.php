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
/*                                                                             */
/*       This software is provided as is, without warranty of any kind.        */
/*           The author shall not be liable for damages of any kind.           */
/*               Use of this software indicates that you agree.                */
/*                                                                             */
/* ########################################################################### */
$soubor = 'http://www.netvianet.com/nvn_update/'.$_POST["voucher"].'/nvn_export_products.zip';
header("Content-Description: File Transfer"); 
header("Content-Type: application/force-download"); 
header("Content-Disposition: attachment; filename=".basename($soubor)); 
readfile ($soubor); 
?>