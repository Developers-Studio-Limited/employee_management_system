<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Requests\ForgetPasswordRequest;
use App\Http\Requests\EmployeeLoginRequest;
use App\Services\ForgetPasswordService;
use App\Http\Requests\EmployeeRequest;
use App\Jobs\SendResetPasswordEmail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Employee;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Queue;

use function App\errorLogs;

class AuthController extends Controller
{
    protected $passwordService;

    public function __construct(ForgetPasswordService $PasswordService)
    {
        $this->passwordService = $PasswordService;
    }

     /**
     * Logs in an employee with the provided credentials.
     *
     * Method: POST
     *
     * @param \Illuminate\Http\EmployeeLoginRequest $request The request containing the employee's login credentials.
     * @return \Illuminate\Http\JsonResponse JSON indicating success or failure of the login attempt.
     */
    public function login(EmployeeLoginRequest $request) {
        try {
            $credentials = $request->all();
            $token = Auth::guard('employee-api')->attempt($credentials);

            $statusCode = $token ? 200 : 401;
            $success = $token ? true : false;
            $message = $token ? "You are Logged in Successfully" : "Invalid email or password";

            return response()->request_response($statusCode, $success, $message, $token ? $token : null);
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__,$ex->getMessage());
        }
    }

    /**
     * Registers a new employee.
     *
     * Method: POST
     *
     * @param \Illuminate\Http\EmployeeRequest $request The request containing the new employee's details.
     * @return \Illuminate\Http\JsonResponse JSON indicating success or failure of the registration.
     */
    public function register(EmployeeRequest $request) {
        try {
            $password = bcrypt($request->input('password'));
            $data = $request->validated();
            $data['password'] =  $password;
            $employee = Employee::create($data);
            
            event(new Registered($employee));
            Queue::push(function ($job) use ($employee) {
                $employee->sendEmailVerificationNotification();
                $job->delete();
            });

            return response()->request_response(201, true, "User created successfully. We've sent you an email. Please verify your email before logging in", $employee);
            
        } catch (\Exception $ex) {
            return  errorLogs(__METHOD__, __LINE__,$ex->getMessage());
        }
    }

    /**
     * Refreshes the authentication token for the authenticated employee.
     *
     * Method: POST
     *
     * @return \Illuminate\Http\JsonResponse JSON indicating success or failure of the token refresh.
     */
    public function refreshToken() {
        try{
            if (auth()->guard('employee-api')->check()) {
                $newToken = JWTAuth::refresh(JWTAUTH::getToken());
                $statusCode = $newToken ? 200 : 401;
                $success = $newToken ? true : false;
                $message = $newToken ? "Access token generated" : "Token not found";

                return response()->request_response($statusCode, $success, $message, $newToken);
            }
        }catch(Exception $ex){
            return errorLogs(__METHOD__, __LINE__, $ex->getMessage());
        }
    }

    /**
     * Retrieves the profile information of the authenticated employee.
     *
     * Method: POST
     *
     * @return \Illuminate\Http\JsonResponse JSON containing the profile information.
     */
    public function profile() {
        try {
            $status = auth()->guard('employee-api')->check();
            $userData = auth()->guard('employee-api')->user();

            $statusCode = $status ? 200 : 404;
            $success = $status && isset($userData);
            $message = $status ? "Fetch the profile successfully" : "User is not found. Please login first!";

            return response()->request_response($statusCode, $success, $message, compact('userData'));

        } catch (\Exception $e) {
           return errorLogs(__METHOD__,__LINE__,$e->getMessage());
        }
    }

    /**
     * Updates the profile of the authenticated employee.
     *
     * Method: POST
     *
     * @param \Illuminate\Http\Request $request The request containing the updated employee data.
     * @param int $id The ID of the employee whose profile is being updated.
     * @return \Illuminate\Http\JsonResponse JSON indicating success or failure of the profile update.
     */
    public function updateProfile(EmployeeRequest $request, $id) {
        try {
            $response = null;
            if (!auth()->guard('employee-api')->check()) {
                $response = response()->request_response('401', false, "You are not authorised");
            }
                $user = Employee::findOrFail($id);
                
                if ($user->id != auth()->guard('employee-api')->user()->id) {
                    $response = response()->request_response(403, false,  'Unauthorized action');
                } else {
                    $userData = $request->validated();
                    if(isset($userData['password'])){
                        $userData['password'] = bcrypt($userData['password']);
                    }
                    $user->update($userData);
                    $response = response()->request_response(200, true, "Profile Update successfully",$user);
                }
            return $response;
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__, __LINE__, $ex->getMessage());
        }
    }

     /**
     * Logs out the authenticated employee.
     *
     * Method: POST
     *
     * @return \Illuminate\Http\JsonResponse JSON indicating success or failure of the logout attempt.
     */
    public function logout() {
        try {
            if (auth()->guard('employee-api')->check()) {
                auth()->guard('employee-api')->logout();
            }
            return response()->request_response(204, true, "Logout successful");
        } catch (\Exception $e) {
            return errorLogs(__METHOD__, __LINE__, $e->getMessage());
        }
    }

    /**
     * Sends a password reset email to the provided email address.
     *
     * Method: POST
     *
     * @param \Illuminate\Http\Request $request The request containing the email address.
     * @return \Illuminate\Http\JsonResponse JSON indicating success or failure of the password reset email.
     */
    public function forgetPassword(ForgetPasswordRequest $request) {
        try {

            $user = Employee::where('email', $request->validated()["email"])->first();
            
            $codeData = $this->passwordService->forgetPassword($user, $request->validated());
    
            SendResetPasswordEmail::dispatch($request->validated()['email'] ,$codeData);
            return response()->request_response(200, true, "we sent you an email to reset your password");

        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__,$ex->getMessage());
        }
    }
}
