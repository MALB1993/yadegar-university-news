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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('fullname');
            // client can change the phone number, when before requesting for employee so we can add it as a nullable field for now
            $table->string('phone_number')->unique()->nullable();
            $table->string('email')->unique();
            
            $table->enum('role', ['professor', 'employee' , 'student'])->default('student');
            
            // mabye client can request for this feature, so we can add it as a nullable field for now
            $table->string("identification_code")->unique()->nullable();
            
            // client can change the profile picture , when before requesting for this feature, so we can add it as a nullable field for now
            $table->string("profile_picture")->nullable();
            
            // show status of the user, if the user is active or inactive, default is active
            $table->enum("status",['active','inactive'])->default('active');

            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
