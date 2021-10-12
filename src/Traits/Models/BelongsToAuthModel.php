<?php


namespace CoolRunner\Utils\Traits\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Class BelongsToAuthModel
 *
 * @package Coolrunner\Utils\Traits\Models
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait BelongsToAuthModel
{
    public static function bootBelongsToAuthModel() {
        static::creating(function (Model|BelongsToAuthModel $model) {
            if(!$model->user)
                $model->user()->associate(Auth::getUser());
        });
    }

    public function user() {
        return $this->morphTo('user');
    }
}
