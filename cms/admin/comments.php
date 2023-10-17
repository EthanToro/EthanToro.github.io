<?php
include('includes/config.php');
CleanRequestVars();
CheckLogin();
$user = getUser();
if(tabDisabled('comments')) kickToCurb();
$action = get('action');
$uid = get('uid');
$pid = get('pid');
$cid = get('cid');
$mComments = post('comments');
$output = '';
$message = '';

if ($user->getPermissions()->getAccessLevel() != 2) {
    // header('Location: index.php');
}
switch ($action) {
    case 'new':
        if (Post('submit', false))
            newComment();
        newCommentView();
        break;
    case 'edit':
        if (Post('submit', false))
            editComment();
        editCommentView();
        break;
    case 'delete':
        commentDelete();
        break;
    case 'multi':
            echo Post('submit');
        switch(Post('submit',false)){
            case 'Archive Selected':
                mCommentArchive();
                commentsMain();
                break;
            case 'Approve Selected':
                mCommentApprove();
                commentsMain();
                break;
            case 'Mark Selected as spam':
                mCommentSpam();
                commentsMain();
                break;
            default:
                //kickToCurb();
        }
    case 'view':
    default:
        commentsMain();
        break;
}

#################################################################
function mCommentSpam() {
    global $mComments,$pid,$uid;

    foreach ($mComments as $c){
        $comment = new Comment($c);
        $comment->MarkSpamComment();
    }
    $append = '';
    if($pid!=null){
        if($append==''){
            $append.="?pid=$pid";
        }else{
            $append.="&pid=$pid";
        }
    }
    if($uid!=null){
        if($append==''){
            $append.="?uid=$uid";
        }else{
            $append.="&uid=$uid";
        }
    }
    header('Location: comments.php'.$append);
}
function mCommentApprove() {
    global $mComments,$pid,$uid;

    foreach ($mComments as $c){
        $comment = new Comment($c);
        $comment->approveComment();
    }
    $append = '';
    if($pid!=null){
        if($append==''){
            $append.="?pid=$pid";
        }else{
            $append.="&pid=$pid";
        }
    }
    if($uid!=null){
        if($append==''){
            $append.="?uid=$uid";
        }else{
            $append.="&uid=$uid";
        }
    }
    header('Location: comments.php'.$append);
}
function mCommentArchive() {
    global $mComments,$pid,$uid;

    foreach ($mComments as $c)
        Comment::archiveComment($c);
    $append = '';
    if($pid!=null){
        if($append==''){
            $append.="?pid=$pid";
        }else{
            $append.="&pid=$pid";
        }
    }
    if($uid!=null){
        if($append==''){
            $append.="?uid=$uid";
        }else{
            $append.="&uid=$uid";
        }
    } 
    if(Get('sort')!=null){
        if($append==''){
            $append.="?sort=".Get('sort');
        }else{
            $append.="&sort=".Get('sort');
        }
    }
    header('Location: comments.php'.$append);
}
function commentsMain() {
    global $output, $cms, $message, $uid, $pid;
    

    ### PAGINATION ###
    $page = Get('page',1);
    $sort = Get('sort');
    $commentCount = Comment::getCount($uid, $pid);
    $commentUnapprovedCount = Comment::getPendingCount($uid, $pid);
    $commentApprovedCount  = Comment::getApprovedCount($uid, $pid);
    $commentSpamCount = Comment::getSpamCount($uid, $pid);
    $commentsPerPage = 25;


if(Get('sort',false)=='pending'){
    $pageCount = (($commentUnapprovedCount%$commentsPerPage) ? ceil($commentUnapprovedCount/$commentsPerPage) : $commentCount/$commentsPerPage);

    $comments = Comment::getPendingComments(($page-1)*$commentsPerPage, $commentsPerPage, 'date desc', $uid, $pid);

}elseif(Get('sort',false)=='approved'){
    $pageCount = (($commentApprovedCount%$commentsPerPage) ? ceil($commentApprovedCount/$commentsPerPage) : $commentCount/$commentsPerPage);
    
    $comments = Comment::getApprovedComments(($page-1)*$commentsPerPage, $commentsPerPage, 'date desc', $uid, $pid);
}elseif(Get('sort',false)=='spam'){
    $pageCount = (($commentSpamCount%$commentsPerPage) ? ceil($commentSpamCount/$commentsPerPage) : $commentCount/$commentsPerPage);

    $comments = Comment::getSpamComments(($page-1)*$commentsPerPage, $commentsPerPage, 'date desc', $uid, $pid);
}else{
    $pageCount = (($commentCount%$commentsPerPage) ? ceil($commentCount/$commentsPerPage) : $commentCount/$commentsPerPage);
    $comments = Comment::getAllComments(($page-1)*$commentsPerPage, $commentsPerPage, 'date desc', $uid, $pid);
}
   ### PAGINATION ###
    if ($page < 1)
        $page = 1;
    elseif ($page > $pageCount)
        $page = $pageCount;

    ob_start();
    $append='';
    if($pid!=null){
            $append.="&pid=$pid";
    }
    if($uid!=null){
            $append.="&uid=$uid";
    } 
?>
        <aside class="sidebar">
            <?php if($pid!=null){ ?>
            <a href="comments.php?pid=<?php echo $pid; ?>&action=new">New Comment</a>
            <?php } ?>
            <a href="comments.php?sort=all<?php echo $append; ?>">View All(<?php echo $commentCount; ?>)</a>
            <a href="comments.php?sort=pending<?php echo $append; ?>">pending(<?php echo $commentUnapprovedCount; ?>)</a>
            <a href="comments.php?sort=approved<?php echo $append; ?>">Approved(<?php echo $commentApprovedCount; ?>)</a>
            <a href="comments.php?sort=spam<?php echo $append; ?>">Spam(<?php echo $commentSpamCount; ?>)</a>
        </aside>
    
    <script type="text/javascript">
        $(document).ready(function() {
            $("a.delete").click(function(e) {
                e.preventDefault();

                if (confirm('Are you sure that you would like to delete this comment?'))
                    window.location = $(this).attr('href');
                });
            });
    </script>
    <section class="content">
        <h1>Manage Comments</h1>

        <!--<p>There are <strong><?php echo $commentCount;?></strong> comments, <strong><?php echo $commentUnapprovedCount; ?></strong> are waiting for approval.</p>-->
        <form action="comments.php?action=multi<?php
        if($pid!=null){echo "&pid=$pid";}
        if($uid!=null){echo "&uid=$uid";}?>" method="POST" onsubmit="return confirm('Are you sure that you would like to Apply these settings to these comments?')">
        
        <input type="submit" name="submit" value="Archive Selected" />
        <input type="submit" name="submit" value="Approve Selected" />
        <input type="submit" name="submit" value="Mark Selected as spam" />
        <table class="comments posts">
            <tr>
                <th>Id</th>
                <th>Post Title</th>
                <th>User</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
            <?php
            if(count($comments)>0){
             foreach ($comments as $cmt) {
                $id         = $cmt->getId();
                $post       = $cmt->getPost()->getTitle();
                $post       = substr($cmt->getContent(),0,50);
                $userId     = $cmt->getUserId();
                $name       = $cmt->getName();
                $date       = $cmt->getDate("m/d/Y h:i:s A");
            ?>
                <tr class="post">
                    <td><?php echo $id; ?></td>
                    <td><?php echo $post; ?></td>
                    <?php if ($userId) { ?>
                        <td><a href="user.php?uid=<?php echo $userId; ?>"><?php echo $name; ?></a></td>
                    <?php } else { ?>
                        <td><?php echo $name; ?></td>
                    <?php } ?>
                    <td><?php echo $date; ?></td>
                    <td class="actions">
                        <a href="comments.php?pid=<?php echo $pid; ?>&cid=<?php echo $id; ?>&action=edit" class="edit">[Edit]</a>
                        <a href="comments.php?pid=<?php echo $pid; ?>&cid=<?php echo $id; ?>&action=delete" calss="delete">[Delete]</a>
                        <input type="checkbox" name="comments[]" value="<?php echo $id; ?>" />
                    </td>
                </tr>
            <?php }
        }else{
            echo "<tr>  
    <td></td>
    <td colspan=3 style='text-align:center;'>There are not added comments that fit the filters</td>
    <td></td>";
        }
        ?>
        </table>
        <input type="submit" name="submit" value="Archive Selected" />
        <input type="submit" name="submit" value="Approve Selected" />
        <input type="submit" name="submit" value="Mark Selected as spam" />
       <?php if($pageCount>1){ ?>
        <div class="pagination">
            <?php if ($page > 1) { ?>
                <a href="comments.php?page=<?php echo ($page-1); ?>" class="previous">Previous</a>
            <?php } for ($i=1; $i <= $pageCount; $i++) { ?>
                <a href="comments.php?page=<?php echo $i; ?>" class="number<?php if ($i == $page) echo ' current'; ?>"><?php echo $i; ?></a>
            <?php } if ($page < $pageCount) { ?>
                <a href="comments.php?page=<?php echo ($page+1); ?>" class="next">Next</a>
            <?php } ?>
        </div>
        <?php } ?>
        </form>
    </section>
<?php
    $output = ob_get_clean();
}

