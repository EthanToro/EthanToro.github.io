<?php
/* 
 * Class for the getting and displaying of posts
 * 
 * Functions:
 *   getContent()           - returns the post's main body of content
 *   getTitle()             - returns the post's title
 *   getAuthor()            - returns the name of the author of the post
 *   getDate($s_format)     - returns the date the post was made on, formatted in the style of php's date() function
 *   getThumb()             - returns a url with the thumbnail for the given post.
 *   getRating()            - returns an number representing the rating of the post.
 *   getTags()              - returns an array of strings, each a unique tag assigned to this post
 *   getComments()          - returns an array of objects for all of the comments the post has
 *   
 *   create($aa_postInfo)   - given an array with all of the required post information, will create the db entry for the post
 *   edit($aa_postInfo)     - given an array with all of the required post information, and an id, will update the db entry for the post
 *   addTag($s_tag)         - adds a new tag to the post.
 *
 *   addRating($n_rating, $n_uid) - creates a new rating for the post.
 */

class Post{
    private $id;
    private $title;
    private $content;
    private $author;
    private $date;
    private $file;
    private $hasFile;
    private $canComment;
    private $canRate;

    private $comments = array();
    private $commentCount = false;
    private $tags = array();
    private $rating;
    private $userSort;

    public function __construct($postId=false) {
        if ($postId !== false) {
            global $database;

            $sql_post	= "SELECT * FROM posts WHERE `id`=$postId limit 1";
            $postInfo = $database->getRows($sql_post);
            //var_dump($sql_post,$postInfo,mysql_error());
            if ($postInfo != false) {
                $postInfo = $postInfo[0];
                $this->title        = $postInfo['title'];
                $this->content      = $postInfo['content'];
                $this->author       = $postInfo['author'];
                $this->file         = $postInfo['file'];
                $this->date         = $postInfo['date'];
                $this->canComment   = $postInfo['canComment'];
                $this->id           = $postId;
                $this->hasFile		= (isset($postInfo['file'])&&!empty($postInfo['file']))?true:false;
                $this->canComment   = $postInfo['canComment'];
                $this->canRate      = $postInfo['canRate'];
                $this->userSort     = $postInfo['userSort'];
                if($this->hasFile){
                    $fileHandelr = $this->getFeed()->getSetting('uploadType');
                    $this->file = new $fileHandelr($postInfo['file']);
                }
            } else die(mysql_error() . "\ncannot construct post");
        }
    }
    public function disableComments(){
        $this->canComment = false;
    }
    public function getFeed() {
        global $database;
        $sql = "SELECT fid FROM posts_to_feed WHERE `pid`='{$this->id}' LIMIT 1";
        if (($result = $database->getRows($sql)) && count($result))
            return new Feed($result[0]['fid']);
        else {
            $error = "SQL ERR: Post=>getFeed() : Post.class.php <br>\n" . mysql_error() /*. $sql*/;
            throw new Exception($error);
        }
    }
    public function disableRating(){
        $this->canRate = false;
    }
    public function hasFile(){
        return $this->hasFile;
    }
    public function getFile(){
        return $this->file;
    }
    public function getId(){
        return $this->id;
    }
    public function getCanComment(){
        return $this->canComment;
    }
    public function getCanRate(){
        return $this->canRate;
    }
    public function getContent() {
        return $this->content;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function getDate($format = 'D, m Y H:i:sA') {
        return date($format, $this->date);
    }
    public function getUnixDate() {
        return $this->date;
    }

    public function getCommentCount() {
        global $database;
        $sql = "select count(*) as count from comments where postid=".$this->id;
        $result = $database->getrows($sql);
        if ($result !== false && count($result) > 0) {
            $commentCount = $result[0]['count'];
        } else{
            $commentCount = '0';
        }
        return $commentCount;
    }
    public function getApprovedCommentCount() {
        global $database;
        $sql = "select count(*) as count from comments where type=1 AND postid=".$this->id;
        $result = $database->getrows($sql);
        if ($result !== false && count($result) > 0) {
            $commentCount = $result[0]['count'];
        } else{
            $commentCount = '0';
        }
        return $commentCount;
    }
    public function getLastCommenter() {
        global $database;
        $sql = "select uid from blog.comments where postId=".$this->id." order by id desc limit 1";
        $result = $database->getrows($sql);
        if ($result !== false && count($result) > 0) {
            $commentCount = $result[0]['uid'];
        } else{
            $commentCount = false;
        }
        return $commentCount;
    }
    public function getComments($offset='0', $num=null, $sort='date desc') {
        global $database;
        $num = ($num==null?'18446744073709551615':$num);
        $comments = array();
        $sql_getComments = "SELECT id FROM comments WHERE postId='{$this->id}' ORDER BY id LIMIT {$offset} , {$num}";
        $db_comments = $database->getRows($sql_getComments);
        if ($db_comments !== false) {
            foreach ($db_comments as $db_comment)
                $comments[] = new Comment($db_comment['id']);
        } else {
            throw new Exception('sql error, post->getComments()');
        }
        return $comments;
    }
    public function getApprovedComments($offset='0', $num=null, $sort='date desc') {
        global $database;
        $num = ($num==null?'18446744073709551615':$num);
        $comments = array();
        $sql_getComments = "SELECT id FROM comments WHERE postId={$this->id} AND type=1 ORDER BY id LIMIT {$offset} , {$num}";
        $db_comments = $database->getRows($sql_getComments);
        if ($db_comments !== false) {
            foreach ($db_comments as $db_comment)
                $comments[] = new Comment($db_comment['id']);
        } else {

            throw new Exception('sql error, post->getComments()');
        }
        return $comments;
    }
    static function getPostsWithComments() {
        global $database;
        $posts = array();
        $sql_getPosts = "SELECT id from Posts where id in(SELECT postId from `comments` where type=0 group by postId)";
        $db_posts = $database->getRows($sql_getPosts);
        if ($db_posts !== false) {
            foreach ($db_posts as $db_post)
                $posts[] = new Post($db_post['id']);
        } else {
            throw new Exception('sql error, post::getPostsWithComments()');
        }
        return $posts;
    }

    public function makeComment($commentInfo) {
        $commentInfo['postId'] = $this->id;
        $comment = Comment::create($commentInfo);
        return $comment;
    }

    public function getTags() {
        global $database;
        $sql = "SELECT t.tag, COUNT(t.tag) as count
            FROM tag t LEFT JOIN tags_to_posts tp
            ON t.id=tp.tid
            WHERE tp.pid=".$this->id."
            GROUP BY tp.tid";
        $tags = array();
        if (($result = $database->getRows($sql)) !== false) {

            foreach ($result as $r) {
                $tags[] = $r['tag'];
            }

        } else
            die(mysql_error() . "\ncannot get tags for post");

        return $tags;
    }

    public function addTag($tag) {
        if ($tag) {
            global $database;

            // does a tag with that name already exist?
            $sql = "SELECT * FROM tags WHERE tag LIKE '%".$tag."%'";
            if (($currentTags = $database->getRows($sql)) === false) die(mysql_error());
            if (count($currentTags) == 0) {
                $tag = strtolower(trim($tag));
                // no tags with that name already, create a new one
                $sql = "INSERT INTO tags (tag) VALUES ('".$tag."')";
                if ($database->query($sql) === false) die(mysql_error() . "\ncannot add tag");

                // connect newly created tag to post
                $tagId = mysql_insert_id();
                $sql = "INSERT INTO tags_to_posts (pid, tid) VALUES ('".$this->id."', '".$tagId."')";
                if ($database->query($sql) === false) die(mysql_error() . "\ncannot add tag");
            } else {
                // connect already existing tag to post
                $tagId = $currentTags[0]['id'];
                $sql = "INSERT INTO tags_to_posts (pid, tid) VALUES ('".$this->id."', '".$tagId."')";
                if ($database->query($sql) === false) die(mysql_error() . "\ncannot add tag");
            }
            $this->tags[] = $tag;
        } else
            die('cannot add null tag');
    }

    public function getRating() {
        if ($this->rating == false) {
            global $database;
            // get all of the ratings and average them, if no ratings rating=0
            $sql = "SELECT rating FROM ratings WHERE pid=".$this->id;
            if (($result = $database->getRows($sql)) !== false) {
                $this->rating = 0;
                if (count($result) > 0) {
                    foreach ($result as $r)
                        $this->rating += $r['rating'];
                    $this->rating /= count($result);
                }
            } else
                die (mysql_error() . "\ncannot get rating for post");
        }
        return $this->rating;
    }

    public function addRating($rating=false, $userId=false) {
        if ($rating !== false && $userId !== false) {
            global $database;
            $rating = intval($rating);
            $sql = "INSERT INTO ratings (rating, pid, uid) VALUES ('".$rating."', '".$this->id."', '".$userId."')";
            if ($database->query($sql) === false) die (mysql_error() . "\ncannot add rating");
            $this->rating = false;
            $this->getRating();
        } die ('cannot add rating, invalid arguments supplied for addRating');
    }

    public function create($postInformation) {
        global $database, $feed;

        $fid = $feed->getId();
        Post::defragUserSort($fid);
        $fieldsSet = array_merge(array(
            'date'          => time(),
            'file'         => '',
            'canComment'    => 1,
            'canRate'       => 1
        ), $postInformation);
        $fieldsSet = array_intersect_key($fieldsSet, array(
            'title'         => null,
            'content'       => null,
            'author'        => null,
            'date'          => null,
            'file'         => null,
            'canComment'    => null,
            'canRate'       => null,
            'userSort'      => Post::getMaxUserSort()
        ));

        if (
            (!isset($fieldsSet['title'])   && !empty($fieldsSet['title'])) ||
            (!isset($fieldsSet['author'])  && !empty($fieldsSet['author'])) ||
            (!isset($fieldsSet['content']) && !empty($fieldsSet['content']))
        )
        die('cannot create post object, unset fields');
        $sqlColumns = array();
        $sqlValues = array();
        foreach ($fieldsSet as $key => $value) {
            $sqlColumns[] = "`$key`";
            $sqlValues[] = "'$value'";
        }
        $sqlColumns = implode(', ', $sqlColumns);
        $sqlValues = implode(', ', $sqlValues);

        // Create db entries
        $sql = "INSERT INTO posts ({$sqlColumns}) VALUES ({$sqlValues})";
        if($database->query($sql) === false) die(mysql_error() . "\nCannot create posts, cannot create post record.");

        $this->id = mysql_insert_id();

        $sql = "INSERT INTO posts_to_feed (pid, fid) VALUES ('{$this->id}', '{$fid}')";
        if($database->query($sql) === false) die(mysql_error() . "\nCannot create post, cannot create ptb record.");

        foreach ($fieldsSet as $key => $value)
            $this->$key = $value;
        return $this->id;
    }

    public function edit($postInformation) {
        global $database;
        $feed = $this->getFeed();
        $fid = $feed->getId();
        Post::defragUserSort($fid);
        $sqlUpdate = array();
        // do_dump($postInformation);
        $fieldsUpdate = array_intersect_key($postInformation, array(
            'title'         => null,
            'content'       => null,
            'author'        => null,
            'date'          => null,
            'file'         => null,
            'canComment'    => null,
            'canRate'       => null
        ));
        // do_dump($fieldsUpdate);
        foreach ($fieldsUpdate as $key => $value) {
            $this->$key = $value;
            $sqlUpdate[] = "`$key`='$value'";
        }
        $sqlUpdate = implode(', ', $sqlUpdate);
        $sql = "UPDATE posts SET ".$sqlUpdate." WHERE `id`=".$this->id;
        if($database->query($sql) === false) die(mysql_error() . "\nCannot update post\n<br>\n" . $sql);
    }

    public function destroy() {
        global $database,  $uploadPath;
        $sql = "SELECT * FROM `posts` where `id`=".$this->id;
        $postInfo = $database->getRows($sql);
        if($postInfo!==false){
            if(isset($postInfo[0]['thumb'])){
                $filename = $postInfo[0]['thumb'];
                unlink($uploadPath . $filename);
            }
            $sql = "DELETE FROM `posts` WHERE `id`=".$this->id;
            $database->query($sql);
        }
    }
    public function getUserSort(){
        return $this->userSort;
    }
    public function setUserSort($value){
        if (is_numeric($value)) {
            global $database;
            $value = intval($value);
            $sql = "UPDATE posts set `userSort`=$value WHERE `id`={$this->id}";
            if ($database->query($sql) === false) die(mysql_error() . "\nCouldn't set sort value of group.");
            $this->sort = $value;
        } else die('sort value must be numeric');
    }

    public static function defragUserSort($id){
        global $cms;
        $feed = $cms->getFeed($id);
        $posts = $feed->getPosts(0,null,'userSort');
        for($i=0;$i<count($posts);$i++){
            $posts[$i]->setUserSort($i);
        }
    }
    public static function getMaxUserSort(){
        global $database;
        $sql = 'SELECT MAX(userSort) as maxSort FROM posts';
        if (($result = $database->getRows($sql)) === false) die(mysql_error() . "\nCould not get maximum sort value for group.");
        if (count($result) > 0)
            return intval($result[0]['maxSort']);
        return 0;
    }

    public function getSample($maxLength){
        $html = substr($this->getContent(),0,$maxLength);
        $html = Html::close_tags($html).'&hellip;';
        return $html;
    }
    static public function exists($pid) {
       global $database;
        $sql = "SELECT COUNT(*) AS c FROM posts WHERE `id`='{$pid}'";
        if (($results = $database->getRows($sql)) !== false) {
            return ($results[0]['c'] > 0);
        } else {
            $error = "SQL ERR: Post::exists() | Post.class.php <br>\n" . mysql_error() /*.sql*/;
            throw new Exception($error);
        }
    }
}
?>
