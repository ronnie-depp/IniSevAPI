<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateUsersWebsitesRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_websites_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('website_id');
            $table->foreignId('role_id')->default(3)->comment('default value 3 for Role: Subscriber is used.');
            $table->softDeletes($column = 'deleted_at')->comment('Suspended User-Website-Role mapped relation.');
            $table->timestamps();
            // Define Foreign Keys
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('website_id')->references('id')->on('websites')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onUpdate('cascade')->onDelete('cascade');
        });

        // Enable Foreign Key Constraints
        Schema::enableForeignKeyConstraints();

        // Insert Default Users, Websites & Roles

        // Admin (is Author & Subscriber for all available websites in websites table.)
        DB::table('users_websites_roles')->insert(
            array(
                'user_id' => 1,
                'website_id' => 1,
                'role_id' => 1,// Role: Admin
                'created_at' => NOW()
            )
        );
        DB::table('users_websites_roles')->insert(
            array(
                'user_id' => 2,
                'website_id' => 1,
                'role_id' => 3,// Role: Subscriber
                'created_at' => NOW()
            )
        );
        // Author
        DB::table('users_websites_roles')->insert(
            array(
                'user_id' => 2,
                'website_id' => 2,
                'role_id' => 2,// Role: Author
                'created_at' => NOW()
            )
        );
        DB::table('users_websites_roles')->insert(
            array(
                'user_id' => 2,
                'website_id' => 3,
                'role_id' => 3,
                'created_at' => NOW()
            )
        );
        DB::table('users_websites_roles')->insert(
            array(
                'user_id' => 2,
                'website_id' => 4,
                'role_id' => 3,
                'created_at' => NOW()
            )
        );
        // Author
        DB::table('users_websites_roles')->insert(
            array(
                'user_id' => 3,
                'website_id' => 1,
                'role_id' => 2,
                'created_at' => NOW()
            )
        );
        DB::table('users_websites_roles')->insert(
            array(
                'user_id' => 3,
                'website_id' => 2,
                'role_id' => 3,
                'created_at' => NOW()
            )
        );
        DB::table('users_websites_roles')->insert(
            array(
                'user_id' => 3,
                'website_id' => 3,
                'role_id' => 3,
                'created_at' => NOW()
            )
        );
        DB::table('users_websites_roles')->insert(
            array(
                'user_id' => 3,
                'website_id' => 4,
                'role_id' => 3,
                'created_at' => NOW()
            )
        );
        DB::table('users_websites_roles')->insert(
            array(
                'user_id' => 4,
                'website_id' => 1,
                'role_id' => 3,
                'created_at' => NOW()
            )
        );
        DB::table('users_websites_roles')->insert(
            array(
                'user_id' => 4,
                'website_id' => 2,
                'role_id' => 3,
                'created_at' => NOW()
            )
        );
        DB::table('users_websites_roles')->insert(
            array(
                'user_id' => 4,
                'website_id' => 3,
                'role_id' => 3,
                'created_at' => NOW()
            )
        );
        DB::table('users_websites_roles')->insert(
            array(
                'user_id' => 4,
                'website_id' => 4,
                'role_id' => 3,
                'created_at' => NOW()
            )
        );
        // Author
        DB::table('users_websites_roles')->insert(
            array(
                'user_id' => 5,
                'website_id' => 1,
                'role_id' => 2,
                'created_at' => NOW()
            )
        );
        // Author
        DB::table('users_websites_roles')->insert(
            array(
                'user_id' => 5,
                'website_id' => 2,
                'role_id' => 2,
                'created_at' => NOW()
            )
        );
        // Author
        DB::table('users_websites_roles')->insert(
            array(
                'user_id' => 5,
                'website_id' => 3,
                'role_id' => 2,
                'created_at' => NOW()
            )
        );
        // Author
        DB::table('users_websites_roles')->insert(
            array(
                'user_id' => 5,
                'website_id' => 4,
                'role_id' => 2,
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

        // Drop Foreign Keys
        Schema::table('users_websites_roles', function (Blueprint $table) {

            // Check If Foreign Keys Exist
            $keyExists = DB::select(
                DB::raw(
                    'SHOW KEYS
                    FROM users_websites_roles
                    WHERE Key_name=\'users_websites_roles_user_id_foreign\''
                )
            );
            // Drop FK
            if($keyExists){
                $table->dropForeign('users_websites_roles_user_id_foreign');
            }

            // Check If Foreign Keys Exist
            $keyExists = DB::select(
                DB::raw(
                    'SHOW KEYS
                    FROM users_websites_roles
                    WHERE Key_name=\'users_websites_roles_website_id_foreign\''
                )
            );
            // Drop FK
            if($keyExists){
                $table->dropForeign('users_websites_roles_website_id_foreign');
            }

            // Check If Foreign Keys Exist
            $keyExists = DB::select(
                DB::raw(
                    'SHOW KEYS
                    FROM users_websites_roles
                    WHERE Key_name=\'users_websites_roles_role_id_foreign\''
                )
            );
            // Drop FK
            if($keyExists){
                $table->dropForeign('users_websites_roles_role_id_foreign');
            }

        });

        // Drop Table
        Schema::dropIfExists('users_websites_roles');
    }
}
