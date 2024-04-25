<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\ResetPasswordRequest;
use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;

use function App\errorLogs;

class ResetPasswordController extends Controller
{
    /**
     * Reset the password for a user.
     *
     * Method: POST
     * @param \Illuminate\Http\ResetPasswordRequest $request The HTTP request object containing the reset password data.
     * @return \Illuminate\Http\JsonResponse JSON response indicating the status of password reset operation.
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $token = $request->validated()['token'];
            $passwordReset = PasswordReset::where('token', $token)->latest()->first();
    
            if (!$passwordReset || $passwordReset->token != $token || $passwordReset->created_at > now()->addHour()) {
                $statusCode = (!$passwordReset || $passwordReset->token != $token) ? 404 : 422;
                $message = (!$passwordReset) ? 'Invalid Token' : 'Expired Link. Please try again.';
                return response()->request_response($statusCode, false, $message);
            }
    
            $employee = User::firstWhere('email', $passwordReset->email);
    
            if (!$employee) {
                return response()->request_response(404, false, 'Invalid Employee mail or employee not found');
            }
    
            $employee->password = bcrypt($request->validated()['password']);
            $employee->save();
            $passwordReset->delete();
    
            return response()->request_response(201, true, 'Password Successfully Updated', $employee);
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__, $ex->getMessage());
        }
    }
}
