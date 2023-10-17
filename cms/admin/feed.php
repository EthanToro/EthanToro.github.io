<?php
	include('includes/config.php');
	CleanRequestVars();
	CheckLogin();
	$user = getUser();
	$action = Get('action','display');
	$offset = Get('offset',0);
	$fid = Get('fid',false);
	if($fid=='main'){ 
		$b = $cms->getFeed();
		$fid =  $b->getId();
	}

	if($cms->getFeedCount() == 0){ header('Location: index.php'); }
	switch($action){
	case 'display':
		if($fid){ 
			postListingMenu();
			break;
		}else{
			feedListingMenu();
			break;
		}
	case 'deleteSelected':
	if($cms->getFeed($fid)->getSetting('postDeletion')|| $user->getPermissions()->getAccessLevel()==2){
		if($fid){
			if(Post('ds',false))deleteSelected();
			postListingMenu();
			break;
		}
	}else kickToCurb();
	case 'deleteFeed':
		if($user->getPermissions()->getAccessLevel()!=2) kickToCurb();
		if($fid){
			if(Post('db',false))deleteFeed();
			feedDeletingMenu();
			break;
		}
		
	case 'feedSettings':
		if ($user->getPermissions()->getAccessLevel()!=2)
			header('Location: feed.php');
		else {
			if (isset($_POST['submit']))
				feedSettings();
			feedSettingsMenu();
		}
		break;
	case 'uploadSettings':
		if ($user->getPermissions()->getAccessLevel()!=2)
			header('Location: feed.php');
		else {
			if (isset($_POST['submit']))
				uploadSettings();
			uploadSettingsMenu();
		}
		break;
	case 'reorderItems':
		if($cms->getFeed($fid)->getSetting('userSortable')){
			if (isset($_POST['submit']))
				reorderItems();
			reorderItemsMenu();
			break;
		}else kickToCurb();
	default:
		header('location: feed.php');
		break;
	}


	function reorderItems(){
		global $fid,$cms;
		$posts = array();
		foreach($_POST as $k=>$v){
			if(startsWith($k,'sort_')){
				$posts[str_replace('sort_', '', $k)]=$v;
			}
		}
		$feed = $cms->getFeed($fid);
		foreach($posts as $k=>$v){
			if(($post = $feed->getPost($k))!==false){
				$post->setUserSort($v);
			}
		}
		Post::defragUserSort($fid);
	}
	function reorderItemsMenu(){
		global $output,$database,$fid,$user,$cms;
		$feed = $cms->getFeed($fid);
		$settings = $feed->getSettings();
		$posts = $feed->getPosts(0, null,$settings['sortBy']);
		
		
		$name = $feed->getName();
		ob_start();
		?>
		<h1>Reorder items for '<?php echo $name; ?>'</h1>
		<form action='feed.php?fid=<?php echo $fid; ?>&action=reorderItems' method='post'>
		<table>
			<tr><th>Sort</th><th>title</th><th>date</th></tr>
			<?php
			if($posts!==false){
				if(count($posts)>0){
					foreach($posts as $post){
						$id = $post->getId();
						$userSort = $post->getUserSort();
						$title = $post->getTitle();
						$date = $post->getDate();
						echo "<tr><td><input type='text' name='sort_".$id."' value='".$userSort."' /></td><td>".$title."</td><td>".$date."</td></tr>";
					}
				}
			}
			?>
			</table>
			<input type='submit' name='submit' value='Save' />
		</form>
		<?php
		$output = ob_get_clean();
	}
	function uploadSettings(){
		global $cms, $fid, $message;
        $uploadEnabled  = Post('uploadEnabled');
        $uploadType  = Post('uploadType').'_fileHandeler';
        $uploadCount  = Post('uploadCount');
        $uploadText  = Post('uploadText');
        $feed 		  = $cms->getFeed($fid);
        $settings = array(
        	'uploadEnabled'	=>$uploadEnabled,
        	'uploadCount'	=>$uploadCount,
        	'uploadType'	=>$uploadType,
        	'uploadText'	=>$uploadText
        	);
       	$feed->editSettings($settings);
	}
	function uploadSettingsMenu(){
		 global $cms, $fid, $message, $output,$fileHandlers;
		$fid = intval($fid);
		$feed = $cms->getFeed($fid);
		$name = $feed->getName();
		$settings = $feed->getSettings();

		ob_start();
		?>
            <h1>Edit Upload Settings for '<?php echo $name; ?>'</h1>
            
            <?php if ($message['content'] !== '') { ?>
            <p class= "<?php echo $message['type']; ?>"><?php echo $message['content']; ?></p>
            <?php } ?>
            <form enctype="multipart/form-data" action="feed.php?action=uploadSettings&fid=<?php echo $fid; ?>" method="POST">
            	<label for="uploadEnabled">image Uploads</label><br />
                <input type="radio" name="uploadEnabled" value="1" <?php echo (($settings['uploadEnabled'])? 'checked="checked"' : ''); ?>/> Enable<br />
                <input type="radio" name="uploadEnabled" value="0" <?php echo ((!$settings['uploadEnabled'])? 'checked="checked"' : ''); ?>/> Disable
                <hr />

				<label for="uploadText">Upload Text</label><br />
                <input type='text' style="width: 500px;" name='uploadText' value='<?php echo $settings['uploadText'] ?>'/>
                <hr />


                <label for="uploadType">fileHandeler</label><br />
               	<select name='uploadType'>
               		<?php foreach($fileHandlers as $fh){
               			echo "<option value='$fh'".((str_replace('_fileHandeler', '', $settings['uploadType'])==$fh)?' selected':'').">$fh</option>";
               		}?>
               	</select>
                <hr />

                <br><input type="submit" name="submit" value="Submit" />
                <p class="clear"></p>
            </form>
		<?php
		$output = ob_get_clean();
       }   
	function feedSettingsMenu() {
        global $cms, $fid, $message, $output;
		$fid = intval($fid);
		$feed = $cms->getFeed($fid);
		$name = $feed->getName();
		$settings = $feed->getSettings();

		ob_start();
		?>
            <h1>Edit Feed Settings for '<?php echo $name; ?>'</h1>
            
            <?php if ($message['content'] !== '') { ?>
            <p class= "<?php echo $message['type']; ?>"><?php echo $message['content']; ?></p>
            <?php } ?>
            <form enctype="multipart/form-data" action="feed.php?action=feedSettings&fid=<?php echo $fid; ?>" method="POST">
                <label for="name">Feed Name</label>
                <br /><input type="text" name="name" value="<?php echo $name; ?>" />
                <hr /> 

                <label for="name">Feed Entry Noun <small>(Singular)</small></label><br />
                <input type="text" name="noun" value="<?php echo $settings['noun'] ?>" />
                <hr />

                <label for="hidden">Feed Hidden</label><br />
                <input type="radio" name="hidden" value="1" <?php echo (($settings['hidden'])? 'checked="checked"' : ''); ?>/> Enable<br />
                <input type="radio" name="hidden" value="0" <?php echo ((!$settings['hidden'])? 'checked="checked"' : ''); ?>/> Disable
                <hr />

                <label for="name">Sort By?</label><br />
                <select name="sortBy" value="" />
	                <?php
		                $cols = array('id','title','content','author','date','canComment','canRate','userSort');
		                $sortArray = array();
		                foreach($cols as $col){
		                	$sortArray[] = $col.' asc';
		                	$sortArray[] = $col.' desc';
		                }
		                for($i=0;$i<count($sortArray);$i++){
		             	   echo "<option value='".$sortArray[$i]."' ".($sortArray[$i]==$settings['sortBy']?' selected ':'').">".$sortArray[$i]."</option>";
		            	}
	            	?>
            	</select>
                <hr />

                <label for="hidden">Editor</label><br />
                <input type="radio" name="hasEditor" value="1" <?php echo (($settings['hasEditor'])? 'checked="checked"' : ''); ?>/> Enable<br />
                <input type="radio" name="hasEditor" value="0" <?php echo ((!$settings['hasEditor'])? 'checked="checked"' : ''); ?>/> Disable
                <hr />

                <label for="hidden">Default template</label><br />
                <input type="radio" name="hasDefault" value="1" <?php echo (($settings['hasDefault'])? 'checked="checked"' : ''); ?>/> Enable<br />
                <input type="radio" name="hasDefault" value="0" <?php echo ((!$settings['hasDefault'])? 'checked="checked"' : ''); ?>/> Disable
                <hr />

				<label for="comments">Commenting of posts</label><br />
                <input type="radio" name="comments" value="1" <?php echo (($settings['comments'])? 'checked="checked"' : ''); ?>/> Enable<br />
                <input type="radio" name="comments" value="0" <?php echo ((!$settings['comments'])? 'checked="checked"' : ''); ?>/> Disable
                <hr />

                <label for="comments">Rating of posts</label><br />
                <input type="radio" name="ratings" value="1" <?php echo (($settings['ratings'])? 'checked="checked"' : ''); ?>/> Enable<br />
                <input type="radio" name="ratings" value="0" <?php echo ((!$settings['ratings'])? 'checked="checked"' : ''); ?>/> Disable
                <hr />

                <label for="postContent">Post Content</label><br />
                <input type="radio" name="postContent" value="1" <?php echo (($settings['postContent'])? 'checked="checked"' : ''); ?>/> Enable<br />
                <input type="radio" name="postContent" value="0" <?php echo ((!$settings['postContent'])? 'checked="checked"' : ''); ?>/> Disable
                <hr />

                <label for="postContent">Post Creation</label><br />
                <input type="radio" name="postCreation" value="1" <?php echo (($settings['postCreation'])? 'checked="checked"' : ''); ?>/> Enable<br />
                <input type="radio" name="postCreation" value="0" <?php echo ((!$settings['postCreation'])? 'checked="checked"' : ''); ?>/> Disable
                <hr />
                
                <label for="postContent">Post Deletion</label><br />
                <input type="radio" name="postDeletion" value="1" <?php echo (($settings['postDeletion'])? 'checked="checked"' : ''); ?>/> Enable<br />
                <input type="radio" name="postDeletion" value="0" <?php echo ((!$settings['postDeletion'])? 'checked="checked"' : ''); ?>/> Disable
                <hr />

                <label for="postContent">User Sortable</label><br />
                <input type="radio" name="userSortable" value="1" <?php echo (($settings['userSortable'])? 'checked="checked"' : ''); ?>/> Enable<br />
                <input type="radio" name="userSortable" value="0" <?php echo ((!$settings['userSortable'])? 'checked="checked"' : ''); ?>/> Disable
                <hr />

                <br><input type="submit" name="submit" value="Submit" />
                <p class="clear"></p>

            </form>
		<?php
		$output = ob_get_clean();
    }


    function feedSettings() {
        global $cms, $fid, $message;
        $name 		  = Post('name',false);
        $comments 	  = Post('comments');
        $hidden  	  = Post('hidden');
        $postContent  = Post('postContent');
        $ratings 	  = Post('ratings');
        $postCreation = Post('postCreation');
        $postDeletion = Post('postDeletion');
        $userSortable = Post('userSortable');
        $noun 		  = Post('noun','post');
        $sortBy 	  = Post('sortBy');
        $hasEditor    = Post('hasEditor');
        $hasDefault    = Post('hasDefault');
        $anonComments = Post('anonComments');

        $feed 		  = $cms->getFeed($fid);

        $cols = array('id','title','content','author','date','canComment','canRate','userSort');
        $sortArray = array();
        foreach($cols as $col){
        	$sortArray[] = $col.' asc';
        	$sortArray[] = $col.' desc';
        }

		if(in_array($sortBy, $sortArray)){
	        if($comments==1||$comments==0){
	        	if($comments==1||$comments==0){
	        		if($postContent==1||$postContent==0){
		        		if ($name&&$noun) {
				            $feed->setName($name);
				            $settings = array(
				            	'comments'		=>$comments,
	        					'ratings'		=>$ratings,
	        					'postCreation'	=>$postCreation,
	        					'postDeletion'	=>$postDeletion,
	        					'postContent'	=>$postContent,
	        					'userSortable'	=>$userSortable,
	        					'noun'			=>$noun,
	        					'sortBy'		=>$sortBy,
	        					'hidden'		=>$hidden,
	        					'hasEditor'		=>$hasEditor,
	        					'hasDefault'		=>$hasDefault,
	        					'anonComments'	=>$anonComments
	        				);
							$feed->editSettings($settings);
				        } else {
				            $message['content'] = 'Please enter a valid feed name';
				            $message['type'] = 'error';
				        }
	        		}else{
			        	$message['content'] = 'postContent value is not valid';
			            $message['type'] = 'error';
		        	}
	       		 }else{
		        	$message['content'] = 'Comments value is not valid';
		            $message['type'] = 'error';
	        	}
	        }else{
	            $message['content'] = 'Comments value is not valid';
	            $message['type'] = 'error';
		    }		
		}
    }
	function deleteFeed(){
		global $cms,$fid;
		$cms->getFeed($fid)->destroy();
		header('Location: feed.php');
	}

	function feedDeletingMenu(){
		global $output,$fid;
		ob_start();
		?>
		<span class='evil warning'>WARNING YOU ARE ABOUT TO DELETE A COMPLETE FEED!<br> ARE YOU SURE YOU WANT TO CONTINUE?</span>
		<form method='post' action='feed.php?fid=<?php echo $fid; ?>&action=deleteFeed'>
		<input type='submit' name='db' value='Yes, Delete this feed I am sure I wanted this.'/>
		<input type='button' onClick="document.location='feed.php';"value='NOPE get me outta here'/>
		</form>
		<?php
		$output = ob_get_clean();
	}
	function deleteSelected(){
		global $database,$fid;
		$posts = Post('posts');
		if($posts!=null){
			$sql ="DELETE FROM `posts` where `id` in (".join(',',$posts).")";
			$database->query($sql);
			header('location: feed.php?fid='.$fid);
		}
	}
	function feedListingMenu(){
		global $output,$cms,$user;
		$feeds = $cms->getFeeds();
		ob_start();
		?>
		Click a below to manage the selected feed
		<table>
		<tr>
		<?php if($user->getPermissions()->getAccessLevel()==2){ echo '<th>id</th>'; } ?>
		<th>Name</th><th>Posts</th><?php if($cms->getSetting('comments')){echo '<th>comments</th>'; } ?><th>actions</th><?php if($user->getPermissions()->getAccessLevel()==2){	echo "<th>Developer</th>";}?></tr>
		<?php
		foreach($feeds as $b){
			if($user->getPermissions()->getAccessLevel()!=2){
				if($b->getSetting('hidden')){
					continue;
				}
			}
			echo "<tr class='cursorHand".($b->getSetting('hidden')?' evil ':'')."'>";
			if($user->getPermissions()->getAccessLevel()==2){
				echo "<td>{$b->getId()}</td>";
			}
			echo "
			<td onClick='document.location=\"feed.php?fid={$b->getId()}\";'>{$b->getName()}</td>
			<td onClick='document.location=\"feed.php?fid={$b->getId()}\";'>{$b->getPostCount()}</td>";
			if($cms->getSetting('comments')){if($b->getSetting('comments')){ echo "<td onClick='document.location=\"feed.php?fid={$b->getId()}\";'>{$b->getCommentCount()}</td>";}else{echo "<td></td>";} }
			echo "<td><a href=\"feed.php?fid={$b->getId()}\">Edit</a>";
			if($user->getPermissions()->getAccessLevel()==2){
				echo "<td><a class='evil rename' href=\"feed.php?fid={$b->getId()}&action=feedSettings\">[Feed Settings]</a>  ";
				echo "<a class='evil delete' href=\"feed.php?fid={$b->getId()}&action=deleteFeed\">[Delete Feed]</a></td>";
			}
			echo"</tr>";
		}
		?>
		</table>	
		<?php		
		$output = ob_get_clean();
	}

	function postListingMenu(){
		global $output,$database,$fid,$user,$cms;
		$page = Get('page');
		$feed = $cms->getFeed($fid);
		$postCount = $feed->getPostCount();
		$postsPerPage = 10;

		$pageCount = (!($postCount%$postsPerPage)) ? $postCount/$postsPerPage : ceil($postCount/$postsPerPage);

		if($page < 1){
			$page = 1;
		}else if($page>$pageCount){
			$page = $pageCount;
		}
		$settings = $feed->getSettings();
		$posts = $feed->getPosts(($page-1)*$postsPerPage, $postsPerPage,$settings['sortBy']);

		ob_start();
		?>
		<h1><?php echo $feed->getName();?></h1>
		<h3>Posts</h3>
		<form method='post' action='feed.php?fid=<?php echo $fid; ?>&action=deleteSelected'>
		<table class="posts">
		<thead>
		<tr>
		<?php if($user->getPermissions()->getAccessLevel()==2){echo"<th>Id</th>";} ?>
		<th>Name</th>
		<?php  
		if($cms->getUserCount()>1){
			echo "<th>Author</th>";
		}
		?>
		<th>Date</th>
		<?php if($feed->getSetting('comments')){echo'<th>Comments</th>';}?>
		<th class="actions">Actions</th>
		</tr>
		</thead>
		<tbody>
		<?php
		if($posts!==false){
			if(count($posts)>0){
				foreach($posts as $post){
					$postId= $post->getId();
					$postTitle= truncate($post->getTitle(),20,' ','...');
					$postTitlehover = $post->getTitle();
					$postAuthor = $post->getAuthor();
					$postDate = $post->getDate('M d, Y');
					$postCommentCount =intval($post->getCommentCount());
					?>
					<tr class="post">
					<?php if($user->getPermissions()->getAccessLevel()==2){echo"<td>$postId</td>";}?>
					<td title="<?php echo $postTitlehover; ?>" onClick='document.location="post.php?fid=<?php echo $fid; ?>&pid=<?php echo $postId; ?>&action=edit";'><?php echo $postTitle; ?></td>
					<?php if($cms->getUserCount()>1){ ?>	<td onClick='document.location="post.php?fid=<?php echo $fid; ?>&pid=<?php echo $postId; ?>&action=edit";'><?php echo $postAuthor; ?></td><?php } ?>
					<td onClick='document.location="post.php?fid=<?php echo $fid; ?>&pid=<?php echo $postId; ?>&action=edit";'><?php echo $postDate; ?></td>
					<?php if($settings['comments']){ ?> <td onClick='document.location="post.php?fid=<?php echo $fid; ?>&pid=<?php echo $postId; ?>&action=edit";'><?php echo $postCommentCount; ?></td><?php }?>
					<td class="actions">
					<a href="post.php?fid=<?php echo $fid; ?>&pid=<?php echo $postId; ?>&action=edit" class="edit">Edit</a>
					<!--<a href="post.php?fid=<?php echo $fid; ?>&pid=<?php echo $postId; ?>&action=view" class="details">Details</a>-->
					<?php
					if($settings['postDeletion'] || $user->getPermissions()->getAccessLevel()==2){
					?><a href="post.php?fid=<?php echo $fid; ?>&pid=<?php echo $postId; ?>&action=delete" class="delete">Delete</a>
					<input type="checkbox" name="posts[]" value="<?php echo $postId; ?>" />
					<?php } ?>
					</td>
					</tr>
					<?php
				}
			}else{
				?>
				<tr><td colspan=6 style='text-align:center;'>No Posts</td></tr>				
				<?php
			}
		}else echo "nope";
		?>
		
		</tbody>
		</table>
		<?php if(count($posts)>0){
		if($settings['postDeletion'] || $user->getPermissions()->getAccessLevel()==2){ ?>
			<input type='submit' name='ds' value='Delete Selected'/></form>
		<?php } 
		if($pageCount>1){?>
			<div class="pagination">
			<a href="<?php echo 'feed.php?fid=' . $fid . '&page=' . ($page-1); ?>" class="previous">Previous</a>
			<?php for ($i = 1; $i <= $pageCount; $i++) { ?>
				<a href="<?php echo "feed.php?fid={$fid}&page={$i}"; ?>" class="number<?php if ($i == $page) echo ' current'; ?>"><?php echo $i; ?></a>
				<?php } ?>
			<a href="<?php echo 'feed.php?fid=' . $fid . '&page=' . ($page+1); ?>" class="next">Next</a>
			</div>
			<?php }
		} ?>
		<?php
		$output = ob_get_clean();
	}

