<?php
/* 
 * Class for the managing of groups for different cms components
 * Group
 *
 * Functions:
 *  getId()                 - returns the group's id
 *  getSort()               - returns the group's sort order
 *  getName()               - returns the group's name
 *  setSort($n_sortOrder)   - sets the sort order of the group, returns null
 *  setName($s_name)        - sets the name of the group, returns null
 *  destroy()               - removes all infromation from the db for this group.
 *
 */

class Group{
    private $id, $sort, $name, $canDelete;

    public function __construct($id=false) {
        if ($id) {
			if($id!=-1){
				global $database;

				$sql = "SELECT * FROM sectiongroups where `id`=$id limit 1";
				if (($result = $database->getRows($sql)) !== false) {
                    if(count($result)>0){
    					$result = $result[0];
    					$this->id = intval($id);
    					$this->name = $result['name'];
    					$this->sort = $result['sort'];
    					$this->canDelete = $result['removable'];
                    }
				} else
					die (mysql_error() . "\nCoult not construct group.");
			}else{
					$this->id = -1;
					$this->name = 'unassigned';
					$this->sort = -1;
			}
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getSort() {
        return $this->sort;
    }
	public function canDestroy(){
		return ((bool) $this->canDelete);
	}

    public function setSort($value) {
        if (is_numeric($value)) {
            global $database;
            $value = intval($value);
            $sql = "UPDATE sectiongroups set `sort`=$value WHERE `id`={$this->id}";
            if ($database->query($sql) === false) die(mysql_error() . "\nCouldn't set sort value of group.");
            $this->sort = $value;
        } else die('sort value must be numeric');
    }

    public function setName($name) {
        global $database;
        $sql = "UPDATE sectiongroups SET `sort`=$value WHERE `id={$this->id}";
        if ($database->query($sql) === false) die(mysql_error() . "\nCould not set name for group.");
        $this->name = $name;
    }

    public function destroy() {
        global $database;
        $sql = 'UPDATE `sections` set `group`=1 WHERE `group`='.$this->id;
        if ($database->query($sql) === false) die(mysql_error() . "\nCould not set groups.");
		$sql = 'DELETE FROM sectiongroups WHERE `id`='.$this->id;
        if ($database->query($sql) === false) die(mysql_error() . "\nCould not destroy group.");
        Group::defragGroupSort();
    }

	public function sortUp() {
		$this->setSort($this->sort+1);
	}
    
    public function sortDown() {
		$this->setSort($this->sort-1);
    }
    
    public function getSections(){
        global $database;
        $sql = "select * from `sections` where `group`='{$this->id}'";
        $sectionsr = $database->getRows($sql);
        $sections = array();
        foreach ($sectionsr as $section) 
            $sections[] = new Section($section['id']);
        return $sections;
    }

    public static function getMaxSort() {
        global $database;

        $sql = 'SELECT MAX(sort) as maxSort FROM sectiongroups';
        if (($result = $database->getRows($sql)) === false) die(mysql_error() . "\nCould not get maximum sort value for group.");
        if (count($result) > 0)
            return intval($result[0]['maxSort']);
        return 0;
    }

    public static function defragGroupSort(){
        global $cms;
        $groups = $cms->getSectionGroups();
        for($i=0;$i<count($groups);$i++){
            if($groups[$i]->canDestroy())
                $groups[$i]->setSort($i+1);
        }
    }
    static function create($name){
        global $database;
        $name = strval($name);
        Group::defragGroupSort();
        $max = Group::getMaxSort() + 1;
        $sql = "INSERT INTO `sectionGroups` (name, sort) VALUES ('$name', '$max')";
        if ($database->query($sql) === false) die(mysql_error() . "\nCouldn't create new section");
        return new Group(mysql_insert_id());
    }
}
