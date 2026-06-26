<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->string('oauth_client_id')->nullable()->after('api_key');
            $table->string('oauth_client_secret')->nullable()->after('oauth_client_id');
        });
    }

    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn(['oauth_client_id', 'oauth_client_secret']);
        });
    }
};
