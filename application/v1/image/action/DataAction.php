<?php

use PHPImageWorkshop\Core\ImageWorkshopLayer;

class DataAction
{
    public static function handle($item): ImageWorkshopLayer|null
    {
        switch ($item["type"]) {
            case "text":
                $layer = new Layer\Layer();
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
                return $layer->text();

            case "image":
                $layer = new Layer\Layer();
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
                return $layer->image();
        }
        return null;
    }
}

