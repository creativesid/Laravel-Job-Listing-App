<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // show register form
     public function create(){
        return view('users.register');
    }

    // store data
    public function store(Request $request){
       $formFields = $request->validate([
           'name' => ['required', 'min:3'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => 'required|confirmed|min:6'
       ]);

        //Hash password
       $formFields['password'] = bcrypt($formFields['password']);

        //create user
       $user = USER::create($formFields);

        //login user
       auth()->login($user);
       
       return redirect('/')->with('message', 'Listing created and loggedIn');
    }

    // Logout
    public function logout(Request $request){
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('message', 'You have been logged out');

    }

    // show login form
     public function login(){
        return view('users.login');
    }

    // show login form
     public function authenticate(Request $request){
         $formFields = $request->validate([
            'email' => ['required', 'email'],
            'password' => 'required'
       ]);

       if(auth()->attempt($formFields)){
        $request->session()->regenerateToken();
        return redirect('/')->with('message', 'You are now logged in');
       }

       return back()->withErrors(['email' => 'Invalid credentials'])->onlyInput('email');
    }
}
