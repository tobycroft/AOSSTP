<?php

namespace app\v1\image\action;

use PHPImageWorkshop\Core\ImageWorkshopLayer;
use think\Exception;

class DataAction extends Layer
{
    /**
     * @throws Exception
     */
    public function handle($item): ImageWorkshopLayer|null
    {
        if (!isset($item["type"])) {
            throw new Exception("type");
        }
        switch ($item["type"]) {
            case "text":
                $layer = new Layer\Layer();
                if (!isset($item["text"])) {
                    throw new Exception("text");
                }
                if (!isset($item["size"])) {
                    throw new Exception("size");
                }
                if (!isset($item["x"])) {
                    throw new Exception("x");
                }
                if (!isset($item["y"])) {
                    throw new Exception("y");
                }
                if (!isset($item["position"])) {
                    throw new Exception("position");
                }
                $layer->text = $item["text"];
                $layer->size = $item["size"];
                $layer->x = $item["x"];
                $layer->y = $item["y"];
                $layer->position = $item["position"];
                return $layer->text();

            case "image":
                $layer = new Layer\Layer();
                if (!isset($item["path"])) {
                    throw new Exception("path");
                }
                if (!isset($item["x"])) {
                    throw new Exception("x");
                }
                if (!isset($item["y"])) {
                    throw new Exception("y");
                }
                if (!isset($item["position"])) {
                    throw new Exception("position");
                }
                $layer->path = $item["path"];
                $layer->x = $item["x"];
                $layer->y = $item["y"];
                $layer->position = $item["position"];
                return $layer->image();
        }
        return null;
    }
}

