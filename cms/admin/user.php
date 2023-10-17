<?php
include('includes/config.php');
CleanRequestVars();
CheckLogin();
$user = getUser();
$action = Get('action');
$page = Get('page');
$uid = Get('uid');

$output = '';

if(tabDisabled('users')) kickToCurb();
switch ($action) {
case 'new':
    if (Post('submit', false))
        userNew();
    userNewView();
    break;
case 'edit':
    if (Post('submit', false))
        userEdit();
    userEditView();
    break;
    //case 'view':
    //	userView();
    //	break;
case 'delete':
    userDelete();
    break;
case 'deleteS':
    usersDelete();
    break;
default:
    usersMain();
    break;
}
function usersDelete() {
    $users = Post('users');
    foreach ($users as $user){
        $u = new User($user);
        $u->destroy();
    }
    header('Location: user.php');
}

// The view for creating a new user
function userNewView() {
    global $output, $cms, $message,$user;

    ob_start();
?>
            <script src="http://js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>
            <script type="text/javascript">bkLib.onDomLoaded(nicEditors.allTextAreas);</script>

            <aside class="sidebar">
                <a href="user.php">Back</a>
                <?php //<a href="#">Reset</a> ?>
                <a href="user.php?action=new">New User</a>
            </aside>

            <section class="content">
                <h1>Create a User</h1>

                <?php if (isset($message) && $message) echo $message; ?>
                <h3>New User</h3>
                    <form enctype="multipart/form-data" action="user.php?action=new" method="POST">
                    <label for="username">Username</label>
                    <br /><input type="text" name="username" value="<?php echo Post('username'); ?>" />

                    <br /><br /><label for="email">Email</label>
                    <br /><input type="text" name="email" value="<?php echo Post('email'); ?>" />

                    <br /><br /><label for="name">Name</label>
                    <br /><input type="text" name="name" value="<?php echo Post('name'); ?>" />

                    <br /><br /><label for="password1">Password</label>
                    <br /><input type="password" name="password1" placeholder="password"/>
                    <br /><label for="password2">Confirm Password</label>
                    <br /><input type="password" name="password2" placeholder="confirm password" />

                    <br /><br /><label for="accessLevel">Access Level</label>
                    <br /><select name="accessLevel">
                        <option value="0"<?php echo ((Post('accessLevel') == 0) ? ' selected="selected"' : '' ); ?>>User</option>
                        <option value="1"<?php  echo ((Post('accessLevel') == 1) ? ' selected="selected"' : '' ); ?>>Admin</option>
                        <?php if($user->getPermissions()->getAccessLevel()>=2){?>
                            <option value="2"<?php echo ((Post('accessLevel') == 2) ? ' selected="selected"' : '' ); ?>>Developer</option>
                        <?php } ?>

                    </select>

                    <div class="options">
                        <label>Options</label>
                       <?php if($cms->getSetting('comments')){?><br /><input type="checkbox" name="canComment" value="1" <?php echo ((Post('canComment')) ? 'checked="checked"' : '' ); ?>/> Allow this user to post comments <?php } ?>
                        <br /><input type="checkbox" name="enabled" value="1" <?php echo ((Post('enabled')) ? 'checked="checked"' : '' ); ?>/> Enable this user's account
                    </div><!--options end-->
                    <input type="submit" name="submit" value="Submit" />
                    <p class="clear"></p>

                </form>
            </section><!--content end-->
<?php
    $output = ob_get_clean();
}

// The processing of new users
function userNew() {
    global $cms, $message, $validate,$user;

    $errors = array();

    $p = array();
    foreach($_POST as $k => $v)
        $p[$k] = Post($k);

    if($p['accessLevel']>$user->getPermissions()->getAccessLevel()) kickToCurb();

    if (!isset($p['enabled']))
        $p['enabled'] = 0;
    if (!isset($p['canComment']))
        $p['canComment'] = 0;

    $requiredFields = array(
        'username',
        'email',
        'password1',
        'name'
    );

    $p['email'] = $validate->email($p['email'], true);

    foreach($p as $field => $value) {
        if ($value == false && in_array($field, $requiredFields))
            $errors[] = 'The ' . $field . ' field was left blank or is invalid.';
    }

    if ((!isset($p['password2']) || !isset($p['password1']))||($p['password1'] != $p['password2']))
        $errors[] = 'The supplied passwords do not match.';

    // Process the stuff
    if (count($errors)) {
        $message = '<ul class="error">';
        foreach ($errors as $error)
            $message .= '<li>' . $error . '</li>';
        $message .= '</ul>';

    } else {
        $user = $cms->getUser();

        $user->create($p);
        $user->setPassword($p['password1']);

        header('Location: user.php');
    }
}

