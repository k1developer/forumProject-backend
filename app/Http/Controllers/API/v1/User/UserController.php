<?php

namespace App\Http\Controllers\API\v1\User;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Get User Notifications
     * @return \Illuminate\Http\JsonResponse
     */
    public function userNotifications()
    {
        return \response()->json(auth()->user()->unreadNotifications(), Response::HTTP_OK);
    }
}
