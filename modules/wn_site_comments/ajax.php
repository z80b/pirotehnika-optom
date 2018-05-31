<?php 
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');

require_once(dirname(__FILE__).'/wn_site_comments.php');
$request = wn_site_comments::sendRequest($_POST);

exit($request);