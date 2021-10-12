<?php

namespace CoolRunner\Utils\Traits\Models;

use Coolrunner\Support\Internal;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait HasSessionUuid
 *
 * @package CoolRunner\Utils\Traits\Models
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasSessionUuid
{
    public static function bootHasSessionUuid() {
        static::saving(function (Model $model) {
            try {
                $model->setAttribute('session_uuid', \CoolRunner\Utils\Support\Internal\SessionUuid::get());
            }catch (\Throwable $e) {
                dd($e);
            }
        });
    }
}
