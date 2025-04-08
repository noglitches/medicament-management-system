<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // admin, superuser, user
            $table->timestamps();
        });

        // Insert default roles
        \DB::table('roles')->insert([
            ['name' => 'user'],
            ['name' => 'admin'],
            ['name' => 'superuser'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
