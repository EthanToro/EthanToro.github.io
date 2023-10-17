<?php
$database;
class Cms{
	public  $version = '2.2';
	private $feed = array();
	private $section = array();
    private $users = array();
    private $settings = array();
    private $userCount = false;
    private $feedCount = false;
    private $sectionCount = false;
	private $managedContent;
	 
	
	public function __construct(){
		global $database;
		$database = new Database();
		$database->connect();
	}
    public function getVersion(){
        return $this->version;
    }
	public function getSiteName(){
        global $siteName;
		return $siteName;
	}
	public function getFeed($id=null){
		global $database;
		if($id==null){
			$sql = "SELECT id FROM feed order by id asc limit 1";
			$feedInfo = $database->getRows($sql);
			if($feedInfo!==false){
				$id=$feedInfo[0]['id'];
			}else{
				return false;
			}
		}
		if(!array_key_exists($id,$this->feed)){
				$this->feed[$id] = new Feed($id);
		}
		return $this->feed[$id];
	}
	public function clearSectionsCache(){
		$this->section = array();
	}
	public function getSection($id=1){
		if(!array_key_exists($id,$this->section)){
				$this->section[$id] = new Section($id);
		}
		return $this->section[$id];
	}

    public function getUsers($offset='0', $limit='18446744073709551615',$sort='id') {
        global $user;
        if ($this->users == false) {
            global $database;
            if($user->getPermissions()->getAccessLevel()==2){
                $sql = "SELECT id FROM users order by $sort  LIMIT $offset , $limit";
            }else{
                $sql = "SELECT id FROM users WHERE accessLevel<=".$user->getPermissions()->getAccessLevel()." order by $sort LIMIT $offset , $limit";
            }
            $result = $database->getRows($sql);

            if ($result !== false) {
                foreach ($result as $u)
                    $this->users[] = new User($u['id']);
            } else die(mysql_error() . "\nCannot get users.");
        }
        return $this->users;
    }
    public function getDisabledUsers($offset='0', $limit='18446744073709551615',$sort='id') {
        global $user;
        if ($this->users == false) {
            global $database;
            if($user->getPermissions()->getAccessLevel()==2){
                $sql = "SELECT id FROM users where enabled=0 order by $sort  LIMIT $offset , $limit";
            }else{
                $sql = "SELECT id FROM users  where enabled=0 AND accessLevel<=".$user->getPermissions()->getAccessLevel()." order by $sort LIMIT $offset , $limit";
            }
            $result = $database->getRows($sql);

            if ($result !== false) {
                foreach ($result as $u)
                    $this->users[] = new User($u['id']);
            } else die(mysql_error() . "\nCannot get users.");
        }
        return $this->users;
    }
 public function getNewUsers($offset='0', $limit='18446744073709551615',$sort='id') {
        global $user;
		if ($this->users == false) {
            global $database;
			if($user->getPermissions()->getAccessLevel()==2){
				$sql = "SELECT id FROM users WHERE joinDate>".(time()-604800)." order by $sort  LIMIT $offset , $limit";
			}else{
				$sql = "SELECT id FROM users WHERE joinDate>".(time()-604800)." AND accessLevel<=".$user->getPermissions()->getAccessLevel()." order by $sort LIMIT $offset , $limit";
			}
            $result = $database->getRows($sql);

            if ($result !== false) {
                foreach ($result as $u)
                    $this->users[] = new User($u['id']);
            } else die(mysql_error() . "\nCannot get users.");
        }
        return $this->users;
    }

    public function getUser($id=false) {
        return new User($id);
    }

	public function getFeeds(){
		global $database;
		$sql = "SELECT `id` FROM feed";
		$feeds = $database->getRows($sql);
		if($feeds!==false){
			$feedsArray = array();
			foreach($feeds as $b)
				$feedsArray[] = $this->getFeed($b['id']);
			return $feedsArray;
		}else{
			return array();
		}
	}
	public function getSections($offset='0', $num=null, $str_sort='group') {
		global $database;

        $allowedSort = array('id','name','content','date','group');
        $s = explode(' ',$str_sort);
        if(!in_array($s[0],$allowedSort)) die('The sort method chosen is not allowed, "'.$str_sort.'".');
		if(strrpos('group',$str_sort)!==false) $str_sort = str_replace('group','`group`',$str_sort);
        $num = ($num==null?'18446744073709551615':$num);

		$sql = "SELECT `id` FROM sections ORDER BY $str_sort LIMIT $offset , $num";
		$sections = $database->getRows($sql);
		if($sections !== false){
			$sectionsArray = array();
			foreach($sections as $s)
				$sectionsArray[] = $this->getSection($s['id']);
			return $sectionsArray;
		}else{
			return array();
		}
	}
	
