<?php

namespace CoolRunner\Utils\Traits\Models;

use CoolRunner\Utils\Support\Internal\SessionUuid;
use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait HasSessionUuid
 *
 * @package CoolRunner\Utils\Traits\Models
 * @property $session_uuid
 * @mixin \Illuminate\Database\Eloquent\Model
 * @mixin HasAttributes
 */
trait HasSessionUuid
{
    public static function bootHasSessionUuid()
    {
        static::saving(function (Model|self $model) {
            if (!$model->session_uuid) {
                $model->session_uuid = SessionUuid::get();
            }
        });
    }

    public function getSessionUuidAttribute()
    {
        if (!isset($this->attributes['session_uuid'])) {
            $this->attributes['session_uuid'] = SessionUuid::get();
        }

        return $this->attributes['session_uuid'] ?? null;
    }
}
