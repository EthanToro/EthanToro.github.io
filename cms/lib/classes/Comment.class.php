<?php 
class Comment{
    private
        $id,
        $parent,
        $parentId,
        $post,
        $postId,
        $content,
        $date,
        $user,
        $userId,
        $email,
        $name,
        $rating;

    private
        $isAnon,
        $isApproved,
        $isSpam;

    public function __construct($id){
        global $database;
        $sql = "Select * from comments where `id`='$id' limit 1";
        $result = $database->getRows($sql);
        if ($result!==false) {
            $result = $result[0];
            $this->id           = $id;
            $this->parentId     = $result['parentId'];
            $this->postId       = $result['postId'];
            $this->content      = $result['content'];
            $this->date         = $result['date'];
            $this->userId       = $result['uid'];
            $this->name         = $result['name'];
            $this->email        = $result['email'];
            $this->isAnon       = $result['isAnon'];
            $this->isApproved   = ($result['type']==1);
            $this->isSpam       = ($result['type']==2);
            $this->rating       = $result['rating'];

            if (isset($result['parentId'])&&$result['parentId']!='0'){
                $this->parent       = new Comment($result['parentId']);
            }
            if (isset($result['postId'])&&$result['postId']!='0'){
                $this->post         = new Post($result['postId']);
            }
            if (isset($result['uid']) && !$this->isAnon){
                $this->user         = new User($result['uid']);
            }
        } else {
            $error = "SQL ERR: Comment::__construct() | Comment.class.php <br>\n" . mysql_error() /*. $sql*/;
            throw new Exception($error);
        }
    }

    public function getChildren($offset='0', $limit=null, $sort='date desc'){
        global $database;
        $limit = ($limit==null?'18446744073709551615':$num);

        $sql = "SELECT id FROM COMMENTS WHERE `parentId`='{$this->id}' ORDER BY {$sort} LIMIT {$offset} , {$limit}";
        if (($result = $database->getRows($sql)) !== false) {
            $a_return = array();
            foreach ($result as $r)
                $a_return[] = new Comment($r['id']);

            return $a_return;
        } else {
            $error = "SQL ERR: Comment::getChildren() | Comment.class.php <br>\n" . mysql_error() /*. $sql*/;
            throw new Exception($error);
        }
    }
    public function isAnon() {
        return ($this->isAnon);
    }
    public function isApproved() {
        return ($this->isApproved);
    }
    public function getName() {
        if ($this->userId)
            return $this->user->getAlias();
        else
            return htmlentities($this->name);
    }
    public function getEmail() {
        if ($this->userId)
            return $this->user->getEmail();
        else
            return htmlentities($this->email);
    }
    public function getParent() {
        if ($this->parentId)
            return $this->parent;
        else
            return false;
    }
    public function getParentId() {
        return $this->parentId;
    }
    public function getRating() {
        return $this->rating;
    }
    public function getPost() {
        if ($this->postId)
            return $this->post;
        else 
            return false;
    }
    public function getPostId() {
        return $this->postId;
    }
    public function getId(){
        return $this->id;	
    }
    public function getUser(){
        return $this->user;
    }
    public function getUserId() {
        return $this->userId;
    }
    public function getContent(){
        return htmlentities($this->content);
    }
    public function getDate($dateFormat=false){
        if ($dateFormat)
            return date($dateFormat, $this->date);
        else
            return $this->date;
    }

    static public function exists($cid) {
       global $database;
        $sql = "SELECT COUNT(*) AS c FROM comments WHERE `id`='{$cid}'";
        if (($results = $database->getRows($sql)) !== false) {
            return ($results[0]['c'] > 0);
        } else {
            $error = "SQL ERR: Comment::exists() | Comment.class.php <br>\n" . mysql_error() /*.sql*/;
            throw new Exception($error);
        }
    }

