<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;

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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('guest:admin')->except('adminLogout');
        $this->middleware('guest:vendor')->except('vendorLogout');

    }
 

    // ====================== Admin===========================

    public function showAdminLoginForm()
    {
        return view('admin.auth.login');
    }


    public function adminLogin(Request $request)
    {
        $this->validate($request, [
            'email'   => 'required|email',
            'password' => 'required|min:6'
        ]);

        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {
            $credentials = request(['email', 'password']);
            if ($token = auth('admin-api')->attempt($credentials)) {
                return redirect('/admin?token='.$token);
            }
        }
        return back()->withInput($request->only('email', 'remember'));
    }


    public function adminLogout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $this->loggedOut($request) ?: redirect('/');
    }



    // ====================== Vendor===========================

    public function showVendorLoginForm()
    {
        return view('vendor.auth.login');
    }

    public function vendorLogin(Request $request)
    {
        $this->validate($request, [
            'email'   => 'required|email',
            'password' => 'required|min:6'
        ]);

        if ( Auth::guard('vendor')->attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {
            $credentials = request(['email', 'password']);
            if ($token = auth('vendor-api')->attempt($credentials)) {
                return redirect('/vendor?token='.$token);
            }
        }
        return back()->withInput($request->only('email', 'remember'));
    }


    public function vendorLogout(Request $request)
    {
        Auth::guard('vendor')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $this->loggedOut($request) ?: redirect('/');
    }

    // ====================== Customer ===========================

      public function showCustomerLoginForm()
      {
          return view('authCustomer.login');
      }
  
  
      public function customerLogin(Request $request)
      {
          $this->validate($request, [
              'email'   => 'required|email',
              'password' => 'required|min:6'
          ]);
  
          if (Auth::guard('customer')->attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {
  
              return redirect()->intended('/customer');
          }
          return back()->withInput($request->only('email', 'remember'));
      }
  
      // =================================================

      
}