#################################################################

function newCommentView() {
    global $output, $cms, $message, $user, $pid;

    ob_start();
?>
    <aside class="sidebar">
        <a href="comments.php?pid=<?php echo $pid; ?>">Back</a>
    </aside>

    <section class="content">
        <h1>Create a Comment</h1>

        <?php if (isset($message) && $message) echo $message; ?>
        <h3>New Comment</h3>
            <form enctype="multipart/form-data" action="comments.php?action=new&pid=<?php echo $pid; ?>" method="POST">
            <label for="name">Name</label>
            <br /><strong><?php echo Post('name', $user->getAlias()); ?></strong>

            <br /><br />
            <label for="email">Email</label>
            <br /><strong><?php echo Post('email', $user->getEmail()); ?></strong>

            <br /><br />
            <label for="postId">Post Id</label>
            <br /><input type="text" name="postId" value="<?php echo Post('postId', $pid); ?>" />

            <br /><br />
            <label for="content">Comment</label>
            <br /><textarea name="content" id="content"><?php echo Post('content'); ?></textarea>

            <input type="submit" name="submit" value="Submit" />
            <p class="clear"></p>
        </form>
    </section>

<?php
    $output = ob_get_clean();
}

function newComment() {
    global $cms, $message, $validate, $user, $pid;
    $errors = array();

    $p = array();
    foreach($_POST as $k => $v)
        $p[$k] = Post($k);

    $p['uid'] = $user->getId();
    $p['name'] = null;
    $p['email'] = null;
    $p['isApproved'] = '1';
    if ($pid)
        $p['postId'] = $pid; 

    //if($p['accessLevel'] > $user->getPermissions()->getAccessLevel()) kickToCurb();
    
    if (!Post::exists($p['postId']))
        $errors[] = "There is no corresponding post for the given post Id";

    $requiredFields = array(
        'postId',
        'uid',
        'content',
    );

    foreach($p as $field => $value) {
        if ($value == false && in_array($field, $requiredFields))
            $errors[] = 'The ' . $field . ' field was left blank or is invalid.';
    }

    if (count($errors)) {
        $message = '<ul class="error">';
        foreach ($errors as $error)
            $message .= '<li>' . $error . '</li>';
        $message .= '</ul>';
    } else {
        Comment::create($p);
        header('Location: comments.php');
    }
}