    static public function getCount($uid, $pid) {
        global $database;

        $a_where = array();

        if ($uid)
            $a_where[] = "`uid`='{$uid}'";
        if ($pid)
            $a_where[] = "`postId`='{$pid}'";
        $a_where = implode(' AND ', $a_where);

        $sql = "SELECT COUNT(*) AS c FROM comments " . (($a_where) ? "WHERE {$a_where}" : '');
        if (($result = $database->getRows($sql)) !== false)
            return $result[0]['c'];
        else {
            $error = "SQL ERR: Comment::getCount() | Comment.class.php <br>\n" . mysql_error() /*. $sql*/;
            throw new Exception($error);
        }
    }
    static public function getPendingCount($uid, $pid) {
        global $database;

        $a_where = array();

        if ($uid)
            $a_where[] = "`uid`='{$uid}'";
        if ($pid)
            $a_where[] = "`postId`='{$pid}'";
        $a_where[] = "`type`=0";
        $a_where = implode(' AND ', $a_where);

        $sql = "SELECT COUNT(*) AS c FROM comments " . (($a_where) ? "WHERE {$a_where}" : '');
        if (($result = $database->getRows($sql)) !== false)
            return $result[0]['c'];
        else {
            $error = "SQL ERR: Comment::getCount() | Comment.class.php <br>\n" . mysql_error() /*. $sql*/;
            throw new Exception($error);
        }
    }
    static public function getApprovedCount($uid, $pid) {
        global $database;

        $a_where = array();

        if ($uid)
            $a_where[] = "`uid`='{$uid}'";
        if ($pid)
            $a_where[] = "`postId`='{$pid}'";
        $a_where[] = "`type`=1";
        $a_where = implode(' AND ', $a_where);

        $sql = "SELECT COUNT(*) AS c FROM comments " . (($a_where) ? "WHERE {$a_where}" : '');
        if (($result = $database->getRows($sql)) !== false)
            return $result[0]['c'];
        else {
            $error = "SQL ERR: Comment::getCount() | Comment.class.php <br>\n" . mysql_error() /*. $sql*/;
            throw new Exception($error);
        }
    }
    static public function getSpamcount($uid, $pid) {
        global $database;

        $a_where = array();

        if ($uid)
            $a_where[] = "`uid`='{$uid}'";
        if ($pid)
            $a_where[] = "`postId`='{$pid}'";
        $a_where[] = "`type`=2";
        $a_where = implode(' AND ', $a_where);

        $sql = "SELECT COUNT(*) AS c FROM comments " . (($a_where) ? "WHERE {$a_where}" : '');
        if (($result = $database->getRows($sql)) !== false)
            return $result[0]['c'];
        else {
            $error = "SQL ERR: Comment::getCount() | Comment.class.php <br>\n" . mysql_error() /*. $sql*/;
            throw new Exception($error);
        }
    }

    static public function getAllComments($offset='0', $limit=null, $sort='date desc', $uid=false, $pid=false) {
        global $database;
        $limit = ($limit==null?'18446744073709551615':$limit);
        $a_where = array();

        if ($uid)
            $a_where[] = "`uid`='{$uid}'";
        if ($pid)
            $a_where[] = "`postId`='{$pid}'";
        $a_where = implode(' AND ', $a_where);

        $sql = "SELECT id FROM comments " . (($a_where) ? "WHERE {$a_where}" : '') . " ORDER BY {$sort} LIMIT {$offset} , {$limit}";

        if(($result = $database->getRows($sql)) !== false) {
            $a_r = array();
            foreach ($result as $r)
                $a_r[] = new Comment($r['id']);

            return $a_r;
        } else {
            $error = "SQL ERR: Comment::getComments() | Comment.class.php <br>\n" . mysql_error() /*. $sql*/;
            throw new Exception($error);
        }
    }
    static public function getSpamComments($offset='0', $limit=null, $sort='date desc', $uid=false, $pid=false) {
        global $database;
        $limit = ($limit==null?'18446744073709551615':$limit);
        $a_where = array();

        if ($uid)
            $a_where[] = "`uid`='{$uid}'";
        if ($pid)
            $a_where[] = "`postId`='{$pid}'";
        $a_where[] = "`type`=2";
        $a_where = implode(' AND ', $a_where);

        $sql = "SELECT id FROM comments " . (($a_where) ? "WHERE {$a_where}" : '') . " ORDER BY {$sort} LIMIT {$offset} , {$limit}";

        if(($result = $database->getRows($sql)) !== false) {
            $a_r = array();
            foreach ($result as $r)
                $a_r[] = new Comment($r['id']);

            return $a_r;
        } else {
            $error = "SQL ERR: Comment::getComments() | Comment.class.php <br>\n" . mysql_error() /*. $sql*/;
            throw new Exception($error);
        }
    }
     static public function getApprovedComments($offset='0', $limit=null, $sort='date desc', $uid=false, $pid=false) {
        global $database;
        $limit = ($limit==null?'18446744073709551615':$limit);
        $a_where = array();

        if ($uid)
            $a_where[] = "`uid`='{$uid}'";
        if ($pid)
            $a_where[] = "`postId`='{$pid}'";
        $a_where[] = "`type`=1";
        $a_where = implode(' AND ', $a_where);

        $sql = "SELECT id FROM comments " . (($a_where) ? "WHERE {$a_where}" : '') . " ORDER BY {$sort} LIMIT {$offset} , {$limit}";

        if(($result = $database->getRows($sql)) !== false) {
            $a_r = array();
            foreach ($result as $r)
                $a_r[] = new Comment($r['id']);

            return $a_r;
        } else {
            $error = "SQL ERR: Comment::getComments() | Comment.class.php <br>\n" . mysql_error() /*. $sql*/;
            throw new Exception($error);
        }
    }
    static public function getPendingComments($offset='0', $limit=null, $sort='date desc', $uid=false, $pid=false) {
        global $database;
        $limit = ($limit==null?'18446744073709551615':$limit);
        $a_where = array();

        if ($uid)
            $a_where[] = "`uid`='{$uid}'";
        if ($pid)
            $a_where[] = "`postId`='{$pid}'";
        $a_where[] = "`type`=0";
        $a_where = implode(' AND ', $a_where);

        $sql = "SELECT id FROM comments " . (($a_where) ? "WHERE {$a_where}" : '') . " ORDER BY {$sort} LIMIT {$offset} , {$limit}";

        if(($result = $database->getRows($sql)) !== false) {
            $a_r = array();
            foreach ($result as $r)
                $a_r[] = new Comment($r['id']);

            return $a_r;
        } else {
            $error = "SQL ERR: Comment::getComments() | Comment.class.php <br>\n" . mysql_error() /*. $sql*/;
            throw new Exception($error);
        }
    }

