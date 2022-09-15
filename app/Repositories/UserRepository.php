<?php


namespace App\Repositories;


use App\Models\User;
use App\Repositories\Interfaces\GetRepositoryInterface;
use App\Repositories\Interfaces\StoreRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserRepository implements StoreRepositoryInterface, GetRepositoryInterface
{
    /**
     * @param $id
     * @return mixed
     */
    public function get($id)
    {
        return User::find($id);
    }

    /**
     * @param Request $request
     */
    public function store(Request $request): User
    {
        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    }
}
