<?php

namespace App\Http\Controllers\TestHelpers;

use App\Http\Controllers\Controller;
use App\Models\User;

class TestController extends Controller
{
    public function allUsers()
    {
        return User::all();
    }

    public function getUser(User $user)
    {
        return $user;
    }

    public function deleteUser(User $user)
    {
        $user->delete();
    }

    public function deleteAllUsers()
    {
        User::truncate();
    }
}
