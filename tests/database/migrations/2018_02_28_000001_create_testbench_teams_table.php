<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateTestbenchTeamsTable extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('name');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('teams');
    }
}
