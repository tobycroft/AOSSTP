<?php

use PHPImageWorkshop\Core\ImageWorkshopLayer;

class DataAction
{
    public static function handle($item): ImageWorkshopLayer|null
    {
        switch ($item["type"]) {
            case "text":
                $layer = new Layer\Layer();
                if (!isset($item["text"])) {
                    $layer->text = $item["text"];
                }
                if (!isset($item["size"])) {
                    $layer->size = $item["size"];
                }
                if (!isset($item["x"])) {
                    $layer->x = $item["x"];
                }
                if (!isset($item["y"])) {
                    $layer->y = $item["y"];
                }
                return $layer->text();

            case "image":
                $layer = new Layer\Layer();
                if (!isset($item["path"])) {
                    $layer->path = $item["path"];
                }
                if (!isset($item["x"])) {
                    $layer->x = $item["x"];
                }
                if (!isset($item["y"])) {
                    $layer->y = $item["y"];
                }
                return $layer->image();
        }
        return null;
    }
}

