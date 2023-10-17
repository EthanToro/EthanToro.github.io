<?php
include('includes/config.php');
CleanRequestVars();
CheckLogin();
$user = getUser();
if(tabDisabled('settings')) kickToCurb();
$action = Get('action','settings');
$output = '';
$hasReset = false;
switch ($action) {
    case 'addFeed': 
        if (Post('submit', false) == 'Create')
            addFeed();
        addFeedView();
        break;
    case 'tabvisibility':
        if (Post('submit', false) == 'Set')
            setVisibility();
            setVisibilityView();
        break;
    case 'addSection':
        if (Post('submit', false) == 'Create')
            addSection();
        addSectionView();
        break;
	//case 'settings':
    case 'deleteGroup':
        
        if (Post('submit', false) == 'Delete')
            deleteSection();
        deleteSectionView();
        
        break;
    default:
	$hasReset=true;
	   if (Post('submit', false) !== false)
            settingsSubmit();
        settingsMain();
        break;
    /*
    default:
        kickToCurb();
    */
}

function deleteSectionView() {
    global $output, $message, $cms;

    ob_start();
    ?>
        <h1>Delete Section Group</h1>

        <?php if (isset($message) && $message) echo $message; ?>
        <form enctype="multipart/form-data" action="settings.php?action=deleteGroup" method="POST">
            <label for="group">Which group would you like to remove?</label>
            <br /><select name="group">
                <?php
                    $groups = $cms->getSectionGroups();
                    foreach ($groups as $og) {
                        if ($og->canDestroy()) {
                            echo "<option value='{$og->getId()}'>{$og->getName()}</option>";
                        }
                    }
                ?>
            </select>
            
            <hr />

            <input type="submit" name="submit" value="Delete" />
            <p class="clear"></p>
    <?php
    $output = ob_get_clean();
}

function deleteSection() {
    global $message, $cms;

    $gid = Post('group');
    $group = $cms->getSectionGroup($gid);
    if ($group->canDestroy())
        $group->destroy();

    $message = '<p class="success">Successfully deleted section group</p>';
}

function addFeedView() {
    global $output, $message,$cms;

    ob_start();
    ?>
		<h1>New Feed</h1>
		
		<?php if (isset($message) && $message) echo $message; ?>
		<form enctype="multipart/form-data" action="settings.php?action=addFeed" method="POST">
			<label for="name">What is the name of your new feed?</label>
            <br /><input type="text" name="name" />

            <hr />
            
            <input type="submit" name="submit" value="Create" />
			<p class="clear"></p>
		</form>
	<?php
    $output = ob_get_clean();
}

function addFeed() {
    global $cms, $message;

    $name = Post('name', false);
    if ($name == false)
        $message = '<p>Please supply a name for the new feed</p>';
    else {
        Feed::create($name);
        header('Location: feed.php');
    }
}

function addSectionView() {
    global $output, $message,$cms;

    $groups = $cms->getSectionGroups();

    ob_start();
    ?>
		<h1>New Section</h1>
		
		<?php if (isset($message) && $message) echo $message; ?>
		<form enctype="multipart/form-data" action="settings.php?action=addSection" method="POST">
            <label for="name">What is the name of your new section?</label><br />
			<input type="text" name="name" /><br>
            <?php /*
			<label for="name">What type of section?</label><br />
				<input type="radio" name="group1" value="0" checked>Editor<br>
				<input type="radio" name="group1" value="1">Simple text<br>
				<input type="radio" name="group1" value="2">Image<br>
            */ ?>
            <br>
            <label for="gname">What group would you like your new section in?</label>
            <br />
            <select id='ajaxReload_gname' name="gname">
                <?php
                $groups = $cms->getSectionGroups();
                foreach($groups as $og){
                    echo "<option value='".$og->getId()."' >".$og->getName()."</option>";
                }                   
                ?>
            </select>
            <a href="#" onclick="newSectionGroup_js();">[+]</a>
            <hr />

            <input type="submit" name="submit" value="Create" />
			<p class="clear"></p>
		</form>
    <?php
    $output = ob_get_clean();
}

