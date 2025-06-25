<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function store(Request $request){
        try{
            if (!Auth::attempt($request->only('username', 'password'), $request->filled('remember'))) {
                return back()->withErrors([
                    'username' => 'Invalid credentials.',
                ]);
            }

            $request->session()->regenerate();
            return inertia()->location('/');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Request $req){
        Auth::guard('web')->logout();
        $req->session()->invalidate();
        $req->session()->regenerateToken();
        return redirect('/login');
    }
}