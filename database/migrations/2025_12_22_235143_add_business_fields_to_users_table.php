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
            $table->string('business_name')->nullable()->after('name');
            $table->text('business_information')->nullable()->after('business_name');
            $table->string('phone')->nullable()->after('business_information');
            $table->string('owner_principal')->nullable()->after('phone');
            $table->string('address_line1')->nullable()->after('owner_principal');
            $table->string('address_line2')->nullable()->after('address_line1');
            $table->string('city')->nullable()->after('address_line2');
            $table->string('state')->nullable()->after('city');
            $table->string('postal_code')->nullable()->after('state');
            $table->string('country')->nullable()->after('postal_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'business_name',
                'business_information',
                'phone',
                'owner_principal',
                'address_line1',
                'address_line2',
                'city',
                'state',
                'postal_code',
                'country',
            ]);
        });
    }
};