    public function getUserCount($everyone=false) {
		global $user;
        if ($this->userCount === false) {
            global $database;
			if($everyone){
				$sql = "SELECT COUNT(*) AS c FROM users";
			}else{
				$sql = "SELECT COUNT(*) AS c FROM users WHERE accessLevel<=".$user->getPermissions()->getAccessLevel();
			}
            $result = $database->getRows($sql);

            if ($result !== false)
                $this->userCount = $result[0]['c'];
            else die (mysql_error());
        }
        return intval($this->userCount);
    }	
	
    public function getFeedCount() {
        if ($this->feedCount === false) {
            global $database;
            $sql = "SELECT COUNT(*) AS c FROM feed";
            $result = $database->getRows($sql);

            if ($result !== false)
                $this->feedCount = $result[0]['c'];
            else die (mysql_error());
        }
        return intval($this->feedCount);
    }	
	
    public function getSectionCount() {
        if ($this->sectionCount === false) {
            global $database;
            $sql = "SELECT COUNT(*) AS c FROM sections";
            $result = $database->getRows($sql);

            if ($result !== false)
                $this->sectionCount = $result[0]['c'];
            else die (mysql_error());
        }
        return intval($this->sectionCount);
    }

    public function getSettings() {
        if ($this->settings == false) {
            global $database;
            $sql = "SELECT * FROM settings";
            $result = $database->getRows($sql);
            if ($result !== false) {
                foreach($result as $r)
                    $this->settings[$r['name']] = $r['value'];
            } else 
                die (mysql_error() . "\nCannot get settings.");
        }
        return $this->settings;
    }
 	public function getSetting($name) {
        global $database;
        $sql = "SELECT * FROM settings WHERE `name`='$name' limit 1";
        $result = $database->getRows($sql);

        if ($result !== false) {
        	if(count($result)>0){
           		return $result[0]['value'];
           	}else{
           		die("'$name' is not a setting what are you trying to pull?");
        	}
        } else {
            die (mysql_error() . "\nCannot get setting $name.");
        }

    }
    public function editSettings($settings) {
        global $database;
        $sqlUpdate = array();
        $fieldsUpdate = array_intersect_key($settings, array(
            'registration'      => null,
            'comments'          => null,
            'ratings'          => null,
            'uploadEnabled'       => null,
            'noteText'          => null,
            'feedsTab'          => null,
            'sectionsTab'          => null,
            'commentsTab'          => null,
            'notesTab'          => null,
            'settingsTab'          => null,
            'usersTab'          => null,
            'anonComments'          => null,
            'coolBackgrounds'          => null,
            'bans' =>null
        ));
        foreach ($fieldsUpdate as $key => $value) {
            $sql = "UPDATE settings SET `value`='$value' WHERE `name`='$key'";
            if ($database->query($sql) === false) die(mysql_error() . "\nCouldn't edit settings" . $sql);
        }
    }


    public function getSectionGroups($offset='0', $num=null, $sort='sort'){
		global $database;

        $groups = array();
		$sort = "`$sort`";
        $num = ($num==null?'18446744073709551615':$num);

		$sql = "SELECT `id` FROM sectionGroups ORDER BY $sort LIMIT $offset , $num";
		$result = $database->getRows($sql);
		if ($result !== false) {
            foreach ($result as $group)
                $groups[] = new Group($group['id']);
		} else {
			echo 'MYSQL ERROR:getSectionGroups():cms.class.php|'.mysql_error();
		}
		return $groups;
	}

    public function getSectionGroup($id){
		return new Group($id);
    }
    
public function getSectionsByGroup($id){
	global $database;
	$sql = "select * from `sections` where `group`='$id'";
	$sectionsr = $database->getRows($sql);
	$sections = array();
	foreach ($sectionsr as $section) 
		$sections[] = new Section($section['id']);
	return $sections;
}

    public function getSocial() {
        return new Social();
    }
}
