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
        Schema::table('job_listings', function (Blueprint $table) {
            $table->string('location')->nullable()->after('description');
            $table->string('salary')->nullable()->after('location');
            $table->string('company')->nullable()->after('salary');
            $table->string('company_logo')->nullable()->after('company');
            $table->string('tags')->nullable()->after('company_logo');
            $table->string('job_type')->nullable()->after('tags');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->dropColumn(['location', 'salary', 'company', 'company_logo', 'tags', 'job_type']);
        });
    }
};
