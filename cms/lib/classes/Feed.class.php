<?php
/* 
 * Feed.class.php
 * Feed class for the management of different feeds
 * and there accompanying posts and comments
 *
 * methods:                             			* RETURNS:      *
 *  setId($int_number)                  			* null          *
 *  getId()                             			* int           *
 *  getPost($id)                       				* o.Posts       *
 *  getPosts($int_offset, $int_limit, $str_sort)   	* arr o.Posts   *
 *  getPostCount()                      			* int           *
 *  getLoadedPostCount()                			* int           *
 *  getCommentCount()                   			* int           *
 */

class Feed{
	private $id;
	private $name;
	private $posts=false;
    private $count=false;
    private $settings = array();
    private $default;
	public function __construct($id=1){
		global $database;
		
		if(is_numeric($id)){
			$sql = "SELECT * from feed where `id`=$id limit 1";
			$byName=false;
		}elseif(is_string($id)) {
			$sql = "SELECT * from feed where `name`='$id' limit 1";
			$byName=true;
		}else die("Feed.class.php:32 type Error--> Type of `".gettype($id)."` when type should be int or string");		
		
		$name = $database->getRows($sql);
		if($name!==false){
			if(count($name)>0){
                $this->name = $name[0]['name'];
                $this->default = array(  
                    'Title' => $name[0]['defTitle'],
				    'Content' => $name[0]['defContent']
                );
				$this->id = $name[0]['id'];
			}else die("Feed.class.php:constructor:38 Feed $id not found!");
		}else die('line 30 of Feed.class.php: error feed id does not exist!');
		
	}

	public function getName(){
		return $this->name;
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
            $sql = "SELECT * FROM feedsettings where `feedId`=".$this->id;
            $result = $database->getRows($sql);
            if ($result !== false) {
                foreach($result as $r)
                    $this->settings[$r['name']] = $r['value'];
            } else 
                die (mysql_error() . "\nCannot get settings.|Feed.class|getSettings");
        }
        return $this->settings;
    }
 	public function getSetting($name,$def='asdfasdfasdfasdfasdf') {
        global $database;
        $sql = "SELECT * FROM feedsettings WHERE `name`='$name' AND `feedId`=".$this->id." limit 1";
        $result = $database->getRows($sql);

        if ($result !== false) {
        	if(count($result)>0){
           		return $result[0]['value'];
           	}else{
                if($def!=='asdfasdfasdfasdfasdf')
                    return $def;
                else
           		   die('thats not a setting what are you trying to pull?');
           	}
        } else {
            die (mysql_error() . "\nCannot get setting $name.");
        }

    }
    public function editSettings($settings) {
        global $database;
        $sqlUpdate = array();
        $fieldsUpdate = array_intersect_key($settings, array(
            'comments'          => null,
            'postContent'          => null,
            'postDeletion'          => null,
            'postCreation'          => null,
            'userSortable'          => null,
            'ratings'          => null,
            'noun'          => null,
            'sortBy'          => null,
            'anonComment'   => null,
            'commentReview' => null,
            'commentWait' => null,
            'uploadEnabled'       => null,
            'uploadText'       => null,
            'uploadCount'       => null,
            'uploadType'       => null,
            'hasEditor'       => null,
            'hasDefault'       => null,
            'hidden'       => null
        ));
        foreach ($fieldsUpdate as $key => $value) {
            $sql = "UPDATE feedsettings SET `value`='$value' WHERE `name`='$key' AND `feedId`=".$this->id;
            if ($database->query($sql) === false) die(mysql_error() . "\nCouldn't edit settings" . $sql);
        }
    }

	public function getPosts($offset='0', $num=null, $str_sort='date'){
		global $database;
		if($this->id!=null){
			$allowedSort = array('id','title','content','author','date','canComment','canRate','userSort');
			$s = explode(' ',$str_sort);
			if(!in_array($s[0],$allowedSort)) die('That is not an allowed sort order, '.$str_sort.'.');
			
			$num = ($num==null?'18446744073709551615':$num);
				$sql_getPosts = "SELECT ptb.id, ptb.pid, ptb.fid FROM posts_to_feed ptb LEFT JOIN posts p ON ptb.pid=p.id  WHERE ptb.fid=".$this->id." ORDER BY p.$str_sort LIMIT $offset ,$num";
				$db_posts = $database->getRows($sql_getPosts);
				if ($db_posts !== false) {
					$posts = array();
					foreach ($db_posts as $db_post)
						$posts[] = new Post($db_post['pid']);
					 
				} else die(mysql_error());
			
			return $posts;
		}else{
			echo 'no feed id selected or no feed exists';
			return array();
		}
	}