#################################################################

function editCommentView() {
    global $output, $cms, $message, $user, $cid, $pid, $uid;

    if (!Comment::exists($cid))
        header('Location: comments.php');

    $comment = new Comment($cid);

    ob_start();
?>
    <aside class="sidebar">
        <?php if ($uid) { ?>
            <a href="comments.php?uid=<?php echo $uid; ?>">Back</a>
        <?php } elseif ($pid) { ?>
            <a href="comments.php?pid=<?php echo $pid; ?>">Back</a>
        <?php } else { ?>
            <a href="comments.php">All Comments</a>
        <?php } ?>
    </aside>

    <section class="content">
        <h1>Edit Comment</h1>

        <?php if (isset($message) && $message) echo $message; ?>
        <h3>Comment id:<?php echo $comment->getId(); ?></h3>
        <h4>On Post "<?php echo $comment->getPost()->getTitle(); ?>"</h4>
            <form enctype="multipart/form-data" action="comments.php?action=edit&cid=<?php echo $comment->getId(); ?>" method="POST">
            <label for="name">Name:</label>
            <?php if ($comment->isAnon()) { ?>
            <br /><input type="text" name="name" value="<?php echo $comment->getName(); ?>" />
            <?php } else { ?>
            <br /><a href="user.php?uid=<?php echo $comment->getUser()->getId(); ?>&action=edit"><?php echo $comment->getName(); ?></a>
            <?php } ?>

            <br /><br />
            <label for="email">Email:</label>
            <?php if ($comment->isAnon()) { ?>
                <br /><input type="email" name="email" value="<?php echo $comment->getEmail(); ?>" />
            <?php } else { ?>
                <br /></strong><?php echo $comment->getEmail(); ?></strong>
            <?php } ?>

            <br /><br />
            <label for="postId">Post Id:</label>
            <br /><input type="text" name="postId" value="<?php echo $comment->getPost()->getId(); ?>" />

            <br /><br />
            <label for="content">Comment:</label>
            <br /><textarea name="content" id="content"><?php echo $comment->getContent(); ?></textarea>

            <input type="submit" name="submit" value="Submit" />
            <p class="clear"></p>
        </form>
    </section>
<?php
    $output = ob_get_clean();
}

