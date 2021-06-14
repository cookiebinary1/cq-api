<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BigSchema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL

CREATE TABLE `collabs` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`user_id` INT NOT NULL,
	`protagonist_id1` INT NOT NULL,
	`protagonist_id2` INT NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `creators` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`additional_data` TEXT NOT NULL,
	`created_by_id` INT,
	`user_id` INT NOT NULL,
	`status` INT NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `likes` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`user_id` INT NOT NULL,
	`collab_id` INT NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `creator_info` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`creator_id` INT NOT NULL,
	`field` varchar(255) NOT NULL,
	`value` TEXT(255) NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `categories` (
	`id` INT NOT NULL AUTO_INCREMENT UNIQUE,
	`created_by_id` INT,
	`name` varchar(255) NOT NULL,
	`description` TEXT NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `creator_category` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`creator_id` INT NOT NULL,
	`category_id` INT NOT NULL,
	PRIMARY KEY (`id`)
);
SQL;

        foreach (explode(";", $sql) as $command) {
            if (trim($command))
                DB::statement($command);
        }
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
