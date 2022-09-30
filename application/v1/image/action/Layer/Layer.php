<?php

namespace Layer;

use PHPImageWorkshop\Core\ImageWorkshopLayer;
use PHPImageWorkshop\ImageWorkshop;

class Layer
{
    public $type = "";
    public string $text = "";
    public int $size = 13;
    public int $x = 0;
    public int $y = 0;
    public string $path = "";

    public function __construct()
    {
        switch ($this->type) {
            case "text":
                return $this->text();
        }
    }

    public function text(): ImageWorkshopLayer
    {
        return ImageWorkshop::initTextLayer("123", $this->font, $this->font_size, $this->font_color);
    }

    /**
     * @throws \PHPImageWorkshop\Exception\ImageWorkshopException
     */
    public function image(): ImageWorkshopLayer
    {
        return ImageWorkshop::initFromPath($this->path);
    }

}