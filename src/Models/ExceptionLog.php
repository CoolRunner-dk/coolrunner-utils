<?php


namespace CoolRunner\Utils\Models;

use Illuminate\Database\Eloquent\Model;

class ExceptionLog extends Model
{
    protected $table = 'exception_logs';



    public function getConnectionName()
    {
        return config('utils.model_connection');
    }

}