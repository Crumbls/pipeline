<?php

// Migration for the pipeline states table
namespace Crumbls\Pipeline\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePipelineStatesTable extends Migration
{
    public function up()
    {
        Schema::create('pipeline_states', function (Blueprint $table) {
            $table->string('pipeline_id')->primary();
            $table->json('state');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pipeline_states');
    }
}
