<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code');
            $table->string('postal_code')->nullable();
            $table->string('position')->nullable();;
            $table->string('region')->nullable();;
            $table->string('regione_code')->nullable();;
            $table->string('province')->nullable();;
            $table->string('sigle_province')->nullable();;
            $table->string('latitude')->nullable();;
            $table->string('longitude')->nullable();;

            #$table->foreign(['id_permesso'])->references('id')->on('permesso')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign("country_code")->references("country_code")->on("countries");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
