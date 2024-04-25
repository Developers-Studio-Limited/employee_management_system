<?php
namespace App;

use App\Models\ErrorLog;


/**
 * Logs errors and returns a JSON response with error details.
 *
 * @param string $method_name The name of the method where the error occurred.
 * @param int $line_no The line number where the error occurred.
 * @param mixed $error The error message or data.
 * @param mixed|null $transactinId The transaction ID associated with the error (optional).
 * @param mixed|null $apiRequestId The API request ID associated with the error (optional).
 * @return \Illuminate\Http\JsonResponse JSON response with error details.
 */
function errorLogs($method_name, $line_no, $error, $transactinId=null, $apiRequestId = null)
{
    $error_log = new ErrorLog;
    $error_log->method_name = $method_name;
    $error_log->line_no = $line_no;
    $error_log->error = isset($error) ? json_encode($error) : NULL;
    $error_log->api_request_id = $apiRequestId;
    $error_log->save();
    $user_info = get_user_agent();
    $real_ip = getIpAddress($user_info);

    $error = json_encode(array(
        "message" => 'Error found in ' . $method_name . ' on line no ' . $line_no,
        "error" => $error,
        "transactionId" => $transactinId ? $transactinId : NULL,
        "apiRequestId" => $apiRequestId ? $apiRequestId : NULL,
        "environment" => config('app.env'),
        "ip_address" => $real_ip
    ));

    return response()->json($error);
}
/**
 * Retrieves the user agent from the HTTP headers.
 *
 * @return string|null The user agent string or null if not found.
 */
function get_user_agent() {
    return $_SERVER['HTTP_USER_AGENT'] ?? null;
}

/**
 * Retrieves the IP address of the client.
 *
 * @param string|null $user_agent The user agent string.
 * @return string|null The IP address or null if not found.
 */
function getIpAddress($user_agent) {
    return $_SERVER['REMOTE_ADDR'] ?? null;
}
    