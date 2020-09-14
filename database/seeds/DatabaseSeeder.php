<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\User;

class DatabaseSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    DB::statement('SET FOREIGN_KEY_CHECKS = 0');
    $date = date("Y-m-d H:i:s");
    $time_zone = date("e");

    $User_1 = new User();
    $User_1->email = "admin@laravel-angular-starter.vagrant";
    $User_1->first_name = "Admin";
    $User_1->last_name = "User";
    $User_1->phone_number = "";
    $User_1->password =  User::encryptPassword("admin1234");
    $User_1->create_date = $date;
    $User_1->create_date_tz = $time_zone;
    $User_1->create_user_id = null;
    $User_1->save();
  }
}
