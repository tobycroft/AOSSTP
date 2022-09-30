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
                $layer = new Layer();
                if (!isset($item["text"])) {
                    throw new Exception("text");
                }
                if (isset($item["size"])) {
                    $layer->size = $item["size"];
                }
                if (isset($item["x"])) {
                    $layer->x = $item["x"];
                }
                if (isset($item["y"])) {
                    $layer->y = $item["y"];
                }
                if (isset($item["position"])) {
                    $layer->position = $item["position"];
                }
                $layer->text = $item["text"];
                return $layer->text();

            case "image":
                $layer = new Layer();
                if (!isset($item["path"])) {
                    throw new Exception("path");
                }
                if (isset($item["x"])) {
                    $layer->x = $item["x"];
                }
                if (isset($item["y"])) {
                    $layer->y = $item["y"];
                }
                if (isset($item["position"])) {
                    $layer->position = $item["position"];
                }
                $layer->url = $item["path"];
                return $layer->image();
        }
        return null;
    }
}

