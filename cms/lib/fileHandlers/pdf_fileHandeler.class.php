<?php

class pdf_fileHandeler extends base_fileHandeler
{
    public $allowedExtensions = array(
        'pdf', 'pdfs'
    );
    public $allowedMimeTypes = array(
        'application/pdf',
        'application/x-pdf',
        'application/x-bzpdf',
        'application/x-gzpdf'
    );
    protected $uploadPath = '';
    protected $urlAppend = 'files';
    protected $max_filesize = 5242880;

    public function __construct($filename = null)
    {
        if ($filename !== null) {
            $this->filename = $filename;
        }
        $this->uploadPath = $this->getCwd() . 'uploads/files';
    }

    public function upload($identifier, $file)
    {
        $results = array();
        $this->filename = $file['name'];
        $this->fileMime = $file['type'];
        if ($this->checkFileType($this->filename, $this->allowedExtensions)) {
            if ($this->checkMimeType()) {
                if (filesize($file['tmp_name']) < $this->max_filesize) {
                    if (is_uploaded_file($file['tmp_name'])) {
                        mkdir($this->uploadPath . "/" . $identifier . "/", 777, true);
                        if (is_writable($this->uploadPath . "/" . $identifier . "/")) {
                            echo 'fileUpload allowed';
                            move_uploaded_file($file['tmp_name'], $this->uploadPath . "/" . $identifier . "/" . $this->filename);
                            $results['success'] = true;
                            $results['path'] = $identifier . "/" . $this->filename;
                        } else {
                            $results['bigReason'] = 'file not writeable!! permissions error';
                            $results['success'] = false;
                            $results['reason'] = 'Permisisons';
                        }
                    } else {
                        $results['success'] = false;
                        $results['reason'] = 'NotAUpload';
                        $results['offender'] = $file['tmp_name'];
                        $results['bigReason'] = 'file not a uploaded file';
                    }
                } else {
                    $results['success'] = false;
                    $results['reason'] = 'FileSize';
                    $results['offender'] = filesize($file['tmp_name']);
                    $results['bigReason'] = 'file size Too big';
                }
            } else {
                $results['success'] = false;
                $results['reason'] = 'MimeType';
                $results['offender'] = $this->fileMime;
                $results['bigReason'] = 'file MimeType not allowed';
            }
        } else {
            $results['success'] = false;
            $results['reason'] = 'Extension';
            $results['offender'] = $this->ext;
            $results['bigReason'] = 'file Extension not allowed';
        }
        return $results;
    }

    public function view()
    {
        return "<a href='" . $this->getUrl() . "'>" . ltrim($this->filename, '/') . "</a>";
    }

    public function getUrl()
    {
        global $uploadUrl;
        return $uploadUrl . $this->urlAppend . $this->filename;
    }

    public function destroy()
    {
        unlink($this->uploadPath . $this->filename);
    }
}
