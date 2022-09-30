<?php

use PHPImageWorkshop\Core\ImageWorkshopLayer;
use PHPImageWorkshop\ImageWorkshop;

class DataAction extends LayerInput
{
    public function __construct($json)
    {
        $data = json_decode($json, 1);
        if (count($data) > 0) {
            foreach ($data as $item) {
                switch ($item["type"]) {
                    case "text":
                        $layer = new LayerInput();
                        if (!isset($item["type"])) {
                            $layer->type = $item["type"];
                        }
                        if (!isset($item["text"])) {
                            $layer->type = $item["text"];
                        }
                        if (!isset($item["size"])) {
                            $layer->type = $item["size"];
                        }
                        if (!isset($item["x"])) {
                            $layer->type = $item["x"];
                        }
                        if (!isset($item["y"])) {
                            $layer->type = $item["y"];
                        }
                        return $layer;

                    case "img":
                        $layer = new LayerInput();
                        if (!isset($item["type"])) {
                            $layer->type = $item["type"];
                        }
                        if (!isset($item["path"])) {
                            $layer->path = $item["path"];
                        }
                        if (!isset($item["x"])) {
                            $layer->type = $item["x"];
                        }
                        if (!isset($item["y"])) {
                            $layer->type = $item["y"];
                        }
                        return $layer;
                }
            }
        }
    }
}

class LayerInput
{
    public $type = "";
    public string $text = "";
    public int $size = 13;
    public int $x = 0;
    public int $y = 0;
    public string $path = "";
}

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