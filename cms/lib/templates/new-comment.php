<?php
$settings = new Post(Get('pid'));
$settings =$settings->getFeed();
$settings =$settings->getSettings();
if($cms->getSetting('comments')){
    if($settings['comments']){
        if ($cms->getSetting('anonComments')) {
            include('new_comment_anon.php');
        }else{
            if (isset($_SESSION['user_obj'])) {
                $user = $_SESSION['user_obj'];
                $user->refresh();
                if($user->isEnabled()){
                    include('new_comment_user.php');
                }else{
                    include('new_comment_account_disabled.php');
                }
            }else{
                include('new_comment_login.php');
            }
        }
    }else{
        include('new_comment_disabled.php');
    }
}else{
    include('new_comment_disabled.php');
}

?>
