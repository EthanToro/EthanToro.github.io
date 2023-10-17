<?php
include('includes/config.php');
CleanRequestVars();
CheckLogin();
$user = getUser();
$action = get('action');
$output = '';
$message = '';

if(tabDisabled('notes')) kickToCurb();
switch ($action) {
    default:
        if(Post('submit',false)){
            todoProcess();
        }
        todoView();
        break;
}

function todoProcess(){
    global $output, $cms;
    $todo = array('noteText'=>Post('content'));
    $cms->editSettings($todo);
}
function todoView() {
    global $output, $cms,$user;
    $todoText = $cms->getSetting('noteText');
    ob_start();
    ?>
    <script src="http://js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>
    <script type="text/javascript">
    $(document).ready(function(){
        new nicEditor({fullPanel : true}).panelInstance('content');
    });
    
    </script>
    <form method='post'>
        <h1 title='these notes are shared between you and the dev'>Shared Notes</h1>
        <?php if(!tabDisabled('notes')&&($user->getPermissions()->getAccessLevel()==2)) echo "<h3 class='evil' style='width:280px;'>The client can see these notes</h3>";?>
        <textarea id='content' name='content'><?php echo $todoText; ?></textarea>
        <input class='right' type='submit' name='submit' value='Save'/>
    </form>

    <?php
    $output = ob_get_clean();
}

include('includes/header.php');
echo $output;
include('includes/footer.php');
