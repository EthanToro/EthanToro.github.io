<?php

class View {
    private 
        $pageName,
        $a_vars = array();

    public function __construct($pageName, $id=false) {
        $this->pageName = $pageName;
        if ($if !== false)
            $this->a_vars['id'] = $id;
    }

    public function setValue($name, $value) {
        $this->a_vars[$name] = $value;
    }

    public function display() {
        echo $this->getDisplay();
    }

    public function getDisplay() {
        foreach ($this->a_vars as $name => $value)
            $$name = $value;

        ob_start();
            require_once(Settings::getDocRoot() . "lib/templates/{$this->pageName}.php");
        return = ob_get_clean();
    }
}

?>
