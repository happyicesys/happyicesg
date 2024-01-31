<?php

namespace App\Traits;

trait HasRunningNumber{

    // normal builder
    public function generateRunningNumber($model)
    {

        $code = $model->max('code');

        if(!$code) {
            return 10001;
        }
        else{
            return intval($code) + 1;
        }
    }

    public function getIncrementNumber($code)
    {
        if(!$code) {
            return 10001;
        }
        $code = $code + 1;
        return $code;
    }

}