// View function for editing a user
function userEditView() {
    global $output, $cms, $message, $uid,$user;

    $pageUser = $cms->getUser($uid);
    if($pageUser->getPermissions()->getAccessLevel()>$user->getPermissions()->getAccessLevel()) kickToCurb();
    ob_start();
?>
            <script src="http://js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>
            <script type="text/javascript">bkLib.onDomLoaded(nicEditors.allTextAreas);</script>

            <aside class="sidebar">
                <a href="user.php">Back</a>
                <?php //<a href="#">Reset</a> ?>
                <a href="comments.php?action=view&uid=<?php echo $uid; ?>">View Comments</a>
                <a href="user.php?action=new">New User</a>
            </aside>

            <section class="content">
                <h1>Edit User</h1>

                <?php if (isset($message) && $message) echo $message; ?>
                <h3><?php echo $pageUser->getUsername(); ?></h3>
                    <form enctype="multipart/form-data" action="user.php?action=edit&uid=<?php echo $uid; ?>" method="POST">
                    <label for="username">Username</label>
                    <br /><input type="text" name="username" value="<?php echo $pageUser->getUsername(); ?>" />

                    <br /><br /><label for="email">Email</label>
                    <br /><input type="text" name="email" value="<?php echo $pageUser->getEmail(); ?>" />

                    <br /><br /><label for="name">Name</label>
                    <br /><input type="text" name="name" value="<?php echo $pageUser->getName(); ?>" />

                    <br /><br /><label for="password1">Password</label>
                    <br /><input type="password" name="password1" placeholder="password"/>
                    <br /><label for="password2">Confirm Password</label>
                    <br /><input type="password" name="password2" placeholder="confirm password" />

                    <br /><br /><label for="accessLevel">Access Level</label>
                    <br /><select name="accessLevel">
                        <option value="0"<?php echo (($pageUser->getPermissions()->getAccessLevel() == 0) ? ' selected="selected"' : '' ); ?>>User</option>
                        <option value="1"<?php echo (($pageUser->getPermissions()->getAccessLevel() == 1) ? ' selected="selected"' : '' ); ?>>Admin</option>
                        <?php if($user->getPermissions()->getAccessLevel()>=2){?>
                            <option value="2"<?php echo (($pageUser->getPermissions()->getAccessLevel() == 2) ? ' selected="selected"' : '' ); ?>>Developer</option>
                        <?php } ?>

                    </select>

                    <div class="options">
                        <label>Options</label>
                        <?php if($cms->getSetting('comments')){?><br /><input type="checkbox" name="canComment" value="1" <?php echo (($pageUser->getCanComment()) ? 'checked="checked"' : '' ); ?>/> Allow this user to post comments<?php } ?>
                        <br /><input type="checkbox" name="enabled" value="1" <?php echo (($pageUser->isEnabled()) ? 'checked="checked"' : '' ); ?>/> Enable this user's account
                    </div><!--options end-->
                    <input type="submit" name="submit" value="Submit" />
                    <p class="clear"></p>

                </form>
            </section><!--content end-->
<?php
    $output = ob_get_clean();
}

// Processing of user editing
function userEdit() {
    global $cms, $message, $validate, $uid,$user;

    $errors = array();

    $p = array();
    foreach($_POST as $k => $v)
        $p[$k] = Post($k);

    if($p['accessLevel']>$user->getPermissions()->getAccessLevel()) kickToCurb();

    if (!isset($p['enabled']))
        $p['enabled'] = 0;
    if (!isset($p['canComment']))
        $p['canComment'] = 0;

    $user = $cms->getUser($uid);
    $userInfo = $user->getAll();

    $requiredFields = array(
        'username',
        'email',
        'name'
    );

    $p['email'] = $validate->email($p['email'], true);

    $p = array_merge($userInfo, $p);

    foreach($p as $field => $value) {
        if ($value == false && in_array($field, $requiredFields))
            $errors[] = 'The ' . $field . ' field was left blank or is invalid.';
    }

    if (isset($p['password1']) && $p['password1']) {
        if (isset($p['password2']) && $p['password1'] === $p['password2'])
            $passChange = $p['password1'];
        else
            $errors[] = 'The supplied passwords do not match.';
    }

    // Process the stuff
    if (count($errors)) {
        $message = '<ul class="error">';
        foreach ($errors as $error)
            $message .= '<li>' . $error . '</li>';
        $message .= '</ul>';

    } else {
        $user->edit($p);

        if (isset($passChange))
            $user->setPassword($passChange);

        $message = '<p class="success">Successfully updated user.</p>';
        header('location: user.php');
    }

}

