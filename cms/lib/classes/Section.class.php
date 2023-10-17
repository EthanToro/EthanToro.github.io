<?php
/* 
 * Class for the managing of individual sections
 *
 * Functions:
 *   getContent()                - returns the section's content, probably the only display truly needed
 *   getName()                   - returns the section's name
 *   getDate($s_format)          - returns the date the section was last updated on
 *   edit($a_sectionData)        - sets the section's information to that of the values given, also updates db
 *   set($a_sectionData)         - when creating a new section, creates a db entry and sets all of the data for object
 *   destroy()                   - destroy all the data for this section, from db and all
 *
 */

class Section{
	private $id;
	private $name;
	private $group;
	private $content;
    private $date;
    private $settings = array();
	
	public function __construct($id=false){
        //if id not set, don't try and load information.
        if ($id !== false) {
            global $database;
            
            if(is_numeric($id)){
                $sql = "SELECT * from sections where `id`=$id limit 1";
            }elseif(is_string($id)) {
                $sql = "SELECT * from sections where `name`='$id' limit 1";
            }else die("Section.class.php:23 type Error--> Type of `".gettype($id)."` when type should be int or string");		
            
            $sectionInfo = $database->getRows($sql);
            if($sectionInfo!==false){
                if(count($sectionInfo)>0){
                    $sectionInfo = $sectionInfo[0];
                    $this->id = $sectionInfo['id'];
                    $this->name = $sectionInfo['name'];
    				$this->content = fixIT($sectionInfo['content']);
                    $this->date = $sectionInfo['date'];

                    $this->group = new Group($sectionInfo['group']);
                }else die("Section.class.php:constructor:45 section $id not found!");
            }else die("Section.class.php:constructor:46 mysql error-->".mysql_error());
        }
    }
    public function getSettings() {
        if ($this->settings == false) {
            global $database;
            $sql = "SELECT * FROM sectionsettings where `feedId`=".$this->id;
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
        $sql = "SELECT * FROM sectionsettings WHERE `name`='$name' AND `feedId`=".$this->id." limit 1";
        $result = $database->getRows($sql);

        if ($result !== false) {
            if(count($result)>0){
                return $result[0]['value'];
            }else{
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
            'uploadEnabled'       => null
        ));
        foreach ($fieldsUpdate as $key => $value) {
            $sql = "UPDATE sectionsettings SET `value`='$value' WHERE `name`='$key' AND `feedId`=".$this->id;
            if ($database->query($sql) === false) die(mysql_error() . "\nCouldn't edit settings" . $sql);
        }
    }
	public function getId() {
		return $this->id;
	}

	public function getName() {
		return $this->name;
	}

    public function getGroup() {
        return $this->group;
    }

	public function getContent(){
		return $this->content;
	}

    public function getDate($format = 'D, m Y H:i:s A') {
        return date($format, $this->date);
    }

    public function setName($name) {
        global $database;
        $sql = "UPDATE sections SET `name`='$name' WHERE `id`={$this->id}";
        if ($database->query($sql) === false) die(mysql_error() . "\nCould not set the name of the section");
        $this->name = $name;
    }

    public function setGroup($gid) {
        global $database;
        $sql = "UPDATE sections SET `group`=$gid WHERE `id`={$this->gid}";
        if ($database->query($sql) === false) die(mysql_error() . "\nCould not set the group for the section");
        $this->group = new Group($gid);
    }

    public function edit($sectionData) {
        global $database;
        $fieldsUpdate = array_intersect_key($sectionData, array(
            'name'      => null,
            'content'   => null,
            'group'     => null,
        ));
        $fieldsUpdate['date'] = time();

        // Update the db entry and the object where things have been updated
        $sqlUpdate = array();
        foreach ($fieldsUpdate as $key => $value) {
            $sqlUpdate[] = "`$key`='$value'";
        }
        $sqlUpdate = implode(', ', $sqlUpdate);

        // perform the query
		$sql = "UPDATE sections SET ".$sqlUpdate." WHERE `id`=".$this->id;
        if($database->query($sql) === false) die(mysql_error() . "\ncould not update section \n $sql");
        if (isset($fieldsUpdate['group']) && $fieldsUpdate['group'] != null){
            $this->group = new Group($fieldsUpdate['group']);
            unset($fieldsUpdate['group']);
        }
        foreach ($fieldsUpdate as $key => $value) {
            $this->$key = $value;
        }
    }
		
    public function destroy() {
        global $database;
        $sql = 'DELETE FROM sections WHERE `id`='.$this->id;
        if ($database->query($sql) === false) die(mysql_error() . "\nCould not destroy section");
    }	

   
    static function create($fields) {
        global $database;
        $name = strval($fields['name']);
        $group = strval($fields['group']);
        $sql = "INSERT INTO sections (`name`,`group`,`date`) VALUES ('$name','$group', ".(time()).")";
        if ($database->query($sql) === false) die(mysql_error() . "\nCouldn't create new section");
        return new Section(mysql_insert_id());
    }
}
