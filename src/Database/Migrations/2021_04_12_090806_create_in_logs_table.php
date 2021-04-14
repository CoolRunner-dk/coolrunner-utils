<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(config('utils.model_connection'))->create('in_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable()->index();
            $table->string('prefix')->nullable()->index();
            $table->string('method')->nullable()->index();
            $table->longText('headers')->nullable()->index();
            $table->longText('request')->nullable();
            $table->longText('response')->nullable()->index();
            $table->smallInteger('time')->nullable();
            $table->string('route')->nullable();
            $table->string('route_name')->nullable()->index();
            $table->mediumInteger('response_code')->nullable();
            $table->string('response_content_type')->nullable();
            $table->string('request_content_type')->nullable();
            $table->bigInteger('auth_id')->unsigned()->nullable()->index();
            $table->string('app_name')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(config('utils.model_connection'))->dropIfExists('in_logs');
    }
}
