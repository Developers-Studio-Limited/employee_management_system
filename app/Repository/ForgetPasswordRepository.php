<?php

namespace App\Repository;

use App\Models\PasswordReset;
use App\Repository\Interface\ForgetPasswordRepositoryInterface;

use function App\errorLogs;

class ForgetPasswordRepository implements ForgetPasswordRepositoryInterface {
    
    /**
     * Initiate the password reset process.
     *
     * @param \App\Models\User|null $user The user instance associated with the email.
     * @param array $data Data containing email address for password reset.
     * @return \App\Models\PasswordReset|null Newly created password reset instance if successful, null otherwise.
     */
    public function forgetPassword($user, $data){
        try {            
            if (!$user) {
                return response()->request_response(404, false, 'Email does not exist');
            }
    
            PasswordReset::where("email", $data["email"])->delete();
            $token = mt_rand(100000, 999999);
            $codeData = PasswordReset::create([
                'email' => $user->email,
                'token' => $token,
                'created_at' => now(),
            ]);
            return $codeData;
        } catch (\Exception $ex) {
            errorLogs(__METHOD__, __LINE__, $ex->getMessage());
        }
    }
}