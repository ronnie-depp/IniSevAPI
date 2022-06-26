<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // table `posts`
        // can be further normalized
        // by spliting (`post_id`, `user_id`, `website_id`) columns
        // into a new mapping table `posts_users_websites`.

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('post_title', 255)->default('New Post Title.')->comment('Post Title.');
            $table->string('post_summary', 500)->default('New Post Summary.')->comment('Post Summary.');
            $table->longText('post_detail')->default('New Post Detail.')->comment('Post Detail.');
            $table->foreignId('user_id');
            $table->foreignId('website_id');
            $table->softDeletes($column = 'deleted_at')->comment('Suspended Post.');
            $table->timestamps();
            // Post should be unique
            $table->unique(['post_title', 'post_summary', 'post_detail', 'website_id'], 'posts_are_unique');

            // Define Foreign Keys
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('website_id')->references('id')->on('websites')->onUpdate('cascade')->onDelete('cascade');
        });

        // Enable Foreign Key Constraints
        Schema::enableForeignKeyConstraints();

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
        Schema::table('posts', function (Blueprint $table) {

            // Check If Foreign Keys Exist
            $keyExists = DB::select(
                DB::raw(
                    'SHOW KEYS
                    FROM posts
                    WHERE Key_name=\'posts_user_id_foreign\''
                )
            );
            // Drop FK
            if($keyExists){
                $table->dropForeign('posts_user_id_foreign');
            }

            // Check If Foreign Keys Exist
            $keyExists = DB::select(
                DB::raw(
                    'SHOW KEYS
                    FROM posts
                    WHERE Key_name=\'posts_website_id_foreign\''
                )
            );
            // Drop FK
            if($keyExists){
                $table->dropForeign('posts_website_id_foreign');
            }

        });

        Schema::dropIfExists('posts');
    }
}
