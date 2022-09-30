<?php

class DataAction extends LayerInput\LayerInput
{
    public function __construct($json)
    {
        $data = json_decode($json, 1);
        if (count($data) > 0) {
            foreach ($data as $item) {
                switch ($item["type"]) {
                    case "text":
                        $layer = new LayerInput\LayerInput();
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
                        $layer = new LayerInput\LayerInput();
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

