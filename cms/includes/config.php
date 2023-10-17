<?php
//CLIENT CONFIG
/*
goes in /includes/config.php
*/

@chdir('/home/coreycompressor/public_html/cms');
require('includes/autoloader.php');
require('includes/general_functions.php');
require('includes/site_functions.php');
session_name('SynergyCms_04933542e58c215f96ab58ffe6643397');
session_Start();
$siteName = 'Corey';
$siteBaseUrl = 'http://coreycompressor.com/';
$uploadUrl = $siteBaseUrl . 'cms/uploads/';
$uploadPath = '../uploads/';

define('SERVER', '172.16.1.119');
define('USERNAME', 'corey');
define('PASSWORD', '6tdnX7wQepWrVjV2');
define('DATABASE', 'corey');
$cms = new Cms();
$crypt = new Crypt();
$validate = new Validate();
?>