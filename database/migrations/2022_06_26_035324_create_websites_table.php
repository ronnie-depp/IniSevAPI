<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebsitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('websites', function (Blueprint $table) {
            $table->id();
            $table->string('site_name');
            $table->string('site_url')->comment('`site_url` is the main Attribute of Table websites. All User-Website-Role interactions are based on `site_url`.')->unique();
            $table->softDeletes($column = 'deleted_at')->comment('Suspended Website.');
            $table->timestamps();
        });

        // Insert Few Websites
        DB::table('websites')->insert(
            array(
                'site_name' => 'Sell Codes',
                'site_url' => 'sellcodes.com',
                'created_at' => NOW()
            )
        );
        DB::table('websites')->insert(
            array(
                'site_name' => 'IniSev',
                'site_url' => 'inisev.com',
                'created_at' => NOW()
            )
        );
        DB::table('websites')->insert(
            array(
                'site_name' => 'Medium',
                'site_url' => 'medium.com',
                'created_at' => NOW()
            )
        );
        DB::table('websites')->insert(
            array(
                'site_name' => 'The Independent United Kingdom: News',
                'site_url' => 'independent.co.uk',
                'created_at' => NOW()
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Disable Foreign Key Constraints
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('websites');
    }
}
