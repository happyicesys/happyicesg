<?php

namespace App\Traits;

trait HasRunningNumber{

    // normal builder
    public function generateRunningNumber($model)
    {

        $code = $model
            ->orderByRaw('LENGTH(code) DESC, code DESC')
            ->first()
            ->code;

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