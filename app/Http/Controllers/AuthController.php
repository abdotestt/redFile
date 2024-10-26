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

        /**
     * @group Auth
     * Register a new user
     *
     * This endpoint allows you to register a new user.
     *
     * @bodyParam name string required The name of the user. Example: John Doe
     * @bodyParam email string required The email of the user. Example: johndoe@example.com
     * @bodyParam password string required The password of the user. Example: secret
     *
     * @response 201 {
     *  "user": {
     *      "id": 1,
     *      "name": "John Doe",
     *      "email": "johndoe@example.com",
     *      "created_at": "2023-10-20",
     *      "updated_at": "2023-10-20"
     *  },
     *  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9"
     * }
 */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        if($user){
            $role = Role::findByName('user');
            if($role){
                $user->assignRole($role);
                $roleAssigned = $role->name;
            }else{
                $roleAssigned = "failed to affect a role to this user";
            }
            return response()->json(['access_token' => $token, 'token_type' => 'Bearer' ,'your role is : '=>$roleAssigned]);    
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

    /**
     * @group Auth
     * Logout the user
     *
     * This endpoint requires authentication.
     * 
     * @authenticated
     *
     * @response 200 {
     *  "message": "Logged out successfully."
     * }
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }
}
