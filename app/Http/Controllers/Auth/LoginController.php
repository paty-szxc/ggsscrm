<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException as ValidationValidationException;
use Inertia\Inertia;

class LoginController extends Controller {
    public function create(){
        if(Auth::check()) {
            return redirect('/');
        }
        else{
            return Inertia::render('Login');
        }
    }

    // public function store(Request $request){
    //     try{
    //         if (!Auth::attempt($request->only('username', 'password'), $request->filled('remember'))) {
    //             return back()->withErrors([
    //                 'username' => 'Invalid credentials.',
    //             ]);
    //         }

    //         $request->session()->regenerate();
    //         return inertia()->location('/');
    //     } catch (\Exception $e) {
    //         return back()->withErrors(['error' => $e->getMessage()]);
    //     }
    // }
    public function store(Request $req){
        try{
            // Validate the request data
            $req->validate([
                'username' => 'required',
                'password' => 'required',
            ]);

            if(!Auth::attempt($req->only('username', 'password'), $req->filled('remember'))){
                // If login fails, throw a ValidationException with a custom message.
                // This will be caught by Inertia and handled as an AJAX error.
                throw ValidationValidationException::withMessages([
                    'username' => 'The provided credentials do not match our records.',
                ]);
            }

            $req->session()->regenerate();
            
            // This will be handled as an Inertia success response, which won't cause a full page reload.
            return inertia()->location('/');

        }
        catch(ValidationValidationException $e){
            // For AJAX requests, return a JSON response with the validation errors.
            // This is crucial for your Vue component to receive the error without a page reload.
            return response()->json([
                'errors' => $e->errors(),
            ], 422);
        }
        catch(\Exception $e){
            //handle other potential errors, returning a 500 server error
            return response()->json([
                'message' => 'Login failed. Please try again later.',
            ], 500);
        }
    }

    public function destroy(Request $req){
        Auth::guard('web')->logout();
        $req->session()->invalidate();
        $req->session()->regenerateToken();
        return redirect('/login');
    }
}