public function getDefault(){
    return $this->default;
}
public function setDefault($title, $content){
   $sql = "UPDATE `feed` set `defTitle`='$title',`defContent`='$content' WHERE `id`='".$this->id."'";
}

    public function getPostCount(){
        if ($this->count === false) {
            global $database;
            $sql = "SELECT COUNT(*) AS c FROM posts_to_feed WHERE fid=".$this->id;
            $result = $database->getRows($sql);

            if ($result !== false)
                $this->count = $result[0]['c'];
            else die(mysql_error());
        }
        return $this->count;
    }

    public function getLoadedPostCount() {
        if ($this->posts !== false)
            return count($this->posts);
        return 0;
    }

    public function getCommentCount(){
        $posts = $this->getPosts();
        $totalComments = 0;
        foreach($posts as $post)
            $totalComments += $post->getCommentCount();
        return $totalComments;
    }

	public function getPost($id=false){
		global $database;
		
		if($id!=false){
			return new Post($id);
		}else return false;
	}

	public function getId(){
		return $this->id;
	}
	
	public function destroy() {
        global $database;
        $posts= $this->getPosts();
        foreach($posts as $p){
            $p->destroy();
        }
        $sql = 'DELETE FROM `feed` WHERE `id`='.$this->id;
        if ($database->query($sql) === false) die(mysql_error() . "\nCannot destroy feed");
        $sql = 'DELETE FROM `feedsettings` WHERE `feedId`='.$this->id;
        if ($database->query($sql) === false) die(mysql_error() . "\nCannot destroy feed");

    }
	public function setName($name) {
        global $database;
        $sql = "UPDATE feed SET `name`='$name' WHERE `id`={$this->id}";
        if ($database->query($sql) === false) die(mysql_error() . "\nCould not set the name of the feed");
        $this->name = $name;
    }
 static function create($name) {
        global $database;
        $name = strval($name);
        $sql = "INSERT INTO feed (name) VALUES ('" . $name . "')";
        if ($database->query($sql) === false) die(mysql_error() . "\nCouldn't create new feed");
        $fid = mysql_insert_id();
        $sql = "
INSERT INTO `feedsettings` (`id`, `feedId`, `name`, `value`, `default`) VALUES
(null, $fid, 'noun',        'Post', 'Post'),
(null, $fid, 'sortBy',      'date desc', 'date desc'),
(null, $fid, 'ratings',     '0',    '0'),
(null, $fid, 'comments',    '0',    '0'),
(null, $fid, 'postContent', '1',    '1'),
(null, $fid, 'anonComment', '0',    '0'),
(null, $fid, 'commentReview','1',    '1'),
(null, $fid, 'commentWait',  '2',    '2'),
(null, $fid, 'uploadType', '',     ''),
(null, $fid, 'uploadCount', '1',     '1'),
(null, $fid, 'uploadText',  'Please type text for the client here',    'Please type text for the client here'),
(null, $fid, 'uploadEnabled', '0',    '0'),
(null, $fid, 'postCreation', '1',    '1'),
(null, $fid, 'postDeletion', '1',    '1'),
(null, $fid, 'userSortable', '0',    '0'),
(null, $fid, 'hasEditor', '1',    '1'),
(null, $fid, 'hasDefault', '0',    '0'),
(null, $fid, 'hidden', '0',    '0')
;";
        if ($database->query($sql) === false) die(mysql_error() . "\nCouldn't create new feed");

        return new Feed($fid);
    }
}
