<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return string
     */
    public function username()
    {
        return 'username';
        // $this->middleware('guest')->except('logout');
        // $this->middleware('auth')->only('logout');
    }

    protected function attemptLogin(Request $request)
    {
        $credentials = $this->credentials($request);

        // Attempt login
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $user = Auth::user();

            // Check status_sdm from sdm table
            $status = DB::table('sdm')->where('user_id', $user->id)->value('status_sdm');

            if ($status === 'Tidak Aktif') {
                Auth::logout();
                return false; // Block login
            }

            return true;
        }

        return false;
    }
}
