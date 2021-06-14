<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Sources extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('categories', 'sources');
        Schema::rename('category_creator', 'source_creator');

        Schema::table('creator_info', function (Blueprint $table) {
            $table->renameColumn('category_id', 'source_id');
            $table->foreign('source_id')->references('id')->on('sources');
        });

        Schema::table('source_creator', function (Blueprint $table) {
           $table->dropForeign('category_creator_category_id_foreign');
           $table->dropForeign('category_creator_creator_id_foreign');
           $table->renameColumn('category_id', 'source_id');

           $table->foreign('creator_id')->references('id')->on('creators')->cascadeOnDelete();
           $table->foreign('source_id')->references('id')->on('sources')->cascadeOnDelete();
        });


        Schema::table('sources', function (Blueprint $table) {
           $table->dropForeign('categories_created_by_id_foreign');
           $table->foreign('created_by_id')->references('id')->on('users');
        });

        DB::statement(<<<SQL
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_by_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `generic` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `categories_created_by_id_foreign` (`created_by_id`),
  CONSTRAINT `categories_created_by_id_foreign` FOREIGN KEY (`created_by_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL
        );

        DB::statement(<<<SQL
CREATE TABLE `category_creator` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `creator_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_creator_creator_id_category_id_unique` (`creator_id`,`category_id`),
  KEY `category_creator_category_id_foreign` (`category_id`),
  CONSTRAINT `category_creator_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `category_creator_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `creators` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4194 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
