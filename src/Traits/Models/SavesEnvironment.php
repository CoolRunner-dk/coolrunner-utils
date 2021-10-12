<?php


namespace CoolRunner\Utils\Traits\Models;


use Illuminate\Database\Eloquent\Model;

trait SavesEnvironment
{
    public static function bootSavesEnvironment() {
        static::creating(function (Model $model) {
            $model->environment = env('APP_NAME');
        });
    }
}
