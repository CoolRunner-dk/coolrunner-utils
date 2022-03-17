<?php


namespace CoolRunner\Utils\Traits\Models;


use Illuminate\Database\Eloquent\Model;

trait SavesEnvironment
{
    public static function bootSavesEnvironment() {
        static::saving(function (Model $model) {
            if (!$model->environment) {
                $model->environment = env('APP_NAME');
            }
        });
    }
}
