<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticable
{
    use Notifiable, HasApiTokens, SoftDeletes;

    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
        'first_name',
        'last_name',
        'phone_number'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];


    public static function createRules(){
        return [
            "email" => "required|email|unique:users,email",
            "password" => "required|min:10|regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/",
            "confirm_password" => "required|same:password",
            "phone_number" => "sometimes|nullable",
            "first_name" => "required|min:3|nullable",
            "last_name" => "required|min:3|nullable",
        ];
    }

    public static function updateRules($id){
        return [
            "email" => "sometimes|email|unique:users,email,".$id,
            "password" => "sometimes|nullable|min:10|regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/",
            "confirm_password" => "sometimes|nullable|same:password",
            "phone_number" => "sometimes|nullable",
            "first_name" => "sometimes|nullable",
            "last_name" => "sometimes|nullable",
        ];
    }

    public static function loginRules(){
        //return
        return [
            "email" => "required",
            "password" => "required",
        ];
    }

    public static function UserModel($User){
        return [
            "id" => $User->id,
            "first_name" =>  $User->first_name,
            "last_name" => $User->last_name,
            "email" => $User->email,
            "phone_number" => $User->phone_number,
            "created_at" => $User->ad_create_date,
            "created_at_tz" => $User->ad_create_date_tz,
            "created_by" => User::UserInfoModel($User->ad_create_user_id),
            "updated_at" => $User->ad_modify_date,
            "updated_at_tz" => $User->ad_modify_date_tz,
            "updated_by" => User::UserInfoModel($User->ad_modify_user_id)
        ];
    }

    public static function UserInfoModel($user_id){
        $User = User::where("id", $user_id)->first();

        if(!isset($User)) return null;
        return [
            "id" => $User->id,
            "first_name" =>  $User->first_name,
            "last_name" => $User->last_name,
            "email" => $User->email,
            "phone_number" => $User->phone_number
        ];
    }

    public static function encryptPassword($password){
        return bcrypt($password);
    }
}
