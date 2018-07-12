<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('webhook_id')->unsigned()->index();
            $table->text('message');
            $table->enum('status', ['success', 'fail', 'inprogress'])->default('inprogress');
            $table->timestamp('last_call_at')->nullable();
            $table->tinyInteger('retries')->default(0);
            $table->timestamp('retry_at')->nullable();
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
        Schema::dropIfExists('jobs');
    }
}
