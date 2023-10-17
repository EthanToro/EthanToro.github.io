<?php
    include('includes/config.php');
	CleanRequestVars();
	CheckLogin();
	$user = getUser();
	$action = Get('action','new');
	$fid = intval(Request('fid'));
    $message = array(
        'content'=>null,
        'type'=>null
    );
	if($fid=='main'){ 
		$b = $cms->getFeed();
		$fid =  $b->getId();
	}
	$output = '';
	$pid = intval(Request('pid'));
	switch($action){
		case 'new':
		if($cms->getFeed($fid)->getSetting('postCreation')|| $user->getPermissions()->getAccessLevel()==2){
			$feed = $cms->getFeed($fid);
            if (Post('submit', false) !== false)
                doNewPost();
			newPostMenu();
			break;
		}else kickToCurb();
		case 'edit':
			$feed = $cms->getFeed($fid);
            if (Post('submit', false) !== false)
                doEditPost();
			editPostMenu();
			break;
		case 'delete':
			if($cms->getFeed($fid)->getSetting('postDeletion')|| $user->getPermissions()->getAccessLevel()==2){
				if(!Post('submit',false)){ deletePostMenu(); }else{ deletePost(); }
				break;
			}else kickToCurb();
		default:
			header("location: index.php");
	}
	function newPostMenu($error=''){
		global $output, $fid, $message,$cms;
		$feed = $cms->getFeed($fid);
		if($feed->getSetting('hasDefault',false)){
			$def = $feed->getDefault();
		}
		ob_start();
		?>
			<h1>New <?php echo $feed->getSetting('noun');?></h1>    
			<?php if(isset($message['content']) && $message['content']) echo "<div class='{$message['type']}'>{$message['content']}</div>"; ?>
			<form enctype="multipart/form-data" action="post.php?fid=<?php echo $fid; ?>&action=new" method="POST">
				<label for="title">Title</label>
				<input type="text" name="title" placeholder="Title" class="title feed round" value="<?php
				if($et = Post('title','')!==''){
					echo $et;
				}else{
					if($feed->getSetting('hasDefault',false)){
						echo $def['Title'];
					}
				}
				?>"/>

				<?php if($feed->getSetting('postContent')){?><label for="content">Content</label>
				<textarea id="content" name="content"><?php
				 if($et = Post('content','')!==''){
					echo $et;
				}else{
					if($feed->getSetting('hasDefault',false)){
						echo $def['Content'];
					}
				}
					?></textarea>
				<?php } 
				if($feed->getSetting('comments')||$feed->getSetting('uploadEnabled')){?>
				<div class="options">
					<label>Options</label>
					<?php if($feed->getSetting('comments')){?>

					<br /><input type="checkbox" name="canComment" value ='1' <?php echo ((Post('canComment')==1)?'checked="checked"':''); ?> /> Enable commenting on this post
					<br /><input type="checkbox" name="canRate" value='1' <?php echo ((Post('canRate')==1)?'checked="checked"':''); ?> /> Enable rating this post
					<?php }
					if($feed->getSetting('uploadEnabled')){ ?>
					<br /><?php echo $feed->getSetting('uploadText'); ?>
					<br /><input type="file" name="userfile" />
					<?php } ?>
				</div><!--options end-->
				<?php }//else echo "<br>";?>
				<br>
				<input type="submit" name="submit" value="Create" />
				<p class="clear"></p>
			</form>
		<?php
		$output = ob_get_clean();
	}
	function deletePostMenu(){
		global $output, $fid, $pid, $message;
		ob_start();
		?>
			<h1>Delete Post</h1>    

            <?php if(isset($message['content']) && $message['content']) echo "<div class='{$message['type']}'>{$message['content']}</div>"; ?>
			<form enctype="multipart/form-data" action="post.php?action=delete" method="POST">
				<input type='hidden' name='fid'    value='<?php echo $fid; ?>'/>
				<input type='hidden' name='pid'    value='<?php echo $pid; ?>'/>
				<input type='submit' name='submit' value='Confirm'/>
			</form>
		<?php
		$output = ob_get_clean();
	}
	
	function editPostMenu(){
		global $output, $database, $pid, $feed, $fid, $message,$cms,$user;
			$post = $feed->getPost($pid);
			$title = $post->getTitle();
			$content = $post->getContent();
			$canComment = $post->getCanComment();
			$canRate = $post->getCanRate();
			$hasFile=$post->hasFile();
			$hasDefault = $feed->getSetting('hasDefault',false);
			if($hasFile)
				$file= $post->getFile();
			
			
			ob_start();
			?>
				<h1>Edit <?php echo $feed->getSetting('noun');?></h1>    
				<?php if(isset($message['content']) && $message['content']) echo "<div class='{$message['type']}'>{$message['content']}</div>"; ?>
				<form enctype="multipart/form-data" action="post.php?fid=<?php echo $fid; ?>&pid=<?php echo$pid; ?>&action=edit" method="POST">
					<label for="title">Title</label>
					<input type="text" name="title" placeholder="Title" class="title feed round" value="<?php echo $title; ?>"/>
					<?php if($feed->getSetting('postContent')){?><label for="content">Content</label>
					<textarea id="content" name="content"><?php echo $content; ?></textarea><?php } ?>
					<?php if($feed->getSetting('uploadEnabled')||$feed->getSetting('comments')||($user->getPermissions()->getAccessLevel()==2&&$hasDefault)){ ?>
					<div class="options">
						<label>Options</label>
						<?php if($feed->getSetting('comments')){?>
						<br /><input type="checkbox" name="canComment" value ='1' <?php echo (($canComment==1)?'checked="checked"':''); ?> /> Enable commenting on this post
						<br /><input type="checkbox" name="canRate" value='1' <?php echo (($canRate==1)?'checked="checked"':''); ?> /> Enable rating this post
						<?php } 
						if($feed->getSetting('uploadEnabled')){ ?>
						<br /><?php echo $feed->getSetting('uploadText'); ?>
						<br /><input type="file" name="userfile" />
						 <?php
						 echo (($hasFile===true)?"
						 	<br />
						 	<div id='ajax_remove'>
							 	<label>Current Upload</label>
							 	<input type=\"button\" id=\"weird\" onclick=\"deleteFile(".$pid.", ".$fid.");\" value=\"Delete File\">
							 	<br />".$file->getUrl()."<br><br>
							 	".$file->view()."
							</div>":'');
						} ?>
					<?php if($user->getPermissions()->getAccessLevel()==2&&$hasDefault){
?>
<br>set As Default Template<input type='checkbox' name='setDef' value='1'/><br>
<?php
					}?>
					</div><!--options end-->
					<?php }else echo "<br>";?>
					<input type="submit" name="submit" value="Save Changes" />
					<p class="clear"></p>
				</form>
			<?php
			$output = ob_get_clean();
	}
	
	function doNewPost(){
		global $fid, $feed, $user, $message,$uploadPath;
		$empty = chr(60).chr(98).chr(114).chr(62);
		$title = Post('title');
		$content = Post('content');
		$canComment = Post('canComment');
		$canRate = Post('canRate');
		$fields = array($title);
        $error;
        if (isnull($fields) || ($title=='')) {
            $message['content'] = 'It Seems you forgot to complete some required fields.';
            $message['type'] = 'error';
        }

		if (!isnull($fields) && ($title!='') && ($content!='<br>')) {
			$a = array(
                'title'=>$title,
				'author'=>$user->getAlias(),
				'content'=>$content,
				'canComment' => $canComment,
				'canRate' => $canRate
			);
			$p = new Post();
			$id = $p->create($a);		
	        if($feed->getSetting('uploadEnabled')&&isset($_FILES['userfile'])&&$_FILES['userfile']['tmp_name']!=''){
		        $fileHandelr = $feed->getSetting('uploadType');
		        $fileHandelr = new $fileHandelr();
				$results = $fileHandelr->upload('post_'.$id,$_FILES['userfile']);
			}else{
				$results =array('success'=>true,'path'=>'');
			}
			
	        if ($results['success']){
	            $p->edit(array('file'=>$results['path']));
				header("location: feed.php?fid=$fid");
				exit;
	        }else{
	        	$p->destroy();
	        	die('<pre>'.var_export($results).'</pre><br>new post file upload failed');
	        }
		}
	}
	function deletePost(){
		global $database, $pid, $cms, $fid, $message;

		$post = $cms->getFeed($fid)->getPost($pid);
        $post->destroy();

		header("location: feed.php?fid=$fid");
	}
	function doEditPost(){
		global $fid, $feed, $user, $message,$uploadPath;
			$pid = Get('pid',null);
			$empty = chr(60).chr(98).chr(114).chr(62);
			$title = Post('title');
			$content = Post('content');
			$canComment = Post('canComment');
			$canRate = Post('canRate');
			$setDef = Post('setDef',false);
			$fields = array($title);
	        $error;
	        if (isnull($fields) || ($title=='')) {
	            $message['content'] = 'It Seems you forgot to complete some required fields.';
	            $message['type'] = 'error';
	        }

			if (!isnull($fields) && ($title!='') && ($content!=$empty)) {
				$a = array(
	                'title'=>$title,
					'author'=>$user->getAlias(),
					'content'=>$content,
					'canComment' => $canComment,
					'canRate' => $canRate
				);
				$p = new Post($pid);
				$p->edit($a);	
				$f = $p->getFeed();
				if($setDef==1&&$user->getPermissions()->getAccessLevel()==2)
					$f->setDefault($title,$content);	
		        if($feed->getSetting('uploadEnabled')&&isset($_FILES['userfile'])&&$_FILES['userfile']['tmp_name']!=''){
			        $fileHandelr = $feed->getSetting('uploadType');
			        $fileHandelr = new $fileHandelr();
					$results = $fileHandelr->upload('post_'.$pid,$_FILES['userfile']);
				}else{
					$results =array('success'=>true,'path'=>'');
				}
				
		        if ($results['success']){
		            if($results['path']!=''){
			            if($p->hasFile()){
			            	@$p->getFile()->destroy();
			            }
			            $p->edit(array('file'=>$results['path']));
			        }
					header("location: feed.php?fid=$fid");
					exit;
		        }else{
		        	$message['content'] = '<pre>'.var_export($results).'</pre><br>new post file upload failed';
		        	 $message['type'] = 'error';
		        }
			}
		
	}
				
	include('includes/header.php');
	$hasEditor = $hasDefault = false;
	if(Get('fid',false)){
		$hasEditor = new Feed(Get('fid'));
		$hasEditor = $hasEditor->getSetting('hasEditor');
	}
	if(Get('pid',false)){
		$post = new Post(Get('pid'));
		$feed = $post->getFeed();
		$hasEditor = $feed->getSetting('hasEditor');
		$hasDefault = $feed->getSetting('hasDefault');
	}
	if($hasEditor){
			?>
            <script src="http://js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>
            <script type="text/javascript">
			$(document).ready(function(){
				new nicEditor({fullPanel : true}).panelInstance('content');
			});
			</script>
		<?php } ?>
            <aside class="sidebar">
                <a href="feed.php?fid=<?php echo $fid; ?>">Back to <?php echo $cms->getFeed($fid)->getSetting('noun');?> List</a>
                <?php if ($action == 'edit' && $pid && $cms->getSetting('comments') && $feed->getSetting('comments')) { ?>
                <a href="comments.php?pid=<?php echo $pid; ?>">View Comments</a>
                <a href="comments.php?pid=<?php echo $pid; ?>&action=new">New Comment</a>
                <?php }
	                if($cms->getFeed($fid)->getSetting('postCreation') || $user->getPermissions()->getAccessLevel()==2){
?>
	<a href="post.php?fid=<?php echo $fid; ?>&action=new">New <?php echo $cms->getFeed($fid)->getSetting('noun');?></a>
<?php
	            	}
            	?>
            </aside>

            <section class="content">
                <?php echo $output; 
                ?>
            </section><!--content end-->
	<?php include('includes/footer.php'); ?>