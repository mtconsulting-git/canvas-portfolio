<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCanvasTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('canvas_posts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug');
            $table->json('title');
            $table->json('summary')->nullable();
            $table->json('body')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('featured_image_caption')->nullable();
            $table->json('gallery_images')->nullable();
            $table->uuid('user_id')->index();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['slug', 'user_id']);
        });

        Schema::create('canvas_tags', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug');
            $table->json('name');
            $table->uuid('user_id')->index();
            $table->timestamps();
            $table->softDeletes();
            $table->index('created_at');
            $table->unique(['slug', 'user_id']);
        });

        Schema::create('canvas_topics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug');
            $table->json('name');
            $table->uuid('user_id')->index();
            $table->timestamps();
            $table->softDeletes();
            $table->index('created_at');
            $table->unique(['slug', 'user_id']);
        });

        Schema::create('canvas_posts_tags', function (Blueprint $table) {
            $table->uuid('post_id');
            $table->uuid('tag_id');
            $table->unique(['post_id', 'tag_id']);
        });

        Schema::create('canvas_posts_topics', function (Blueprint $table) {
            $table->uuid('post_id');
            $table->uuid('topic_id');
            $table->unique(['post_id', 'topic_id']);
        });

        Schema::create('canvas_views', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('post_id')->index();
            $table->string('ip')->nullable();
            $table->text('agent')->nullable();
            $table->string('referer')->nullable();
            $table->timestamps();
            $table->index('created_at');
        });

        Schema::create('canvas_visits', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('post_id');
            $table->string('ip')->nullable();
            $table->text('agent')->nullable();
            $table->string('referer')->nullable();
            $table->timestamps();
        });

        Schema::create('canvas_users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('username')->unique()->nullable();
            $table->string('password');
            $table->text('summary')->nullable();
            $table->string('avatar')->nullable();
            $table->tinyInteger('dark_mode')->nullable();
            $table->tinyInteger('digest')->nullable();
            $table->string('locale')->nullable();
            $table->tinyInteger('role')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('canvas_portfolios', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug');
            $table->json('title');
            $table->json('summary')->nullable();
            $table->json('body')->nullable();
            $table->json('info')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('featured_image_caption')->nullable();
            $table->json('gallery_images')->nullable();
            $table->uuid('user_id')->index();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['slug', 'user_id']);
        });

        Schema::create('canvas_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug');
            $table->json('name');
            $table->uuid('user_id')->index();
            $table->timestamps();
            $table->softDeletes();
            $table->index('created_at');
            $table->unique(['slug', 'user_id']);
        });

        Schema::create('canvas_portfolios_categories', function (Blueprint $table) {
            $table->uuid('portfolio_id');
            $table->uuid('category_id');
            $table->unique(['portfolio_id', 'category_id']);
        });

        Schema::create('canvas_portfolios_views', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('portfolio_id')->index();
            $table->string('ip')->nullable();
            $table->text('agent')->nullable();
            $table->string('referer')->nullable();
            $table->timestamps();
            $table->index('created_at');
        });

        Schema::create('canvas_portfolios_visits', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('portfolio_id');
            $table->string('ip')->nullable();
            $table->text('agent')->nullable();
            $table->string('referer')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('canvas_posts');
        Schema::dropIfExists('canvas_tags');
        Schema::dropIfExists('canvas_topics');
        Schema::dropIfExists('canvas_posts_tags');
        Schema::dropIfExists('canvas_posts_topics');
        Schema::dropIfExists('canvas_views');
        Schema::dropIfExists('canvas_visits');
        Schema::dropIfExists('canvas_users');
        Schema::dropIfExists('canvas_portfolios');
        Schema::dropIfExists('canvas_categories');
        Schema::dropIfExists('canvas_portfolios_categories');
        Schema::dropIfExists('canvas_portfolios_views');
        Schema::dropIfExists('canvas_portfolios_visits');
    }
}
