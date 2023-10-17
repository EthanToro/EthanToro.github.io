<?php
    include('includes/config.php');
    CleanRequestVars();
    CheckLogin();
    $user = getUser();
    if(tabDisabled('settings')) kickToCurb();
    $action = Get('action','NAN');
    $dir = Get('dir');
    $id = Get('id');

    switch ($action) {
    case 'moveSection':
        if($user->getPermissions()->getAccessLevel()!=2) kickToCurb();
        $needed = array($dir,$id);
        if (isnull($needed)) kickToCurb();
        moveSection($dir,$id);
        break;
    case 'makeSection':
        if($user->getPermissions()->getAccessLevel()!=2) kickToCurb();
        newSectionGroup();
        break;
    case 'deleteImage':
        deleteImage();
        break;
    default:
        //kickToCurb();
    }

    function newSectionGroup() {
        global $cms;

        $name = htmlentities(Get('name'));
        $group =Group::create($name);
        $groups = $cms->getSectionGroups();
        foreach($groups as $og){
            echo "<option value='".$og->getId()."' ". (($group->getId() == $og->getId())? 'selected="selected"' : '') .">".$og->getName()."</option>";
        }   
    }

    function deleteImage() {
        global $cms;
        $pid = Get('pid');
        $feed = new Feed(Get('bid'));
        $post = new Post($pid);
        if($post->hasFile()){
            $post->getFile()->destroy();
            $a = array('thumb'=>'');
            $post->edit($a);
        }
        
    }

    function moveSection($dir, $id) {
        global $cms, $database;
        if ($dir == 'up') {
            $g1 = $cms->getSectionGroup($id);

            $sort = $g1->getSort() - 1;
            $sql = "SELECT id FROM sectiongroups WHERE `sort`=$sort LIMIT 1";
            if (($result = $database->getRows($sql)) === false) die("Canot properly sort groups,\n" . mysql_error());
            $id2 = $result[0]['id'];

            $g1->sortDown();
            $cms->getSectionGroup($id2)->sortUp();
        } else if ($dir == 'down') {
            $g1 = $cms->getSectionGroup($id);

            $sort = $g1->getSort() + 1;
            $sql = "SELECT id FROM sectiongroups WHERE `sort`=$sort LIMIT 1";
            if (($result = $database->getRows($sql)) === false) die("Canot properly sort groups,\n" . mysql_error());
            $id2 = $result[0]['id'];

            $g1->sortUp();	
            $cms->getSectionGroup($id2)->sortDown();	
        }
    }
?>
