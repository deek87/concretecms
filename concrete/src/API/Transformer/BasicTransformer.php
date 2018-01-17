<?php

namespace Concrete\Core\API\Transformer;


use League\Fractal\TransformerAbstract;

class BasicTransformer extends TransformerAbstract
{

    public function transform($data) {
        if (is_array($data) || is_object($data)) {
            return $data;
        } else {

            return ['data'=>$data];
        }

    }
}