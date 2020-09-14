<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class CreateGenerateUuidFunction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $db_user = env('DB_USERNAME');
        $db_host = env('DB_HOST');
        $query = "DROP FUNCTION IF EXISTS GET_UUID;
                  DELIMITER $$
                      CREATE DEFINER = `{$db_user}`@`{$db_host}` FUNCTION `GET_UUID`() RETURNS varchar(36) CHARSET utf8mb4
                        DETERMINISTIC
                      BEGIN
                            DECLARE new_uuid VARCHAR(36);
                            SET new_uuid = CONCAT(SUBSTR(UUID(), 15, 4),SUBSTR(UUID(), 10, 4),SUBSTR(UUID(), 1, 8),SUBSTR(UUID(), 20, 4),SUBSTR(UUID(), 25));
                            RETURN (new_uuid);
                      END$$
                  DELIMITER ; ";

        DB::unprepared($query);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP FUNCTION IF EXISTS GET_UUID;");
    }
}