function userDelete() {
    global $uid;

    $puser = new User($uid);
    $puser->destroy();
    header('Location: user.php');
}
function usersMain() {
    global $database, $output, $cms,$user;
    if($user->getPermissions()->getAccessLevel()==2){
        $sql = "SELECT COUNT(*) AS c FROM users WHERE `enabled`=0";
    }else{
        $sql = "SELECT COUNT(*) AS c FROM users WHERE `enabled`=0 and accessLevel<=".$user->getPermissions()->getAccessLevel();
    }
    if (($unapproved = $database->getRows($sql)) === false) echo mysql_error() . "\n cannot get unapproved user count";
    $unapproved = $unapproved[0]['c'];

    if($user->getPermissions()->getAccessLevel()==2){
        $sql= "SELECT COUNT(*) AS c FROM users WHERE joinDate>".(time()-604800);
    }else{
        $sql= "SELECT COUNT(*) AS c FROM users WHERE joinDate>".(time()-604800)." and accessLevel<=".$user->getPermissions()->getAccessLevel();
    }
    if (($recent = $database->getRows($sql)) === false) echo mysql_error() . "\n cannot get recent user count";
    $recent = $recent[0]['c'];

    $page = Get('page');
    $userCount = $cms->getUserCount(($user->getPermissions()->getAccessLevel()==2));
    $usersPerPage = 25;

    $pageCount = (!($userCount%$usersPerPage)) ? $userCount/$usersPerPage : ceil($userCount/$usersPerPage);

    if ($page < 1)
        $page = 1;
    else if ($page > $pageCount)
        $page = $pageCount;




if(Get('sort',false)=='waiting'){
        $users = $cms->getDisabledUsers(($page-1)*$usersPerPage, $usersPerPage,'accessLevel desc');
}elseif(Get('sort',false)=='new'){
    $users = $cms->getNewUsers(($page-1)*$usersPerPage, $usersPerPage,'accessLevel desc');
}else{
    $users = $cms->getUsers(($page-1)*$usersPerPage, $usersPerPage,'accessLevel desc');
}




    ob_start();
?>
                <aside class="sidebar">
                    <?php //<a href="#">Hide Admins</a> ?>
                    <a href="user.php?action=new">New User</a>
                </aside>

    <script type="text/javascript">
    $(document).ready(function() {
        $("a.delete").click(function(e) {
            e.preventDefault();

            if (confirm('Are you sure that you would like to delete this user?'))
                window.location = $(this).attr('href');
        });
    });
    </script>

                <section class="content">
                    <h1>Manage Users</h1>

                    <p>You have <strong><?php echo $userCount; ?></strong> <a href='user.php'>users</a>. <strong><?php echo $recent; ?></strong> <a href='user.php?sort=new'>new</a>, <strong><?php echo $unapproved; ?></strong> <a href='user.php?sort=waiting'>waiting for approval</a>.</p>
                    <input type="submit" name="ds" value="Delete  Selected" />
                    <form action="user.php?action=deleteS" method="POST" onsubmit="return confirm('Are you sure that you would like to delete these users?')">
                    <table class="posts">
                        <tr>
                            <th>User Name</th>
                            <th>Join Date</th>
                            <th>Comments</th>
                            <th>Enabled</th>
                            <th>Actions</th>
                        </tr>

<?php
    $oldAccessLevel = -1;
    if(count($users)>0){  
        foreach ($users as $puser) {
            if($puser->getPermissions()->getAccessLevel()!=$oldAccessLevel){
                echo "<tr><td class='TableGroupHeader' colspan='5'>".accessLevelToName($puser->getPermissions()->getAccessLevel())." Accounts</td></tr>";
                $oldAccessLevel=$puser->getPermissions()->getAccessLevel();
            }
    ?>

                            <tr class="post">
                                <td><?php echo $puser->getUsername(); ?></td>
                                <td><?php echo $puser->getJoinDate("m/d/Y h:i:s A"); ?></td>
                                <td><?php echo $puser->getCommentCount(); ?></td>
                                <td><?php echo (($puser->isEnabled())? 'Yes' : 'No' ); ?></td>
                                <td class="actions">
                                    <a href="user.php?uid=<?php echo $puser->getId(); ?>&action=edit" class="edit">[Edit]</a>
                                    <?php /* <a href="user.php?uid=<?php echo $puser->getId(); ?>&action=view" class="details">[Details]</a> */ ?>
                                    <a href="user.php?uid=<?php echo $puser->getId(); ?>&action=delete" class="delete">[Delete]</a>
                                    <input type="checkbox" name="users[]" value="<?php echo $puser->getId(); ?>" />
                                </td>
                            </tr>
    <?php                  }
     }else{
 echo "<tr>  
    <td></td>
    <td colspan=3 style='text-align:center;'>There are no users that fit the filters</td>
    <td></td>";
}
      ?>
                    </table>
                    <input type="submit" name="ds" value="Delete  Selected" />
                    <?php if(count($users)>$usersPerPage){?>
                    <div class="pagination">
                        <a href="<?php echo 'user.php?page=' . ($page-1); ?>" class="previous">Previous</a>
                        <?php for ($i = 1; $i <= $pageCount; $i++) { ?>
                            <a href="<?php echo "user.php?page={$i}"; ?>" class="number<?php if ($i == $page) echo ' current'; ?>"><?php echo $i; ?></a>
                        <?php } ?>
                        <a href="<?php echo 'user.php?page=' . ($page+1); ?>" class="next">Next</a>
                    </div>
                    <?php } ?>
                    </form>
                </section><!--content end-->
<?php 
        $output = ob_get_clean();
}

include('includes/header.php');
echo $output;
include('includes/footer.php'); ?>
