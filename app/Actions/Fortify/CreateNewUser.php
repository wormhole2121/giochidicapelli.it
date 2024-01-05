<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email', // verifica che l'email non sia già stata utilizzata
            ],
            'phone' => [
                'required',
                'string',
                'max:15',
                'unique:users,phone', // verifica che il telefono non sia già stato utilizzato
            ],
            'password' => [
                'required',
                'string',
                'min:5',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
            ],
        ], [
            'email.unique' => 'Questa email è già stata utilizzata.',
            'phone.unique' => 'Questo numero è già stato utilizzato.',
            'password.min' => 'La password deve contenere almeno 5 caratteri.',
            'password.regex' => [
                'regex:/[0-9]/[@$!%*#?&]/' => 'La password deve contenere almeno un numero da (0-9) e almeno un carattere speciale. es.(@$!%*#?&)',
                // 'regex:/[@$!%*#?&]/' => 'La password deve contenere almeno un carattere speciale. es.(@$!%*#?&)'
            ],
        ])->validateWithBag('register');

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'],
            'password' => Hash::make($input['password']),
        ]);
    }
}