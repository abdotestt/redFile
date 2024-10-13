<?php

// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        if($user){
            $user = User::find(1); // Example user
            $roleId=ModelHa
            // Find role by ID
            $role = Role::find(1); // Example role ID
            
            // Assign role by ID
            $user->assignRole($role->name);
                    return response()->json(['access_token' => $token, 'token_type' => 'Bearer']);

        }else{
            return response()->json(["error"=>"eroor"]);

        }
    }
    // statut ok
    public function login(Request $request)
    {
        // Validate the request data
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        // Check if the credentials are valid
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Get the authenticated user
        $user = Auth::user();

        // Generate a new token for the user
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token, 
            'token_type' => 'Bearer',
            'user' => $user
        ]);

    }

//    statut ok


    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }
}
