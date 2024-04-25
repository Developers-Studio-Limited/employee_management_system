<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\Employee;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Log;

use function App\errorLogs;

class ResetPasswordController extends Controller
{
    /**
     * Reset the password for a Employees.
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
            
            if (!$passwordReset || $passwordReset->token != $request->validated()['token']) {
                return response()->request_response(404, false, " Invalid Token");
            }
    
            if ($passwordReset->created_at > now()->addHour()) {
                $passwordReset->delete();
                return response()->request_response(422, false, "Expired Link. Please try again.");
            }

            $employee = Employee::firstWhere('email', $passwordReset->email);
    
            if (!$employee) {
                return response()->request_response(404, false, "Invalid Employee mail or employee not found");
            }
    
            $employee->password = bcrypt($request->validated()['password']);
            $employee->save();
            // Delete the password reset token record from the database
            $passwordReset->delete();

            return  response()->request_response(201, true, 'Password Successfully Updated', $employee);
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__, $ex->getMessage());
        }

    }
}
