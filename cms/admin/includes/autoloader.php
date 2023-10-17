<?php
$fileHandlers = array();
$fileHandlers[] = 'image';
$fileHandlers[] = 'pdf';
function autoLoader($className){
	chdir("../");
		$classDir=getcwd().'/lib/classes/';
	chdir("./admin");
	$classes = array();
	$classes['Database'] = 'Database.class.php';
	$classes['Cms'] = 'Cms.class.php';
	$classes['Permissions'] = 'Permissions.class.php';
	$classes['Feed'] = 'Feed.class.php';
	$classes['User'] = 'User.class.php';
	$classes['Post'] = 'Post.class.php';
	$classes['Comment'] = 'Comment.class.php';
	$classes['Section'] = 'Section.class.php';
	$classes['Validate'] = 'Validate.class.php';
	$classes['Logger'] = 'Logger.class.php';
	$classes['Crypt'] = 'Crypt.class.php';
	$classes['UserInfo'] = 'UserInfo.class.php';
	$classes['Social'] = 'Social.class.php';
	$classes['Twitter'] = 'Twitter.class.php';
	$classes['Group'] = 'Group.class.php';
	/*$classes['Image'] = 'Image.class.php';*/
	$classes['Html'] = 'Html.class.php';
	$classes['ChromePhp'] = 'ChromePhp.class.php';

	/*begin fileHandelers*/
	$classes['base_fileHandeler'] = '../fileHandlers/base_fileHandeler.class.php';
	$classes['pdf_fileHandeler'] = '../fileHandlers/pdf_fileHandeler.class.php';
	$classes['image_fileHandeler'] = '../fileHandlers/image_fileHandeler.class.php';

	if(array_key_exists($className,$classes)){
	// echo $classDir.$classes[$className];
		require_once($classDir.$classes[$className]);
		return true;
	}else{
		return false;
	}
}
spl_autoload_register('autoLoader');
