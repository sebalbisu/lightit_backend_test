<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\ValidateEmailRequest;
use App\Models\User;
use App\Notifications\Registration\EmailValidated;
use App\Notifications\Registration\EmailValidationPending;
use Illuminate\Support\Facades\Storage;

class RegisterController extends Controller
{
    public function register(RegistrationRequest $request, User $user)
    {
        $path = Storage::disk('public')->put('photo', $request->file('photo'));

        $data = $request->safe()
            ->merge(['photo' => $path])
            ->all();

        $user->fill($data)
            ->setPassword($request->validated('password'))
            ->save();

        $user->notify(app(EmailValidationPending::class));

        return $user;
    }

    public function validateEmail(ValidateEmailRequest $request)
    {
        $user = $request->user;

        $user->verifyEmail()->save();

        $user->notify(app(EmailValidated::class));

        return view('email_validated');
    }
}
