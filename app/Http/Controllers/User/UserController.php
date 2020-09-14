<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\User;
use Carbon\Carbon;
use App\Http\Helper;
use Illuminate\Support\Facades\Auth;
use function Psy\debug;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;

/**
 * Class UserController
 *
 * @package App\Http\Controllers\User
 */
class UserController extends ApiController
{


    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = User::all();

        return $this->successResponse(collect($data));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), User::createRules());
        if ($validator->fails()) return $this->errorResponse($validator->errors(), Helper::HTTP_UNPROCESSABLE_ENTITY);

        $User = new User();
        $User->email = $request->email;
        $User->password = User::encryptPassword($request->password);
        $User->first_name = $request->first_name;
        $User->last_name = $request->last_name;
        $User->phone_number = $request->phone_number;
        $User->ad_create_date = date("Y-m-d H:i:s");
        $User->ad_create_date_tz = date("e");
        $User->ad_create_user_id = isset($request->user()->id) ? $request->user()->id : null;
        $User->save();

        return $this->successResponse(collect($User));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::where('id', $id)->firstOrFail();

        return $this->successResponse(collect($user));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $User = User::where('id', $id)->firstOrFail();

        $validator = Validator::make($request->all(), User::updateRules($id));
        if ($validator->fails()) return $this->errorResponse($validator->errors(), Helper::HTTP_UNPROCESSABLE_ENTITY);

        if($request->has('email') && $request->email != ""){
            $User->email = $request->email;
        }
        if($request->has('password') && $request->password != ""){
            $User->password = User::encryptPassword($request->password);
        }
        $User->first_name = $request->first_name;
        $User->last_name = $request->last_name;
        $User->phone_number = $request->phone_number;

        if($User->isDirty()) { //Only save if any of the field data has changed
            $User->ad_modify_date = date("Y-m-d H:i:s");
            $User->ad_modify_date_tz = date("e");
            $User->ad_modify_user_id = $request->user()->id;
            $User->save();
        }

        return $this->successResponse(collect($User));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $User = User::where('id', $id)->firstOrFail();
        $User->delete();

        return $this->successResponse(collect(User::UseModel($User)));
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), User::loginRules());
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 400);

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
           try {
                $oauth_url = env('APP_URL').'/oauth/token';
                $client = new Client();
                $body   = [
                    'json' => [
                        'grant_type' => 'password',
                        'client_id' => env('OAUTH_CLIENT_ID'),
                        'client_secret' => env('OAUTH_CLIENT_SECRET'),
                        'username' => $request->email,
                        'password' => $request->password,
                        'scope' => '*',
                    ]
                ];

                $response = $client->request(Helper::POST, $oauth_url, $body);
                $data = json_decode($response->getBody(), true);
                if ($response->getStatusCode() == Helper::HTTP_OK) {
                    $user = User::where('email', $request->email)->first();
                    Auth::guard('web')->login($user);

                    $clientResponse = [
                        'token_type' => $data['token_type'],
                        'expires_in' => $data['expires_in'],
                        'access_token' => $data['access_token'],
                        'refresh_token' => $data['refresh_token']
                    ];
                    return $this->successResponse(collect($clientResponse));
                } else {
                    return $this->errorResponse($data, $response->getStatusCode());
                }
            }
            catch (\Exception $e){
                if ($this->checkForUnauthorizedMessage($e->getMessage())) {
                    return $this->errorResponse("Invalid Oauth", Helper::HTTP_UNAUTHORIZED);
                }
                else {
                    return $this->errorResponse($e->getMessage(), Helper::HTTP_INTERNAL_SERVER_ERROR);
                }
            }
        }
        else {
            return $this->errorResponse( "Invalid Login Credentials", Helper::HTTP_UNAUTHORIZED);
        }
    }


    public function refresh(Request $request) {
        if($request->has('refresh_token')) {
            try {
                $oauth_url = env('APP_URL') . '/oauth/token';
                $client = new Client();
                $body = [
                    'json' => [
                        'grant_type' => 'refresh_token',
                        'client_id' => env('OAUTH_CLIENT_ID'),
                        'client_secret' => env('OAUTH_CLIENT_SECRET'),
                        'refresh_token' => $request->refresh_token,
                        'scope' => '*',
                    ]
                ];

                $response = $client->request(Helper::POST, $oauth_url, $body);
                $data = json_decode($response->getBody(), true);

                if ($response->getStatusCode() == Helper::HTTP_OK) {
                    $clientResponse = [
                        'token_type' => $data['token_type'],
                        'expires_in' => $data['expires_in'],
                        'access_token' => $data['access_token'],
                        'refresh_token' => $data['refresh_token'],
                    ];
                    return $this->successResponse(collect($clientResponse));
                } else {
                    return $this->errorResponse($data, $response->getStatusCode());
                }
            }
            catch (\Exception $e){
                if ($this->checkForUnauthorizedMessage($e->getMessage())) {
                    return $this->errorResponse("Invalid Refresh Token", Helper::HTTP_UNAUTHORIZED);
                }
                else {
                    return $this->errorResponse("Invalid Request", Helper::HTTP_BAD_REQUEST);
                }
            }
        } else {
            return $this->errorResponse( "Invalid Credentials", Helper::HTTP_UNAUTHORIZED);
        }
    }


    /**
     * Log the user out and forget the refresh token.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request) {
        Auth::logout();

        return $this->successResponse("Logged out");
    }


    public function checkForUnauthorizedMessage($message){
        $value = false;

        if (strpos($message, 'revoked') !== false ||
            strpos($message, 'invalid') !== false ||
            strpos($message, 'failed') !== false ||
            strpos($message, 'invalid_client') !== false ){

            $value = true;
        }

        return $value;
    }
}
