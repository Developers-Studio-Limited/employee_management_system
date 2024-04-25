<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\ForgetPasswordRequest;
use App\Http\Requests\UserLoginRequest;
use App\Services\ForgetPasswordService;
use Illuminate\Auth\Events\Registered;
use App\Http\Controllers\Controller;
use App\Jobs\SendResetPasswordEmail;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UserRequest;
use Spatie\Permission\Models\Role;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Queue;

use function App\errorLogs;

class AuthController extends Controller
{

    protected $passwordService;

    public function __construct(ForgetPasswordService $forgetPasswordService)
    {
        $this->passwordService = $forgetPasswordService;
    }

    /**
     * Authenticates a user.
     *
     * Method: POST
     * @param UserLoginRequest $request The login request containing user credentials.
     * @return \Illuminate\Http\JsonResponse JSON response with token on success, error message on failure.
     */
    public function login(UserLoginRequest $request) {
        try {
            $credentials = $request->validated();
            $token = Auth::guard('api')->attempt($credentials);

            $statusCode = $token ? 200 : 422;
            $success = $token ? true : false;
            $message = $token ? "You are Logged in Successfully" : "Invalid email or password";

            return response()->request_response($statusCode, $success, $message, $token ? compact('token') : null);
        } catch (Exception $e) {
            return errorLogs(__METHOD__, __LINE__, $e->getMessage());
        }
    }

    /**
     * Registers a new user.
     *
     * Method: POST
     * @param UserRequest $request The registration request conataining user data
     * @return Illuminate\Http\JsonResponse Json response with user data on success. error message on failure
     */
    public function register(UserRequest $request) {
        try {
            $password = bcrypt($request->input('password'));

            $data = $request->validated();
            $data['password'] = $password;
            $user = User::create($data);

            $role = Role::where('name', 'admin')->first();
            $user->assignRole($role);

            event(new Registered($user));
            Queue::push(function ($job) use ($user) {
                $user->sendEmailVerificationNotification();
                $job->delete();
            });
    
    
            return response()->request_response(201, true, "User created successfully. We've sent you an email. Please verify your email before logging in.", compact('user'));

        } catch (\Exception $e) {
            return errorLogs(__METHOD__, __LINE__, $e->getMessage(), auth()->id());
        }
    }

    /**
     * Retrieves the profile data of the authenticated user.
     *
     * Method: GET
     * @return \Illuminate\Http\JsonResponse JSON response with user profile data on success, error message on failure.
     */
    public function profile() {
        try {
            $userData = auth()->guard('api')->user();
            $status = auth()->guard('api')->check();
            
            $statusCode = $status ? 200 : 404;
            $success = $status && isset($userData);
            $message = $status ? "Fetch the profile successfully" : "User is not found. Please login first!";
            
            return response()->request_response($statusCode, $success, $message, compact('userData'));

        } catch (\Exception $e) {
           return errorLogs(__METHOD__,__LINE__,$e->getMessage());
        }
    }

    /**
     * Updates the profile of the authenticated user.
     *
     * Method: POST
     *
     * @param \Illuminate\Http\Request $request The request containing the updated user data.
     * @param int $id The ID of the user whose profile is being updated.
     * @return \Illuminate\Http\JsonResponse JSON indicating success or failure of the profile update.
     */
    public function updateProfile(UserRequest $request, $id) {
        try {
            $response = null;
            if (!auth()->guard('api')->check()) {
                $response = response()->request_response('401', false, "You are unauthorized");
            } else {
                $user = User::findOrFail($id);
                if ($user->id != auth()->guard('api')->user()->id) {
                    $response = response()->request_response(403, false,  'Unauthorized action');
                } else {
                    $userData = $request->validated();
                    if(isset($userData['password'])){
                        $userData['password'] = bcrypt($userData['password']);
                    }
                    $user->update($userData);
                    $response = response()->request_response(200, true, "Profile Update successfully",$user);
                }
            }
            return $response;
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__, __LINE__, $ex->getMessage());
        }
    }

     /**
     * Logs out the authenticated user.
     *
     * Method: POST
     * @return \Illuminate\Http\JsonResponse JSON indicating successful logout or error message.
     */
    public function logout() {
        try {
            if (auth()->guard( 'api' )->check()) {
                auth()->guard('api')->logout();
                return response()->request_response(204, true, "Logout successful");
            }
        } catch (\Exception $e) {
            return errorLogs(__METHOD__, __LINE__, $e->getMessage());
        }
    }

     /**
     * Refreshes the JWT token for the authenticated user.
     *
     * Method: GET
     * @return \Illuminate\Http\JsonResponse JSON with new token on success, error message on failure.
     */
    public function RefreshToken() {
        try{
            if (auth()->guard('api')->check()) {
                $newToken = JWTAuth::refresh(JWTAUTH::getToken());
                $statusCode = $newToken ? 201 : 401;
                $success = $newToken ? true : false;
                $message = $newToken ? 'Access token generated' : 'Token not found';

                return response()->request_response($statusCode, $success, $message, $newToken);
            }
        }catch(Exception $ex){
            return errorLogs(__METHOD__, __LINE__, $ex->getMessage());
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

            $user = User::where('email', $request->validated()["email"])->first();
            $codeData = $this->passwordService->forgetPassword($user, $request->validated());
            SendResetPasswordEmail::dispatch($request->validated()['email'] ,$codeData);
            return response()->request_response(200, true, "we sent you an email to reset your password");

        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__,$ex->getMessage());
        }
    }
}
