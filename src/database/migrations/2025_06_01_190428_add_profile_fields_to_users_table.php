<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileFieldsToUsersTable extends Migration
{

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->after('id');
            $table->string('profile_image')->nullable()->after('email');
            $table->string('zip')->nullable();
            $table->string('address')->nullable();
            $table->string('building')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'profile_image', 'zip', 'address', 'building']);
        });
    }
}
