<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFlowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flows', function (Blueprint $table) {
            $table->increments('id');
            $table->string('flow');
            $table->string('record');
            $table->unsignedInteger('record_id');
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->string('interval')->nullable();
            $table->unsignedInteger('times')->nullable();
            $table->unsignedInteger('remaining_times')->nullable();
            $table->timestamp('available_at')->nullable();
            $table->timestamps();

            $table->index('flow');
            $table->index(['record', 'record_id']);
            $table->index('available_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flows');
    }
}
