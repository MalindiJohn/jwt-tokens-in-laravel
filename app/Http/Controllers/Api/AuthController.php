<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

//jwt token
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    // Login with email and password
    public function login(Request $request)
    {
        try {
            // Validate the request data
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            // Attempt to authenticate the user
            $credentials = $request->only('email', 'password');

            if (! $token = JWTAuth::attempt($credentials)) {

                // Authentication failed
                // return response()->json(['error' => 'Unauthorized'], 401);
                //throw an exception with message 'unauthorized'
                throw new \Exception('Invalid Credentials');

            }

            // Authentication successful, return token
            return response()->json(['message' => 'Login successful', 'token' => $token], 200);

        } catch (\Exception $th) {

            // JWT Exception
            return response()->json(['error_message' => 'Could not create token', 'error' => $th->getMessage()], 500);

        }
    }

    //get users
    function getUsers() {

        //error handling
        try {

            //get users
            $users = User::all();

            // request successful, return token
            return response()->json(['message' => 'Users fetched successfully', 'users' => $users], 200);

        } catch (\Exception $th) {

            // JWT Exception
            return response()->json(['error_message' => 'Could not get the users data', 'error' => $th->getMessage()], 500);

        }
    }
}
