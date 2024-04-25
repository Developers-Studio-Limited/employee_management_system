<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('request_response', function ($statusCode, $status, $message, $data=null) {
            if ($statusCode) {
                $response = response()->json([
                    'status_code' => $statusCode,
                    'success' => $status,
                    'message' => $message,
                    'data' => $data,
                ], $statusCode);
            } else {
                $response = response()->json([
                    'status_code' => 404,
                    'error' => "You have no data in Database.",
                    'message' => "You have to Seed Database",
                ], 404);
            }
            return $response;
        });
    }
}
