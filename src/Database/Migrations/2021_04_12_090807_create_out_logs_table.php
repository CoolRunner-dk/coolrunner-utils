<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(config('utils.model_connection'))->create('out_logs', function (Blueprint $table) {
            $table->id();
            $table->string('service',64)->nullable()->index();
            $table->string('type',64)->nullable()->index();
            $table->string('endpoint',64)->nullable()->index();
            $table->longText('request')->nullable();
            $table->longText('response')->nullable();
            $table->integer('http_code')->nullable()->index();
            $table->integer('time')->nullable();
            $table->string('phrase')->nullable();
            $table->integer('customer_id')->nullable()->index();
            $table->timestamp('requested_at')->nullable();
            $table->uuid('in_log_uuid')->nullable()->index();
            $table->timestamps();
            $table->index('created_at');
            $table->string('app_name')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(config('utils.model_connection'))->dropIfExists('out_logs');
    }
}