function addSection() {
    global $cms, $message;

    $name = Post('name', false);
    $gname = Post('gname', false);
    if (($name == false)||($gname == false))
        $message = '<p>Please supply a name and group name for the new section</p>';
    else {
	$fields = array('name'=>$name,'group'=>$gname);
        Section::create($fields);
        header('Location: section.php');
    }
}

function setVisibilityView() {
    global $database, $output, $cms, $message, $user;

    $settings = $cms->getSettings();

    ob_start();
    ?>
                <h1>Tab Settings</h1>

                
                <?php if (isset($message) && $message) echo $message; ?>
                <form enctype="multipart/form-data" action="settings.php?action=tabvisibility" method="POST">

                    <label for="registration">Feed tabs</label>
                    <br /><input type="radio" name="feedsTab" value="1" <?php echo (($settings['feedsTab'])? 'checked="checked"' : ''); ?>/> Enable
                    <br /><input type="radio" name="feedsTab" value="0" <?php echo ((!$settings['feedsTab'])? 'checked="checked"' : ''); ?>/> Disable
                    <hr />

                    <label for="registration">Sections tab</label>
                    <br /><input type="radio" name="sectionsTab" value="1" <?php echo (($settings['sectionsTab'])? 'checked="checked"' : ''); ?>/> Enable
                    <br /><input type="radio" name="sectionsTab" value="0" <?php echo ((!$settings['sectionsTab'])? 'checked="checked"' : ''); ?>/> Disable
                    <hr />

                    <label for="registration">Comments tab</label>
                    <br /><input type="radio" name="commentsTab" value="1" <?php echo (($settings['commentsTab'])? 'checked="checked"' : ''); ?>/> Enable
                    <br /><input type="radio" name="commentsTab" value="0" <?php echo ((!$settings['commentsTab'])? 'checked="checked"' : ''); ?>/> Disable
                    <hr />

                    <label for="registration">Notes tab</label>
                    <br /><input type="radio" name="notesTab" value="1" <?php echo (($settings['notesTab'])? 'checked="checked"' : ''); ?>/> Enable
                    <br /><input type="radio" name="notesTab" value="0" <?php echo ((!$settings['notesTab'])? 'checked="checked"' : ''); ?>/> Disable
                    <hr />
                        
                    <label for="registration">Settings tab</label>
                    <br /><input type="radio" name="settingsTab" value="1" <?php echo (($settings['settingsTab'])? 'checked="checked"' : ''); ?>/> Enable
                    <br /><input type="radio" name="settingsTab" value="0" <?php echo ((!$settings['settingsTab'])? 'checked="checked"' : ''); ?>/> Disable
                    <hr />
                    
                    <label for="registration">Users tab</label>
                    <br /><input type="radio" name="usersTab" value="1" <?php echo (($settings['usersTab'])? 'checked="checked"' : ''); ?>/> Enable
                    <br /><input type="radio" name="usersTab" value="0" <?php echo ((!$settings['usersTab'])? 'checked="checked"' : ''); ?>/> Disable
                    <hr /> 


                    <input type="submit" name="submit" value="Set" />
                <p class="clear"></p>

                </form>
    <?php
    $output = ob_get_clean();
}
function setVisibility() {
    global $cms, $validate, $message;

    $p = array();
    foreach($_POST as $k => $v)
        $p[$k] = Post($k);

    $cms->editSettings($p);
    $message = '<p class="success">Settings successfully changes</p>';
}


    function settingsMain() {
    global $database, $output, $cms, $message, $user;

    $settings = $cms->getSettings();

    ob_start();
    ?>
                <h1>Global Settings</h1>

                
                <?php if (isset($message) && $message) echo $message; ?>
                <form enctype="multipart/form-data" action="settings.php" method="POST">
                    <label for="anonComments">Anon comments on posts GLOBALLY!</label>
                    <br /><input type="radio" name="anonComments" value="1" <?php echo (($settings['anonComments'])? 'checked="checked"' : ''); ?>/> Enable
                    <br /><input type="radio" name="anonComments" value="0" <?php echo ((!$settings['anonComments'])? 'checked="checked"' : ''); ?>/> Disable
                    <hr />

                    <label for="comments">Commenting of posts GLOBALLY!</label>
                    <br /><input type="radio" name="comments" value="1" <?php echo (($settings['comments'])? 'checked="checked"' : ''); ?>/> Enable
                    <br /><input type="radio" name="comments" value="0" <?php echo ((!$settings['comments'])? 'checked="checked"' : ''); ?>/> Disable
                    <hr />
                    
                    <label for="comments">Rating of posts GLOBALLY!</label>
                    <br /><input type="radio" name="ratings" value="1" <?php echo (($settings['ratings'])? 'checked="checked"' : ''); ?>/> Enable
                    <br /><input type="radio" name="ratings" value="0" <?php echo ((!$settings['ratings'])? 'checked="checked"' : ''); ?>/> Disable
                    <hr />

                    <label for="registration">User registration</label>
                    <br /><input type="radio" name="registration" value="1" <?php echo (($settings['registration'])? 'checked="checked"' : ''); ?>/> Enable
                    <br /><input type="radio" name="registration" value="0" <?php echo ((!$settings['registration'])? 'checked="checked"' : ''); ?>/> Disable
                    <hr />
                    
                    <label for="coolBackgrounds">Cool backgrounds for Dev</label>
                    <br /><input type="radio" name="coolBackgrounds" value="1" <?php echo (($settings['coolBackgrounds'])? 'checked="checked"' : ''); ?>/> Enable
                    <br /><input type="radio" name="coolBackgrounds" value="0" <?php echo ((!$settings['coolBackgrounds'])? 'checked="checked"' : ''); ?>/> Disable
                    <hr />
                    <?php /*
                    <hr />
                    <h3>Social Settings</h3>

                    <br/><br/>

                    <label>Facebook integration</label>
                    <br /><label for="fb_username">Username:</label>
                        <input type="text" name="fb_username" />
                    <br /><label for="fb_password">Password:</label>
                        <input type="text" name="fb_password" />
                    <br /><input type="radio" name="fb_active" value="1" /> On
                    <br /><input type="radio" name="fb_active" value="0" /> Off

                    <br/><br/><br/>

                    <label>Twitter integration</label>
                    <br /><label for="tw_username">Username:</label>
                        <input type="text" name="tw_username" />
                    <br /><label for="tw_password">Password:</label>
                        <input type="text" name="tw_password" />
                    <br /><input type="radio" name="tw_active" value="1" /> On
                    <br /><input type="radio" name="tw_active" value="0" /> Off

                    <br/><br/><br/>
                
                    <label>Linked In integration</label>
                    <br /><label for="li_username">Username:</label>
                        <input type="text" name="li_username" />
                    <br /><label for="li_password">Password:</label>
                        <input type="text" name="li_password" />
                    <br /><input type="radio" name="li_active" value="1" /> On
                    <br /><input type="radio" name="li_active" value="0" /> Off

                    <br/><br/><br/>

                    */ ?>
                    <hr/>

                    <input type="submit" name="submit" value="Submit" />
                    <p class="clear"></p>

                </form>
    <?php
    $output = ob_get_clean();
}

function settingsSubmit() {
    global $cms, $validate, $message;

    $p = array();
    foreach($_POST as $k => $v)
        $p[$k] = Post($k);

    $cms->editSettings($p);
    $message = '<p class="success">Settings successfully changes</p>';
}


include('includes/header.php');
?>
            <script src="http://js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>
            <script type="text/javascript">bkLib.onDomLoaded(nicEditors.allTextAreas);</script>


            <aside class="sidebar">
                <a href="index.php">Home</a>
                <?php /* if($hasReset){?> <a href="#">Restore Defaults</a><?php } */ ?>
                <?php if($user->getPermissions()->getAccessLevel()==2) { ?>
                <a href="settings.php?action=addFeed">Add Feed</a>
                <a href="settings.php?action=addSection">Add Sections</a>
                <a href="settings.php?action=deleteGroup">Delete Section Group</a>
                <a href="settings.php">General Settings</a>
                <a href="settings.php?action=tabvisibility">Tab visibility</a>
                <?php } ?>
            </aside>

            <section class="content">
			<?php
				echo $output;
			?>
			</section>
			<?php
include('includes/footer.php'); ?>
