<?php
class Permissions {
	private static $level;
	
	function __construct($p){
		$this->level = $p;
    }

    public function getAccessLevel() {
        return $this->level;
    }
}
?>
