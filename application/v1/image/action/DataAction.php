<?php

namespace app\v1\image\action;

use PHPImageWorkshop\Core\ImageWorkshopLayer;
use think\Exception;

class DataAction extends Layer
{
    /**
     * @throws Exception
     */
    private Layer $layer;
    private $item;

    /**
     * @throws Exception
     */
    public function __construct($item)
    {
        $this->item = $item;
        if (!isset($this->item["type"])) {
            throw new Exception("type");
        }
        $this->layer = new Layer();
        if (isset($this->item["x"])) {
            $this->layer->x = $this->item["x"];
        }
        if (isset($this->item["y"])) {
            $this->layer->y = $this->item["y"];
        }
        if (isset($this->item["position"])) {
            $this->layer->position = $this->item["position"];
        }
    }

    public function handle(): ImageWorkshopLayer|null
    {
        switch ($this->item["type"]) {
            case "text":
                $this->layer = new Layer();
                if (!isset($this->item["text"])) {
                    throw new Exception("text");
                }
                if (isset($this->item["size"])) {
                    $this->layer->size = $this->item["size"];
                }

                $this->layer->text = $this->item["text"];
                return $this->layer->text();

            case "image":
                $this->layer = new Layer();
                if (!isset($this->item["url"])) {
                    throw new Exception("url");
                }
                if (isset($this->item["x"])) {
                    $this->layer->x = $this->item["x"];
                }
                if (isset($this->item["y"])) {
                    $this->layer->y = $this->item["y"];
                }
                if (isset($this->item["position"])) {
                    $this->layer->position = $this->item["position"];
                }
                $this->layer->url = $this->item["url"];
                return $this->layer->image();
        }
        return null;
    }
}

