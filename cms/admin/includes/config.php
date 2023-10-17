<?php 
//ADMIN CONFIG
/*
goes in /admin/includes/config.php
*/

require('includes/autoloader.php');
require('includes/general_functions.php');
require('includes/site_functions.php');
session_name ('SynergyCms_04933542e58c215f96ab58ffe6643397');
session_Start();
$siteName = 'Corey';
$siteBaseUrl = 'http://testing.coreycompressor.com/';
$uploadUrl = $siteBaseUrl.'cms/uploads/';
$uploadPath = '../uploads/';

define('SERVER', 'localhost');
define('USERNAME', 'coreycompressor');
define('PASSWORD', 'DellPEt320');
define('DATABASE', 'coreycompressor');
$cms = new Cms();
$crypt = new Crypt();
$validate = new Validate();
?>