include('includes/header.php');
?>
	<aside class="sidebar">
	<?php
		if($fid){
			if($action=='display'){
				?><a href="feed.php">Back to Feed List</a><?php
			}else{
				?><a href="feed.php?fid=<?php echo $fid; ?>">Back to <?php echo $cms->getFeed($fid)->getSetting('noun');?> List</a><?php
			}

			if($action!='feedSettings' && $action!='uploadSettings'){

				if($user->getPermissions()->getAccessLevel()==2){
					?>
					<a href="feed.php?fid=<?php echo $fid; ?>&action=feedSettings">General Settings</a>
					<?php
				}

				if($cms->getFeed($fid)->getSetting('userSortable')){
					?><a href="feed.php?fid=<?php echo $fid; ?>&action=reorderItems">Reorder <?php echo $cms->getFeed($fid)->getSetting('noun');?>s</a><?php
				}
			}else{
				if($user->getPermissions()->getAccessLevel()==2){
				?>
				<a href="feed.php?fid=<?php echo $fid; ?>&action=feedSettings">General Settings</a>
				<a href="feed.php?fid=<?php echo $fid; ?>&action=uploadSettings">Upload Settings</a>
				<?php
				}
			}	
			if($cms->getFeed($fid)->getSetting('postCreation') || $user->getPermissions()->getAccessLevel()==2){
				?><a href="post.php?fid=<?php echo $fid; ?>&action=new">New <?php echo $cms->getFeed($fid)->getSetting('noun');?></a><?php
			}
		}
	?>
	</aside>

	<section class="content">
		<?php echo $output; ?>
	</section><!--content end-->
<?php include('includes/footer.php'); ?>
