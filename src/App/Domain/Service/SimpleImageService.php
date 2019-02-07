<?php

namespace App\Domain\Service;

class SimpleImageService
{
    public $image;
    public $image_type;

    public function load($filename)
    {
        $image_info = getimagesize($filename);
        $this->image_type = $image_info[2];
        if ($this->image_type == IMAGETYPE_JPEG) {
            $this->image = imagecreatefromjpeg($filename);
        } elseif ($this->image_type == IMAGETYPE_GIF) {
            $this->image = imagecreatefromgif($filename);
        } elseif ($this->image_type == IMAGETYPE_PNG) {
            $this->image = imagecreatefrompng($filename);
            $this->setTransparency($this->image);
        } else {
            throw new \Exception('Image must be one of types: png, jpg (jpeg) or gif');
        }
    }

    public function save($filepath, $quality = 100, $image_type = null)
    {
        if (!$image_type) {
            $image_type = $this->image_type;
        }
        if ($image_type == IMAGETYPE_JPEG) {
            return imagejpeg($this->image, $filepath, $quality);
        } elseif ($image_type == IMAGETYPE_GIF) {
            return imagegif($this->image, $filepath);
        } elseif ($image_type == IMAGETYPE_PNG) {
            return imagepng($this->image, $filepath);
        } else {
            throw new \Exception('Image type must be one of types: IMAGETYPE_JPEG, IMAGETYPE_GIF, IMAGETYPE_PNG');
        }
    }

    public function output($image_type = IMAGETYPE_JPEG)
    {
        if ($image_type == IMAGETYPE_JPEG) {
            imagejpeg($this->image);
        } elseif ($image_type == IMAGETYPE_GIF) {
            imagegif($this->image);
        } elseif ($image_type == IMAGETYPE_PNG) {
            imagepng($this->image);
        } else {
            throw new \Exception('Image type must be one of types: IMAGETYPE_JPEG, IMAGETYPE_GIF, IMAGETYPE_PNG');
        }
    }

    public function getWidth()
    {
        return imagesx($this->image);
    }

    public function getHeight()
    {
        return imagesy($this->image);
    }

    public function resizeToHeight($height)
    {
        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize($width, $height);
    }

    public function resizeToWidth($width)
    {
        $ratio = $width / $this->getWidth();
        $height = $this->getHeight() * $ratio;
        $this->resize($width, $height);
    }

    public function scale($scale)
    {
        $width = $this->getWidth() * $scale / 100;
        $height = $this->getHeight() * $scale / 100;
        $this->resize($width, $height);
    }

    public function resize($width, $height)
    {
        $new_image = imagecreatetruecolor($width, $height);

        if ($this->image_type == IMAGETYPE_PNG) {
            $this->setTransparency($new_image, $this->image);
        }

        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->image = $new_image;
    }

    public function resizeTo($maxWidth = null, $maxHeight = null)
    {

        if (is_null($maxHeight)) {
            $maxHeight = $this->getHeight();
        }

        if (is_null($maxWidth)) {
            $maxWidth = $this->getWidth();
        }

        if ($this->getWidth() > $maxWidth) {
            $this->resizeToWidth($maxWidth);
        }

        if ($this->getHeight() > $maxHeight) {
            $this->resizeToHeight($maxHeight);
        }

        return $this;
    }

    function setTransparency(&$image)
    {
        imagealphablending($image, false);
        imagesavealpha($image, true);
    }
}