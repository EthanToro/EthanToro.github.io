<?php
if(Get('pid',false)&&Post::exists(Get('pid'))){
	if(Post('submit',false)){
		$post = new Post(Get('pid'));
		$feed = $post->getFeed();
		if($feed->getSetting('comments')){
			$p['uid'] = $user->getId();
		    $p['name'] = $user->getName();
		    $p['email'] = $user->getEmail();
		    $p['type'] = 0;
            $p['isAnon'] = 0;
		    $p['postId'] = $pid;
		    $p['content'] = Post('content');
			$comment = Comment::Create($p);
			$ch = new Comment_Handler($comment);
            $ch->process();
		}else{
			echo 'STOP RIGHT THERE CRIMINAL SCUM!';
		}
	}
}