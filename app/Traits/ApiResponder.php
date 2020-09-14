<?php
namespace  App\Traits;
use App\Http\Helper;
use Illuminate\Support\Collection;

trait ApiResponder {

    public function successResponse($data, $code = Helper::HTTP_OK){
        return response()->json(
            [
                'data'  => $data,
                'error' => '',
                'code'  => $code
            ],
            $code
        );
    }

    protected function errorResponse($message, $code = Helper::HTTP_BAD_REQUEST){
        return response()->json(
            [
                'data' => '',
                'error' => $message,
                'code'  => $code
            ],
            $code
        );
    }
}
