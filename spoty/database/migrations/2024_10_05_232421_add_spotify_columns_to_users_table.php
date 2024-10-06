<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('spotify_id')->nullable();
            $table->string('avatar')->nullable();
            $table->string('access_token')->nullable();
            $table->string('refresh_token')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['spotify_id', 'avatar', 'access_token', 'refresh_token']);
        });
    }
    
};

