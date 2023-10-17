<?php

class dImage {
    protected $image;

    public function __construct($image, $w) {
        $this->image = $image;
    }

    public function getUrl() {
        return $this->image->getUrl();
    }

    public function view() {
		return "<a href='".$this->image->getUrl()."'><img width='".$this->width."' src='". $this->image->getUrl()."'/></a>";
    }
    public function destroy() {
        $this->image->destroy();
    }
}

?>
