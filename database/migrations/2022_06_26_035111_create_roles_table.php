<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('role')->comment('User Roles: 1 SuperAdmin, 2 Author, 3 Subscriber')->unique();
            $table->softDeletes($column = 'deleted_at')->comment('Suspended Role.');
            $table->timestamps();
        });

        // Insert Default Roles
        DB::table('roles')->insert(
            array(
                'role' => 'SuperAdmin',
                'created_at' => NOW()
            )
        );
        DB::table('roles')->insert(
            array(
                'role' => 'Author',
                'created_at' => NOW()
            )
        );
        DB::table('roles')->insert(
            array(
                'role' => 'Subscriber',
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

        Schema::dropIfExists('roles');
    }
}
