<?php

namespace Viettuans\FileManager\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    /**
     * successResponse
     *
     * @param  mixed $data
     * @return void
     */
    public function successResponse($data = [])
    {
        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }
    
    /**
     * errorResponse
     *
     * @param  mixed $message
     * @param  mixed $status
     * @return void
     */
    public function errorResponse($message, $status = 200)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
        ], $status);
    }
}
