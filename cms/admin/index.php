<?php
	include('includes/config.php');
	CleanRequestVars();
	CheckLogin();
	$user = getUser();
	$action = Get('action',false);
	if($action){
		switch($action){
			case 'logout':
				logout();
				break;
		    case 'password':
                echo str_repeat(md5(uniqid(mt_rand(),true)), 1500);  
        }
	}

    $sql = "SELECT COUNT(*) AS c FROM users WHERE `enabled`=0";
    if (($unapproved = $database->getRows($sql)) === false) echo mysql_error() . "\ncannot get unapproved user count.";
    $unapproved = $unapproved[0]['c'];

    $sql = "SELECT COUNT(*) AS c FROM users WHERE `joinDate`>".(time()-604800);
    if (($recent = $database->getRows($sql)) === false) echo mysql_error() . "\n cannot get recent user count.";
    $recent = $recent[0]['c'];

    $sql = 'SELECT MAX(date) AS date FROM sections';
    if (($update = $database->getRows($sql)) == false) echo mysql_error() . "\n cannot get section dates.";
    $update = date('m/d/Y' , $update[0]['date']);

	include('includes/header.php'); ?>
            <section class="homeContent">
                <?php 
                if(tabDisabled('feeds')==false){
                    if ($cms->getFeedCount() > 0) { ?>
                <div class="help<?php echo((!$cms->getSetting('feedsTab'))?" evil ":"")?>">
                    <a href="feed.php" class="imageButton"><img src="assets/images/manage_posts.png" width="" height="" alt="user icon"/></a>
                    <h2><a href="feed.php">Manage your Feeds</a></h2>
                    <p>Click here to easily manage any feed based content</p>
                </div><!--help end-->
                <?php }
                }
                if(tabDisabled('sections')==false){
                    if ($cms->getSectionCount() > 0) { ?>
                <div class="help<?php echo((!$cms->getSetting('sectionsTab'))?" evil ":"")?>">
                    <a href="section.php" class="imageButton"><img src="assets/images/manage_sections.png" width="" height="" alt="user icon"/></a>
                    <h2><a href="section.php">Manage your sections</a></h2>
                    <p>You last updated your sections on <strong><?php echo $update; ?></strong>, would you like to manage that now?</p>
                </div><!--help end-->
                <?php }
                }
                if(tabDisabled('settings')==false){ ?>
                <div class="help<?php echo((!$cms->getSetting('settingsTab'))?" evil ":"")?>">
                    <a href="settings.php" class="imageButton"><img src="assets/images/manage_settings.png" width="" height="" alt="user icon"/></a>
                    <h2><a href="settings.php">Change your CMS settings</a></h2>
                    <p>You can go here to control and fine tune the settings and social integration for your cms.</p>
                </div><!--help end-->
                <?php }
                if(tabDisabled('users')==false){ ?>
                <div class="help<?php echo((!$cms->getSetting('usersTab'))?" evil ":"")?>">
                    <a href="user.php" class="imageButton"><img src="assets/images/manage_users.png" width="" height="" alt="user icon"/></a>
                    <h2><a href="user.php">Manage your user base</a></h2>
                    <p>You currently have <strong><?php echo $recent; ?></strong> new members, <strong><?php echo $unapproved; ?></strong> of which are waiting for activation.</p>
                </div><!--help end-->
                <?php } ?>
            </section><!--home end-->
<?php include('includes/footer.php'); ?>
