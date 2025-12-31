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
        Schema::table('restaurants', function (Blueprint $table) {

                $table->dropForeign(['owner_id']); // حذف FK أولاً
            $table->unsignedBigInteger('owner_id')->nullable()->unique()->change(); // تعديل العمود
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropForeign(['owner_id']); // حذف FK
            $table->unsignedBigInteger('owner_id')->index()->change(); // إعادة العمود مثل قبل
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade'); 
        });
    }
};
