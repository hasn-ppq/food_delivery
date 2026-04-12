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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('otp_attempts')->default(0)->after('otp_expires_at');
            $table->timestamp('otp_sent_at')->nullable()->after('otp_attempts');
            $table->timestamp('otp_sms_available_at')->nullable()->after('otp_sent_at');
            $table->string('otp_channel', 20)->nullable()->after('otp_sms_available_at');
            $table->timestamp('last_login_at')->nullable()->after('otp_channel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'otp_attempts',
                'otp_sent_at',
                'otp_sms_available_at',
                'otp_channel',
                'last_login_at',
            ]);
        });
    }
};