    static public function create($commentInfo) {
        global $database;

        $commentInfo = array_merge(array(
            'date'          => time(),
            'rating'        => 15,
            'type'          => 0,
        ), $commentInfo);

        $commentInfo = array_intersect_key($commentInfo, array(
            'parentId'      => null,
            'postId'        => null,
            'content'       => null,
            'date'          => null,
            'uid'           => null,
            'name'          => null,
            'email'         => null,
            'type'          => null,
            'isAnon'        => null,
            'archived'      => null,
            'rating'        => null
        ));

        $sqlColumns = array();
        $sqlValues = array();
        foreach ($commentInfo as $key => $value) {
            $sqlColumns[] = "`$key`";
            $sqlValues[] = "'$value'";
        }
        $sqlColumns = implode(', ', $sqlColumns);
        $sqlValues = implode(', ', $sqlValues);

        $sql = "INSERT INTO comments ({$sqlColumns}) VALUES ({$sqlValues})";
        if ($database->query($sql) === false) die(mysql_error() . "\nCannot create comment, cannot create comment record.");

        return new Comment(mysql_insert_id());
    }

    public function edit($commentInfo) {
        global $database;

        $commentInfo = array_intersect_key($commentInfo, array(
            'parentId'      => null,
            'postId'        => null,
            'content'       => null,
            'date'          => null,
            'uid'           => null,
            'name'          => null,
            'email'         => null,
            'type'          => null,
            'archived'      => null,
            'isAnon'        => null,
            'rating'        => null
        ));

        $sqlValues = array();
        foreach ($commentInfo as $key => $value) {
            $sqlValues[] = "`{$key}`='{$value}'";
        }
        $sqlValues = implode(', ', $sqlValues);

        $sql = "UPDATE comments SET {$sqlValues} WHERE `id`='{$this->id}'";
        if ($database->query($sql) === false) die(mysql_error() . "\nCannot edit comment, cannot edit comment record.");
    }

    public function destroy() {
        global $database;
        $sql = "DELETE FROM comments WHERE `id`='{$this->id}' LIMIT 1";
        if ($database->query($sql) === false) die(mysql_error() . "\nCannot destroy user");
    }
    public function approveComment(){
        $info = array();
        $info['type'] = 1;
        $this->edit($info);
    }
    public function MarkSpamComment(){
        $info = array();
        $info['type'] = 2;
        $this->edit($info);
    }
    public function archiveComment(){
        $info = array();
        $info['archived'] = 1;
        $this->edit($info);
    }
}
