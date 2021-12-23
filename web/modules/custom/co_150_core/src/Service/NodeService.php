<?php

namespace Drupal\co_150_core\Service;

class NodeService {

    public function getImage($node){
        $images = [];
        foreach ($node as $key => $value) {
            $path_url = $value->entity->uri->value;
            $data = [
                'url' => file_create_url($path_url),
            ];
            $data = array_merge($data, $value->getValue());
            array_push($images, $data);
        }
        return $images;
    }
}