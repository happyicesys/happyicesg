<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Laracasts\Flash\Flash;
use Carbon\Carbon;


class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins{
        AuthenticatesAndRegistersUsers::postLogin as laravelPostLogin;
    }

    protected $table = 'users';

//    Assigning default route path
    // protected $redirectPath = '/transaction';
    protected $redirectAfterLogout = '/auth/login';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    public function redirectPath()
    {
        // Logic that determines where to send the user
        if (\Auth::user()->type == 'marketer') {
            return '/market/deal';
        }

        if(auth()->user()->hasRole('franchisee')) {
            return '/ftransaction';
        }

        return '/transaction';
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    //Register taken over by admin view
    public function getRegister()
    {
        return view('user.create');
    }

    //Redirect path after register new user
    public function postRegister()
    {
        return redirect('/user');
    }

    //Enable login both with email and username
    public function postLogin(Request $request)
    {
        $field = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $request->merge([$field => $request->input('login')]);
        $this->username = $field;
        return self::laravelPostLogin($request);
    }

    // get password reset view
    public function getPasswordReset()
    {
        return view('password.reset');
    }

    // reset password functionality
    public function resetPassword(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'email' => 'required',
        ],[
            'username.required' => 'Please fill in the Username',
            'email.required' => 'Please fill in the Email',
        ]);
        $user = User::whereUsername($request->username)->whereEmail($request->email)->first();
        if($user){
            $new_password = str_random(6);
            $user->password = $new_password;
            $user->save();
            $this->sendPasswordResetEmail($user, $new_password);
        }else{
            Flash::error('The Username or Email are not matched');
        }

        return view('password.reset');
    }

    // send password reset email
    private function sendPasswordResetEmail($user, $new_password)
    {
        $today = Carbon::now()->format('d-m-Y H:i');
        $send_to = $user->email;
        $sender = 'system@happyice.com.sg';
        $data = [
            'user_id' => $user->id,
            'id_name' => $user->name,
            'new_password' => $new_password,
        ];
        Mail::send('email.reset_password', $data, function ($message) use ($user, $send_to, $today, $sender)
        {
            $message->from($sender);
            $message->subject('Email Password Reset for '.$user->id.' - '.$user->name.' ('.$today.')');
            $message->setTo($send_to);
        });
    }
}
