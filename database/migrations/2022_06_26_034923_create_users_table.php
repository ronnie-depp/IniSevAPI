<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id()->comment('User ID.');
            $table->string('email')->comment('Serves both as User Name & Email.')->unique();
            $table->string('pwd')->nullable()->comment('User\'s Password means User is SuperAdmin or Author. Subscriber doesn\'t need a password to be set.')->default(NULL);
            $table->string('subscription_status')->default('Unverified')->comment('Default Value: Unverified, Before Verification (12 Char. Verification-Code): #############, After Verification: Active.');
            $table->timestamp('subscribed_on')->nullable();//->useCurrent();
            $table->softDeletes($column = 'deleted_at')->comment('Suspended User.');
            $table->timestamps();
        });

                // Insert Default SuperAdmin/Author
                DB::table('users')->insert(
                    array(
                        'email' => 'salman.test.inisevapi@gmail.com',
                        'pwd' => md5('P@ssword'),
                        'subscription_status' => 'Active',
                        'subscribed_on' => NOW(),
                        'created_at' => NOW()
                    )
                );

                // Insert Few Subscribers
                DB::table('users')->insert(
                    array(
                        'email' => 'salman@inisev.com',
                        'pwd' => md5('P@ssword'),
                        'subscription_status' => 'Active',
                        'subscribed_on' => NOW(),
                        'created_at' => NOW()
                    )
                );
                DB::table('users')->insert(
                    array(
                        'email' => 'salman@sellcodes.com',
                        'pwd' => md5('P@ssword'),
                        'subscription_status' => 'Active',
                        'subscribed_on' => NOW(),
                        'created_at' => NOW()
                    )
                );
                DB::table('users')->insert(
                    array(
                        'email' => 'join@inisev.com',
                        'subscription_status' => 'Active',
                        'subscribed_on' => NOW(),
                        'created_at' => NOW()
                    )
                );
                DB::table('users')->insert(
                    array(
                        'email' => 'valentin@sellcodes.com',
                        'pwd' => md5('P@ssword'),
                        'subscription_status' => 'Active',
                        'subscribed_on' => NOW(),
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

        Schema::dropIfExists('users');
    }
}
