<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sql_jobs', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('description', 1000)->nullable();
            $table->foreignId('connection_id')->index();
            $table->longText('script');
            $table->dateTime('execution_date')->index();
            $table->string('status')->index();
            $table->longText('error')->nullable();
            //$table->foreingId('notification_id');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('connection_id')->references('id')->on('connections');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sql_jobs');
    }
};
