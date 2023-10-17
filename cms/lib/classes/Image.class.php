<?php
require_once('image_resizer.php');
class Image{
    public $url = '';
    public $filename = '';
    public $filepath = '';

    function __construct($filename,$filepath=''){
        global $siteImageUrl;
        $this->filename = $filename;
        $this->url = $siteImageUrl.$filename;
        $this->filepath = $filepath;
    }

    function getUrlAbsolute() {
        echo "WARNING:: getUrlAbsolute() is depercated: please use getUrl()";
        return $this->url;
    }

    function getUrl() {
        return $this->url;
    }

    function getFilename() {
        return $this->filename;
    }

    function destroy() {
        global $uploadPath;
        if(file_exists($uploadPath.$this->filename))
            unlink($uploadPath.$this->filename);
        foreach (glob($uploadPath."\cache\*.*") as $filename){

            echo "$filename size " . filesize($filename) . "\n";
        }
    }

    function resize($w){
        global $uploadPath;
        $filenamenew = getcwd()."/images/cms/cache/$w-".$this->filename;
        if(!file_exists($filenamenew)){
            $this->resise(getcwd().'/images/cms/'.$this->filename,$w,$filenamenew);
        }else{
            return new Image("cache/$w-".$this->filename);
        }


        return $this;
    }
    private function resise($img, $thumb_width, $newfilename) 
    { 
        $max_width=$thumb_width;

        //Check if GD extension is loaded
        if (!extension_loaded('gd') && !extension_loaded('gd2')) 
        {
            trigger_error("GD is not loaded", E_USER_WARNING);
            return false;
        }

        //Get Image size info
        list($width_orig, $height_orig, $image_type) = getimagesize($img);

        switch ($image_type) 
        {
        case 1: $im = imagecreatefromgif($img); break;
        case 2: $im = imagecreatefromjpeg($img);  break;
        case 3: $im = imagecreatefrompng($img); break;
        default:  trigger_error('Unsupported filetype!', E_USER_WARNING);  break;
        }

        /*** calculate the aspect ratio ***/
        $aspect_ratio = (float) $height_orig / $width_orig;

        /*** calulate the thumbnail width based on the height ***/
        $thumb_height = round($thumb_width * $aspect_ratio);


        while($thumb_height>$max_width)
        {
            $thumb_width-=10;
            $thumb_height = round($thumb_width * $aspect_ratio);
        }

        $newImg = imagecreatetruecolor($thumb_width, $thumb_height);

        /* Check if this image is PNG or GIF, then set if Transparent*/  
        if(($image_type == 1) OR ($image_type==3))
        {
            imagealphablending($newImg, false);
            imagesavealpha($newImg,true);
            $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
            imagefilledrectangle($newImg, 0, 0, $thumb_width, $thumb_height, $transparent);
        }
        imagecopyresampled($newImg, $im, 0, 0, 0, 0, $thumb_width, $thumb_height, $width_orig, $height_orig);

        //Generate the file, and rename it to $newfilename
        switch ($image_type) 
        {
        case 1: imagegif($newImg,$newfilename); break;
        case 2: imagejpeg($newImg,$newfilename);  break;
        case 3: imagepng($newImg,$newfilename); break;
        default:  trigger_error('Failed resize image!', E_USER_WARNING);  break;
        }

        return $newfilename;
    }
}
?>
