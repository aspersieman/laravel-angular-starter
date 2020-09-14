<?php
namespace App\Http;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Exception\RequestException;

class Helper
{
    /**
     * The name to use for logging from this class
     *
     * @var array
     */
    const MODULE_NAME = "Helper";

    /**
     * HTTP status code for successful requests.
     */
    const HTTP_OK = 200;

    /**
     * HTTP status code for invalid requests.
     */
    const HTTP_BAD_REQUEST = 400;

    /**
     * HTTP status code for unauthorized requests.
     */
    const HTTP_UNAUTHORIZED = 401;

    /**
     * HTTP status code for forbidden requests.
     */
    const HTTP_FORBIDDEN = 403;

    /**
     * HTTP status code for requests not found.
     */
    const HTTP_NOT_FOUND = 404;

    /**
     * HTTP status code for unprocessable entity.
     */
    const HTTP_UNPROCESSABLE_ENTITY = 422;

    /**
     * HTTP status code for an internal server error.
     */
    const HTTP_INTERNAL_SERVER_ERROR = 500;

    /**
     * HTTP status code for a not implemented server error.
     */
    const HTTP_NOT_IMPLEMENTED = 501;

    const GET = "GET";
    const POST = "POST";
    const PUT = "PUT";
    const DELETE = "DELETE";

    public static function generateUUID(){
        return DB::select("SELECT GET_UUID() AS uuid")[0]->uuid;
    }

    public static function errorHandler($error_no, $error_str, $error_file, $error_line){
        switch ($error_no) {
            case 1:
                $level = "E_ERROR";
                break;
            case 2:
                $level = "E_WARNING";
                break;
            case 4:
                $level = "E_PARSE";
                break;
            case 8:
                $level = "E_NOTICE";
                break;
            case 16:
                $level = "E_CORE_ERROR";
                break;
            case 32:
                $level = "E_CORE_WARNING";
                break;
            case 64:
                $level = "E_COMPILE_ERROR";
                break;
            case 128:
                $level = "E_COMPILE_WARNING";
                break;
            case 256:
                $level = "E_USER_ERROR";
                break;
            case 512:
                $level = "E_USER_WARNING";
                break;
            case 1024:
                $level = "E_USER_NOTICE";
                break;
            default:
                return;
        }

        print(date("Y-m-d H:i:s")." - ".$level." : ".strip_tags($error_str)." in file \"".$error_file."\" on line ".$error_line."\r\n");
    }

    public static function callUrl($method, $url, $data = [])
    {
        //Check if $url is external from known source and should not 
        //rely on normal auth
        if( strpos($url, '/api/v1/test') !== false && empty($data)) {
            $data = [
                'securityHash' => env('SESSION_TOKEN')
            ];
        }

        $response = null;
        $client = new Client();
        $body   = [
            'json'    => $data,
            'headers' => [
                'Content-Type' => "application/json",
                'Accept'       => 'application/json'
            ]
        ];

        try {
            $response = $client->request(strtoupper($method), $url, $body);
        }
        catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
            }
        }
        return $response;
    }
}
