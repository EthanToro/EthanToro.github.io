<?php
class User{
	private $id,
        $userName,
        $email,
        $name, 
        $joinDate, 
        $enabled, 
        $comments,
        $canComment,
        $commentCount=false,
		$permissions;
	
	function __construct($userId) {
		$this->id = $userId;
		if ($userId !== false) {
            global $database;
            $sql = 'SELECT username, email, name, joinDate, enabled, canComment, accessLevel FROM users WHERE `id`='.$userId.' LIMIT 1';
            $result = $database->getRows($sql);
            if ($result !== false) {
                $result = $result[0];
                $this->username         = $result['username'];
                $this->email            = $result['email'];
                $this->name             = $result['name'];
                $this->joinDate         = $result['joinDate'];
                $this->enabled          = $result['enabled'];
                $this->canComment       = $result['canComment'];
                $this->id               = $userId;
				$this->permissions = new Permissions($result['accessLevel']);
            } else die(mysql_error() . "\nCan't construct user info.");
        }
	}
    public function getId() {
        return $this->id;
    }
    public function getUsername() {
        return htmlentities($this->username);
    }
	public function getAlias() {
        return htmlentities($this->name);
    }
    public function getEmail() {
        return htmlentities($this->email);
    }
    public function getName() {
        return htmlentities($this->name);
    }
	public function getPermissions(){
		return $this->permissions;
	}
	public function deleteUser() { //todo does this delete comments also?
        global $database;
        $sql = 'DELETE FROM users WHERE id='.$this->id;
        if ($database->query($sql) === false) die(mysql_error() . "\nCannot delete user");
    }
	public function setPassword($password) {
        global $database, $crypt;
        $password = $crypt->hash($password);
        $sql = "UPDATE users SET `password`='$password' WHERE id=".$this->id." LIMIT 1";
        if ($database->query($sql) === false) die(mysql_error() . "\nCannot update password" . $sql);
    }
    public function destroy() {//todo does this delete comments also?
        global $database;
        $sql = 'DELETE FROM users WHERE id='.$this->id;
        if ($database->query($sql) === false) die(mysql_error() . "\nCannot destroy user");
    }
    public function edit($userInfo) {
        global $database;
        $sqlUpdate = array();
        $fieldsUpdate = array_intersect_key($userInfo, array(
            'username'      => null,
            'email'         => null,
            'name'          => null,
            'enabled'       => null,
            'canComment'    => null,
            'accessLevel'   => null
        ));
        foreach ($fieldsUpdate as $key => $value) {
            $this->$key = $value;
            $sqlUpdate[] = "`$key`='$value'";
        }
        $sqlUpdate = implode(', ', $sqlUpdate);
        $sql = "UPDATE users SET ".$sqlUpdate." WHERE `id`=".$this->id;
        if($database->query($sql) === false) die(mysql_error() . "\nCould not edit user");
    }
    public function getCommentCount() {
        if ($this->commentCount === false) {
            global $database;
            $sql = 'SELECT COUNT(*) AS c FROM comments WHERE `uid`='.$this->id;
            $result = $database->getRows($sql);
            if ($result !== false) {
                $this->commentCount = $result[0]['c'];
            } else die(mysql_error() . "\nCan't get comment count");
        }
        return $this->commentCount;
    }

    public function getComments($offset='0', $num=null, $sort='date desc') {
        global $database;

        $this->comments = array();

        $sql_getComments = "SELECT id FROM comments WHERE uid='{$this->id}' ORDER BY id LIMIT {$offset} , {$num}";
        $db_comments = $database->getRows($sql_getComments);
        if ($db_comments !== false) {
            foreach ($db_comments as $db_comment)
                $this->comments[] = new Comment($db_comment['id']);
        } else {
            throw new Exception('sql error, user->getComments()');
        }
        return $this->comments;
    }
    public function getCanComment() {
        return $this->canComment;
    }
    public function getJoinDate($format = 'D, m Y H:i:sA') {
        return date($format, $this->joinDate);
    }
    public function isEnabled() {
        return $this->enabled;
    }

    public function getAll() {
        return array(
            'id'            => $this->id,
            'username'      => $this->username,
            'email'         => $this->email,
            'name'          => $this->name,
            'joinDate'      => $this->joinDate,
            'enabled'       => $this->enabled,
            'permissions'   => $this->permissions,
            'canComment'    => $this->canComment,
            'commentCount'  => $this->getCommentCount()
        );
    }
	public function create($userInfo) {
        global $database;

        $fieldsSet = array_merge(array(
            'joinDate'      => time(),
            'accessLevel'   => 0,
            'canComment'    => 1,
            'enabled'       => 0
        ), $userInfo);
        $fieldsSet = array_intersect_key($fieldsSet, array(
            'username'      => null,
            'email'         => null,
            'name'          => null,
            'joinDate'      => null,
            'accessLevel'   => null,
            'canComment'    => null,
            'enabled'       => null
        ));

        if (!isset($fieldsSet['username']) || empty($fieldsSet['username']) ||
            !isset($fieldsSet['email'])    || empty($fieldsSet['email']))
                die('cannot create user, unset fields');

        $sqlColumns = array();
        $sqlValues = array();
        foreach ($fieldsSet as $key => $value) {
            $sqlColumns[] = "`$key`";
            $sqlValues[] = "'$value'";
        }
        $sqlColumns = implode(', ', $sqlColumns);
        $sqlValues = implode(', ', $sqlValues);

        // Create the db entries
        $sql = "INSERT INTO users ({$sqlColumns}) VALUES ({$sqlValues})";
        if ($database->query($sql) === false) die(mysql_error() . "\nCannot create user");

        foreach ($fieldsSet as $key => $value)
            $this->$key = $value;

        $this->id = mysql_insert_id();
    }
	
	public static function checkUsername($username) {
        global $database;
        $sql = "SELECT 1 from users where `username`='$username'";
        if (($result = $database->getRows($sql)) !== false) {
            if (count($result) == 0)
                return false;
            return true;
        }
        die('!');
    }

    public function refresh(){
        global $database;
        $userId = $this->id;
        $sql = 'SELECT username, email, name, joinDate, enabled, canComment, accessLevel FROM users WHERE `id`='.$userId.' LIMIT 1';
        $result = $database->getRows($sql);
        if ($result !== false) {
            $result = $result[0];
            $this->username         = $result['username'];
            $this->email            = $result['email'];
            $this->name             = $result['name'];
            $this->joinDate         = $result['joinDate'];
            $this->enabled          = $result['enabled'];
            $this->canComment       = $result['canComment'];
            $this->id               = $userId;
            $this->permissions = new Permissions($result['accessLevel']);
        }
    }

}
