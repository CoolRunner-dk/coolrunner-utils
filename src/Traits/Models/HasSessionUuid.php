<?php

namespace CoolRunner\Utils\Traits\Models;

use CoolRunner\Utils\Support\Internal\SessionUuid;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait HasSessionUuid
 *
 * @package CoolRunner\Utils\Traits\Models
 * @property-read $session_uuid
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasSessionUuid
{
    public static function bootHasSessionUuid() {
        static::saving(function (Model $model) {
            $model->setAttribute('session_uuid', $model);
        });
    }

    public function getSessionUuidAttribute($session_uuid) {
        return $session_uuid ?: SessionUuid::get();
    }
}
