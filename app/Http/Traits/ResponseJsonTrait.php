<?php

namespace App\Http\Traits;

/**
 * @return
 */
trait ResponseJsonTrait
{
    public function responseJson ($status = true, $message = '', $data = null, $status_code = 200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $status_code);
    }
}
