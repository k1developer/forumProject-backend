<?php

namespace App\Http\Controllers\API\v1\Auth;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{

    /**
     * AuthController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth')->only('user');
    }

    /**
     * Register User
     * @method POST
     * @param Request $request
     */
    public function register(Request $request)
    {

        // Validate Form Inputs
        $request->validate([
           'name' => ['required'],
           'email' => ['required', 'email', 'unique:users'],
           'password' => ['required'],
        ]);


        // Insert User Into Database
        $user = resolve(UserRepository::class)->store($request);

        $defaultSuperAdminEmail = config('permission.default_super_admin_email');
        $user->email === $defaultSuperAdminEmail ? $user->assignRole('Super Admin') : $user->assignRole('User');

        return response()->json([
            'message' => 'user created successfully'
        ], Response::HTTP_CREATED);
    }

    /**
     * Login User
     * @method POST
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        // Validate Form Inputs
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        // Check User Credentials For Login
        if (Auth::attempt($request->only('email', 'password'))) {
            return response()->json(Auth::user(), Response::HTTP_OK);
        }

        throw ValidationException::withMessages([
            'email' => 'incorrect credentials.'
        ]);
    }

    /**
     * Get User
     * @return \Illuminate\Http\JsonResponse
     */
    public function user()
    {
        $user = [
          Auth::user(),
          'notifications' =>Auth::user()->unreadNotifications(),
          'message' => 'successful'
        ];
        return response()->json($user, Response::HTTP_OK);
    }

    /**
     * Logout User
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::logout();

        return response()->json([
            'message' => 'logged out successfully'
        ], Response::HTTP_OK);
    }


}
