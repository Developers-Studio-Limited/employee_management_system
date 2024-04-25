<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use App\Models\User;

use function App\errorLogs;

class VerifyEmailController extends Controller
{
    /**
     * Handle email verification for users.
     *
     * Method: GET
     * @param \Illuminate\Http\Request $request The HTTP request object.
     * @return \Illuminate\Http\RedirectResponse Redirects the user based on verification status.
     */
    public function __invoke(Request $request)
    {
        try {
            $user = User::find($request->route('id'));

            if (!$user) {
                $user = Employee::find($request->route('id'));
            }

            if (!$user) {
                return response()->request_response(404, false, "User not found");
            }

            if ($user instanceof User) {
                if ($user->hasVerifiedEmail()) {
                    return redirect()->route('verification.already-success');
                }

                if ($user->markEmailAsVerified()) {
                    event(new Verified($user));
                }

                return redirect()->route('verification.success');
            } elseif ($user instanceof Employee) {
                
                if ($user->email_verified_at) {
                    return redirect()->route('verification.already-success');
                }

                $user->email_verified_at = now();
                $user->save();

                return redirect()->route('verification.success');
            }
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__, __LINE__, $ex->getMessage());
        }
    }
}
