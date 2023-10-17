<?php

abstract class BaseFileHandler
{
    protected $filename = ''; // push filename in here on file upload
    protected $fileMime = ''; // push the mime type in here on file upload
    protected $allowedExtensions = ['test'];
    protected $allowedMimeTypes = [];

    abstract public function __construct($path);

    abstract public function upload($identifier, $file);

    abstract public function getUrl();

    abstract public function destroy();

    public function checkFileType()
    {
        $this->ext = explode('.', $this->filename);
        $this->ext = end($this->ext);
        if (in_array($this->ext, $this->allowedExtensions, true)) {
            return true;
        } else {
            return false;
        }
    }

    public function checkMimeType()
    {
        if (in_array($this->fileMime, $this->allowedMimeTypes, true)) {
            return true;
        } else {
            return false;
        }
    }

    public function getCwd()
    {
        // Needs code to determine if I'm in the admin folder...
        $array = explode(DIRECTORY_SEPARATOR, getcwd());
        if (end($array) == 'admin') {
            chdir('..');
            $wd = getcwd();
            chdir('admin');
        } else {
            $wd = getcwd();
        }
        return $wd;
    }
}
