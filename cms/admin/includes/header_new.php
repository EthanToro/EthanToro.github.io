<?php
    $uri = strtolower($_SERVER['REQUEST_URI']);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf8" />
    <title>Synergy Cms</title>

    <link href="assets/css/main.css" rel="stylesheet" type="text/css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
    <script src="assets/js/main.js" type="text/javascript"></script>
    
    <?php if(defined('DEVenviroment')) echo '<script src="assets/js/background-changer.js" type="text/javascript"></script>'; ?>
    <!--[if IE]>
        <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js" type="text/javascript"></script>
    <![endif]-->
</head>

<!--Synergy Networks Content Management Backend-->
<body>
    <div id="wrapper">
        <a href='index.php?action=logout' id='logout'>Logout</a>
        <a href="index.php" class="logo">Synergy CMS</a>
        <nav>
<?php           if($cms->getSetting('feedsTab')){
                    if ($cms->getFeedCount() > 0) { 
                        echo '<a href="feed.php" class="'.((!$cms->getSetting('feedsTab'))?" evil ":"").'tab feed '.((!$cms->getSetting('feedsTab'))?" evil ":"").((strpos($uri, 'feed.php')||strpos($uri, 'post.php'))?' selected':'').'">'.(($cms->getFeedCount()>1)?'Feeds':'Feed').'</a>';

                     }
                }
                if($cms->getSetting('sectionsTab')){
                    if ($cms->getSectionCount() > 0) { 
                        echo '<a href="section.php" class="'.((!$cms->getSetting('sectionsTab'))?" evil ":"").'tab section '.((strpos($uri, 'section.php'))?' selected':'').'">Sections</a>';
                    } 
                }

                if($cms->getSetting('commentsTab')){
                    echo'<a href="comments.php" class="'.((!$cms->getSetting('commentsTab'))?" evil ":"").'tab comment'. ((strpos($uri, 'user'))?' selected':'').'">Comments</a>';
                }
                if($cms->getSetting('usersTab')){ 
                    echo'<a href="user.php" class="'.((!$cms->getSetting('usersTab'))?" evil ":"").'tab user'. ((strpos($uri, 'user'))?' selected':'').'">Users</a>';
                }

                if($cms->getSetting('notesTab')){ 
                    echo '<a href="notes.php" class="'. ((!$cms->getSetting('notesTab'))?" evil ":"").'tab notes'.((strpos($uri, 'notes.php'))?' selected':'').'">Notes</a>';
                }

                if($cms->getSetting('settingsTab')){ 
                    echo '<a href="settings.php" class="'.((!$cms->getSetting('settingsTab'))?" evil ":"").'tab settings'.((strpos($uri, 'settings.php'))?' selected':'').'">Settings</a>';
                }
                if($user->getPermissions()->getAccessLevel()==2){
                ?><ul class='tab dev dropdown'>    
                    <li>
                        Dev
                        <ul>
                            <?php
                                if($cms->getSetting('feedsTab')==false){
                                    if ($cms->getFeedCount() > 0) { 
                                        echo '<li><a href="feed.php">'.(($cms->getFeedCount()>1)?'Feeds':'Feed').'</a></li>';

                                     }
                                }
                                if($cms->getSetting('sectionsTab')==false){
                                    if ($cms->getSectionCount() > 0) { 
                                        echo '<li><a href="section.php">Sections</a></li>';
                                    } 
                                }

                                if($cms->getSetting('usersTab')==false){
                                    echo'<li><a href="user.php">Users</a></li>';
                                }

                                if($cms->getSetting('notesTab')==false){ 
                                    echo '<li><a href="notes.php" >Notes</a></li>';
                                }

                                if($cms->getSetting('settingsTab')==false){ 
                                    echo '<li><a href="settings.php" >Settings</a></li>';
                                }
                            ?>
                        </ul>
                    </li>
                </ul>
                <?php } ?>
                <a href="index.php" class="tab home<?php echo (( strpos($uri, 'index.php') || preg_match('/\/$/', $uri) )?' selected':''); ?>">Home</a>
        </nav>


        <section id="main">
