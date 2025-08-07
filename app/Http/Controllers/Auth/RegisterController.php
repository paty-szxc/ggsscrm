<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserRegister;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;


class RegisterController extends Controller {
    public function register(Request $req){
        try{
            $validated = $req->validate([
                'emp_code' => 'required|string|max:20|unique:user_registers,emp_code',
                'first_name' => 'required|string|max:50',
                'surname' => 'required|string|max:50',
                'username' => [
                    'required',
                    'string',
                    'max:30',
                    'unique:user_registers,username',
                ],
                'password' => [
                    'required',
                    'confirmed',
                    Rules\Password::min(8)
                        ->letters()
                ],
                'role' => 'required|string|in:Admin,Encoder,Viewer,Encoder & Viewer,Checker',
            ]);

            DB::beginTransaction();
            UserRegister::create([
                'emp_code' => $validated['emp_code'],
                'first_name' => $validated['first_name'],
                'surname' => $validated['surname'],
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
            ]);
            DB::commit();
            return response()->json(['message' => $req->first_name . ' successfully registered'], 200);
        }
        catch(\Exception $e){
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}