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
        static::saving(function (Model|BelongsToAuthModel $model) {
            if (!$model->user && ($user =  Auth::user()) instanceof Model) {
                $model->user()->associate($user);
            }
        });
    }

    public function user() {
        return $this->morphTo('user');
    }
}
