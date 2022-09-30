<?php

namespace Layer;

use PHPImageWorkshop\Core\ImageWorkshopLayer;
use PHPImageWorkshop\ImageWorkshop;

class Layer
{
    public string $text = "";
    public int $size = 13;
    public int $x = 0;
    public int $y = 0;
    public string $path = "";

    private string $font = "../public/static/misans/misans.ttf";
    private string $font_color = "000000";

    public function text(): ImageWorkshopLayer
    {
        return ImageWorkshop::initTextLayer($this->text, $this->font, $this->size, $this->font_color);
    }

    /**
     * @throws \PHPImageWorkshop\Exception\ImageWorkshopException
     */
    public function image(): ImageWorkshopLayer
    {
        return ImageWorkshop::initFromPath($this->path);
    }

}