function editComment() {
    global $cms, $message, $validate, $uid, $user, $cid;


    $errors = array();

    $p = array();
    foreach($_POST as $k => $v)
        $p[$k] = Post($k);

    //if($p['accessLevel']>$user->getPermissions()->getAccessLevel()) kickToCurb();
    if (!Comment::exists($p['postId']))
        $errors[] = "There is no corresponding post for the given post Id";

    $requiredFields = array(
        'content',
    );

    foreach ($p as $field => $value) {
        if ($value == false && in_array($field, $requiredFields))
            $errors[] = 'The ' . $field . ' field was left blank or is invalid.';
    }

    if (count($errors)) {
        $message = '<ul class="error">';
        foreach ($errors as $error)
            $message .= '<li>' . $error . '</li>';
        $message .= '</ul>';

    } else {
        $comment = new Comment($cid);
        $comment->edit($p);

        $message = '<p class="success">Successfully updated comment.</p>';
        header('location: comments.php');
    }
}

#################################################################

function commentDelete() {
    global $cid;

    Comment::destroyComment($cid);
    header('Location: comments.php');
}
function mCommentDelete() {
    global $mComments,$pid,$uid;

    foreach ($mComments as $c)
        $comment = new Comment($c);
        $comment->destroyComment($c);
        $comment->destroyComment($c);
    $append = '';
    if($pid!=null){
        if($append==''){
            $append.="?pid=$pid";
        }else{
            $append.="&pid=$pid";
        }
    }
    if($uid!=null){
        if($append==''){
            $append.="?uid=$uid";
        }else{
            $append.="&uid=$uid";
        }
    }
    header('Location: comments.php'.$append);
}

#################################################################

include('includes/header.php');
echo $output;
include('includes/footer.php');
