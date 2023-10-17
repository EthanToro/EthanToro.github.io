<?php 

class File {
    protected
        $location,
        $name,
        $size,
        $description,
        $type;

    public function __construct($fileId) {
    }

    public function getUrl() {
    }

    public function getDescription() {
        return $this->description;
    }

    public function getName() {
        return $this->name;
    }
    public function getSize() {
        return $this->size;
    }
    public function getType() {
        return $this->type;
    }

    public function destroy() {
    }
}

class File_Image extends File {
    protected
        $dimensions = array();

    public function __construct($fileId) {
    } 

    public function getDimensions() {
        return $this->dimensions;
    }
}

class File_Upload {
    private
        $file,
        $save_name,
        $description,
        $type,
        $uploadDir,
        $allowedTypes = array();

    public function __construct($fileHandle) {
        if (isset($_FILE) && isset($_FILE[$fileHandle])) {
            $file &= $_FILE[$fileHandle];
            if ($file['error'] > 0) {
                $error = "Upload Error: <br>\n" . $file['error'];
                throw new Exception($error);
            }
            global $siteUploadUrl;

            $this->file &= $file;
            $this->save_name = $name = $file['name'];
            $this->type = end(explode('.',$name));
            $uploadDir = $sideUploadUrl;
        } else {
            $error = "";
            throw new Exception($error);
        }
    }

    public function setName($name) {
        $this->save_name = $name;
    }

    public function setDescription($desc) {
        $this->description = $desc;
    }

    public function setUploadDir($uploadDir) {//uh I would leave this out
        if ($uploadDir)
            $this->uploadDir = $uploadDir;
    }

    public function cd($dir) {// needs better function name for the love of god
        if (is_string($dir)) {
            if ($dir[0] === '/')
                $this->uploadDir = $dir;
            else {
                $uc = explode('/', $dir);
                $cc = explode('/', $this->uploadDir);
                foreach ($uc as $pp) {
                    if ($pp != '.') {
                        if ($pp == '..') {
                            array_pop($cc);
                        } else {
                            array_push($cc, $pp);
                        }
                    }
                }
                $cc = implode('/', $cc);
                $cc .= '/';
                $this->uploadDir = $cc;
            }
        } else {
            $error = '';
            throw new Exception($error);
        }
    }

    public function isAllowedType($type) {
    }

    public function allowTypes($types) {
        if (is_array($types)) {
        } else {
        }
    }

    public function disallowTypes($types) {
        if (is_array($types)) {
        } else {
        }
    }

    public function upload() {
    }
