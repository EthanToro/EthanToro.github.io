<?php
include('../../includes/config.php');


if ($cms->getSetting('comments')) {
    if (isset($_SESSION['pid'])) && ($pid = $_SESSION['pid'])) {
        if (Post::exists($pid)) {
            $post = new Post($pid);
            $feed = $post->getFeed();

            if ($feed->getSetting('comments') && Post::exists($pid)) {
                if (isset($_SESSION['user_obj']) && ($user = $_SESSION['user_obj']) && $user->isEnabled()) {
                    $info = $_POST;
                    $info['postId'] = $pid;
                    $info['isAnon'] = 0;
                    $info['uid'] = $user->getId();
                    $info['name'] = $user->getAlias();
                    $info['email'] = $user->getEmail();

                    try {
                        $comment = Comment::create($info);
                    } catch (Exception $e) {
                        echo $e;
                    }

                    $ch = new Comment_Handler($comment);
                    $ch->process();
                } elseif ($cms->getSetting('anonComments') /*&& $feed->getSetting('anonComments')*/) {
                    $info = $_POST;
                    $info['postId'] = $pid;
                    $info['isAnon'] = 1;

                    try {
                        $comment = Comment::create($info);
                    } catch (Exception $e) {
                        echo $e;
                    }

                    $ch = new Comment_Handler($comment);
                    $ch->process();
                }
            } else
                throw new Exception('commenting disabled');
        } else 
            throw new Exception('Non-existant post');
    } else
        throw new Exception('No post id supplied for comment');
} else {
    throw new Exception('commenting disabled');

?>
