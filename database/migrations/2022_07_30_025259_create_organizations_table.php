<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('type')->default('individual');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignUuid('organization_id')->after('id');
            $table->foreign('organization_id')->references('uuid')->on('organizations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organizations');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn('organization_id');
        });
    }
}
