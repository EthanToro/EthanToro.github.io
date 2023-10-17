<?php
	include('includes/config.php');
	CleanRequestVars();
	// IF(Post('content',false))
		// var_dump(Post('content'));

	CheckLogin();
	if($cms->getSectionCount() == 0){ header('Location: index.php'); }
    $sid = Get('sid');
	$user = getUser();
	if(tabDisabled('sections')) kickToCurb();
	$action = Get('action');
	$output = '';
	$message = array(
		'content' => '',
		'type' => ''
	);

	switch ($action) {
		case 'edit':
			if (isset($_POST['submit']))
				sectionEdit();
			sectionEditMenu();
			break;
		/*case 'new':
			if (isset($_POST['submit']))
				sectionNew();
			sectionNewMenu();
            break;*/
		case 'delete':
			if($user->getPermissions()->getAccessLevel()!=2) kickToCurb();
            sectionDelete();
			break;
		case 'settings':
            if($user->getPermissions()->getAccessLevel()!=2) kickToCurb();
            if (isset($_POST['submit']))
                sectionSettings();
            sectionSettingsMenu();
			break;
			
		default:
			//sectionEditMenu(1);
			sectionsMain();
			break;
	}


	function sectionEditMenu() {
		global $database, $cms, $output, $message, $sid;

		$sid = intval($sid);
		$section = $cms->getSection($sid);
		$name = $section->getName();
		$content = $section->getContent();

		if ($name == '')
			header('Location: section.php');

		ob_start();
		?>
			<script src="http://js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>
            <script type="text/javascript">
			$(document).ready(function(){
				// new nicEditor({fullPanel : true,uploadURI: '<?php echo niceditUploadUri(); ?>'}).panelInstance('contentbox');
				new nicEditor({fullPanel : true}).panelInstance('contentbox');
			});
			
			</script>

            <h1>Edit Section, <?php echo $name; ?></h1>
            
            <?php if ($message['content'] !== '') { ?>
            <p class="<?php echo $message['type']; ?>"><?php echo $message['content']; ?></p>
            <?php } ?>
            <form enctype="multipart/form-data" action="section.php?action=edit&sid=<?php echo $sid; ?>" method="POST">
                <label for="content">Content</label>
                <textarea name="content" id='contentbox'><?php echo $content; ?></textarea>
                <br>
                <input type="submit" name="submit" value="Submit" />
                <p class="clear"></p>

            </form>
		<?php
		$output = ob_get_clean();
	}

	function sectionEdit() {
		global $database, $cms, $message, $sid;

		$section = $cms->getSection(intval($sid));
		
		$editArray = array(
			'content' => fixIt(post('content'))
		);
		
		$section->edit($editArray);

		$message['type'] = 'success';
		$message['content'] = 'You have successfully edited this section.';
	}

    function sectionSettingsMenu() {
        global $cms, $sid, $message, $output;

		$sid = intval($sid);
		$section = $cms->getSection($sid);
		$name = $section->getName();
		$group = $section->getGroup()->getID();

		if ($name == '')
			header('Location: section.php');

		ob_start();
		?>
            <h1>Edit Section Name, <?php echo $name; ?></h1>
            
            <?php if ($message['content'] !== '') { ?>
            <p class="<?php echo $message['type']; ?>"><?php echo $message['content']; ?></p>
            <?php } ?>
            <form enctype="multipart/form-data" action="section.php?action=settings&sid=<?php echo $sid; ?>" method="POST">
                <label for="name">Name:</label><br>
				<input type="text" name="name" value="<?php echo $name; ?>" /><br>
				<label for="gname">Group Heading:</label><br>
				<select id='ajaxReload_gname' name="gname">
				<?php
				$groups = $cms->getSectionGroups();
				foreach($groups as $og){
					echo "<option value='".$og->getId()."' ". (($group == $og->getId())? 'selected="selected"' : '') .">".$og->getName()."</option>";
				}					
				?>
				</select>
                <a href="#" onclick="newSectionGroup_js();">[+]</a>

                <hr />

                <input type="submit" name="submit" value="Submit" />
                <p class="clear"></p>

            </form>
		<?php
		$output = ob_get_clean();
    }


    function sectionSettings() {
        global $cms, $sid, $message;

        $name = Post('name');
        $gname = Post('gname');
        $section = $cms->getSection($sid);

        if (($name)&&($gname)) {
		$fields= array(
					"name"=>$name,
					"group"=>$gname
				);
			
             $cms->getSection($sid)->edit($fields);
			
            header('Location: section.php');
        } else {
            $message['content'] = 'Please enter a valid section name';
            $message['type'] = 'error';
        }
    }

    function sectionDelete() {
        global $cms, $sid;
		$sid = intval($sid);
        $section = $cms->getSection($sid);
        $section->destroy();
        header('Location: section.php');
    }

	function sectionNewMenu() {
	}

	function sectionNew() {
	}

	function sectionsMain() {
		global $cms, $output,$user;
		
        $groups = $cms->getSectionGroups();
		ob_start();
		?>
                <script type="text/javascript">
                    $(document).ready(function() {
                        $("a.delete").click(function(e) {
                            e.preventDefault();

                            if (confirm('Are you sure that you would like to delete this section?'))
                                window.location = $(this).attr('href');
                        });
                    });
                </script>
				<h1>Sections</h1>
				<table class="posts">
					<thead>
						<tr>
						<?php if($user->getPermissions()->getAccessLevel()==2){ echo '<th>id</th>'; } ?>
							<th>Name</th>
							<th>Last Updated</th>
							<th>Actions</th>
							<?php
								if($user->getPermissions()->getAccessLevel()==2){
									echo '<th>Developer</th>';
								}
							?>
						</tr>
					</thead>
					<tbody>
					<?php
					$i =0;
					$c = count($groups);
                    
                    foreach($groups as $group){
						$o=false;
						$groupId = 		$group->getId();
						$groupName = 	$group->getName();
						$sections = $group->getSections();
						if($user->getPermissions()->getAccessLevel()==2){
                            echo "<tr class='TableGroupHeader' data='".$i."/".$c."'><td>";
							if($i!=0){ echo "<a href='#' onclick='sectionGroupMove(\"up\", \"$groupId\");'>&#9650;</a>";$o=true;}
							if($i!=($c-1)) echo ($o?' / ':'')."<a href='#' onclick='sectionGroupMove(\"down\",\"$groupId\");'>&#9660;</a>";
							
							echo "</td><td colspan='4'>$groupName</td></tr>";
						}else{
							if(count($sections)>0)
							echo "<tr class='TableGroupHeader'><td colspan='5'>$groupName</td></tr>";
						}
						foreach($sections as $section){
						$id = 		$section->getId();
						$name = 	$section->getName();
						$date = $section->getDate("d/m/Y h:i:s A");
						?>
						<tr class="post" >
						<?php
							if($user->getPermissions()->getAccessLevel()==2){
								echo "<td>$id</td>";
							}
						?>
							<td onClick='document.location="section.php?sid=<?php echo $id; ?>&action=edit";'><?php echo $name; ?></td>
							<td onClick='document.location="section.php?sid=<?php echo $id; ?>&action=edit";'><?php echo $date; ?></td>
							<td>
								<a class='edit' href="section.php?sid=<?php echo $id; ?>&action=edit">[Edit]</a>
							</td>
							<?php if($user->getPermissions()->getAccessLevel()==2){
									echo'<td class="actions">';
									echo "<a class='evil settings' href=\"section.php?sid=$id&action=settings\">[Settings]</a>  ";
									echo "<a class='evil delete' href=\"section.php?sid=$id&action=delete\">[Delete]</a>";
									echo'</td>';
								}
								?>
								
							</td>
						</tr>
					<?php
					}
					$i++;
				}
					 ?>

				</tbody>
			</table>
		<?php
		$output = ob_get_clean();
	}

	include('includes/header.php');
	?>
        <aside class="sidebar">
            <a href="index.php">Home</a>
        </aside>

        <section class="content">
            <?php echo $output; ?>
        </section>
	<?php
	include('includes/footer.php');
?>
