<?php

namespace Drupal\co_150_core\Service;

use \Drupal\Core\Controller\ControllerBase;

class OptimizeImage extends ControllerBase
{
    private $image;
    private $width;
    private $height;
    private $quality;

    /**
     * @param mixed $image      Image from $_FILES of formData
     * @param int $width        Width final image, in pixels
     * @param int $height       Height final image, in pixels
     * @param int $quality      Qulity final image, in percent 0 - 100
     */
    public function __construct($image,$width = 600,$height = 800,$quality = 75){
        $this->image = $image;
        $this->width = $width;
        $this->height = $height;
        $this->quality = $quality;
    }

    public function optimize(){
        $filepath = $this->image['tmp_name'];

        switch ($this->image['type']) {
            case 'image/png':
                $imageMod = imagecreatefrompng($filepath);
                break;
            case 'image/jpeg':
                $imageMod = imagecreatefromjpeg($filepath);
                break;
            case 'image/bmp':
                $imageMod = imagecreatefrombmp($filepath);
                break;
            case 'image/gif':
                $imageMod = imagecreatefromgif($filepath);
                break;                
            default:
                die($this->image['type'] . "Is an invalid image type");
                break;
        }
        
        $imgResized = $this->resize($imageMod);
        $tmpfname = $this->crop($imgResized);
        
        $link_to_image = file_create_url($this->image['name']);
        $nameImage = pathinfo($link_to_image, PATHINFO_FILENAME);

        $imageOptimized = [
            'tmp_name' => $tmpfname,
            'name' => $nameImage . '.jpg',
            'type' => 'image/jpeg',
        ];

        return $imageOptimized;
    }

    private function resize($imageMod){
        // find the size of image

        $xSize = imagesx($imageMod);
        $ySize = imagesy($imageMod);

        //resize
        $xResize = $this->width / $xSize;
        $yResize = $this->height / $ySize;
        
        $resize = $xResize > $yResize ? $xResize : $yResize;

        $xSizeNew = $xSize * $resize;
        $ySizeNew = $ySize * $resize;


        $imgResized = imagecreatetruecolor($xSizeNew, $ySizeNew);
        imagefill($imgResized, 0, 0, imagecolorallocate($imgResized, 255, 255, 255));  // white background;
        imagecopyresized($imgResized, $imageMod, 0, 0, 0, 0, $xSizeNew, $ySizeNew, $xSize, $ySize);
        imagedestroy($imageMod);

        return $imgResized;
    }

    private function crop($imgResized){

        // find the size of image
        $xSize = imagesx($imgResized);
        $ySize = imagesy($imgResized);

        //crop image
        $x = ($xSize / 2) - ($this->width / 2);
        $y = ($ySize / 2) - ($this->height / 2); 

        $imgCrop = imagecrop($imgResized, ['x' => $x, 'y' => $y, 'width' => $this->width, 'height' => $this->height]);
        imagedestroy($imgResized);
        
        $drupal_path = \Drupal::service('file_system')->getTempDirectory();
        $tmpfname = $drupal_path .'/'.  time() . rand(10,99) .'.jpg';
        
        imagejpeg($imgCrop, $tmpfname,$this->quality);
        imagedestroy($imgCrop);

        return $tmpfname;
    }
}