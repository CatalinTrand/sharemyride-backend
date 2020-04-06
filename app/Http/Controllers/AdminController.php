<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use JWTAuthException;

class AdminController extends Controller
{
    private function getToken($email, $password)
    {
        $token = null;
        //$credentials = $request->only('email', 'password');
        try
        {
            if (!$token = JWTAuth::attempt( ['email' => $email, 'password' => $password]))
            {
                return response()->json([
                    'response' => 'error',
                    'message'  => 'Password or email is invalid',
                    'token'    => $token
                ]);
            }
        }
        catch (JWTAuthException $e)
        {
            return response()->json([
                'response' => 'error',
                'message'  => 'Token creation failed',
            ]);
        }

        return $token;
    }

    public function new_user()
    {
        $payload = [
            "type"       => '1',
            "name"       => 'Cosmin',
            "surname"    => 'Paduraru',
            "password"   => Hash::make('cosmintest'),
            "email"      => 'cosminpaduraru1997@gmail.com',
            "cellulare"  => '0244325327',
            "telefono"   => '0725244094',
            "image"      => 'avatar.png',
            "address"    => 'Delea Veche 24',
            "city"       => 'Bucharest',
            "auth_token" => ''
        ];

        $user = new User($payload);

        if ($user->save())
        {
            $token = self::getToken('cosminpaduraru1997@gmail.com', 'cosmintest'); // generate user token

            if (!is_string($token))
                return response()->json(['success' => false, 'data' => 'Token generation failed'], 201);

            $user = User::where('email', $payload['email'])->get()->first();

            $user->auth_token = $token; // update user token

            $user->save();

            $response = [
                'success' => true,
                'data'    => [
                    'name'       => $user->name,
                    'id'         => $user->id,
                    'email'      => $payload['email'],
                    'auth_token' => $token
                ]
            ];
        }
        else
            $response = ['success' => false, 'data' => "Couldn't register user"];

        return response()->json($response, 201);
    }

    public function new_user2()
    {
        $payload = [
            "type"       => '2',
            "name"       => 'Cosmin',
            "surname"    => 'Paduraru',
            "password"   => Hash::make('cosmintest'),
            "email"      => 'cosminpaduraru1998@gmail.com',
            "cellulare"  => '0244325327',
            "telefono"   => '0725244094',
            "image"      => 'avatar.png',
            "address"    => 'Delea Veche 24',
            "city"       => 'Bucharest',
            "auth_token" => ''
        ];

        $user = new User($payload);

        if ($user->save())
        {
            $token = self::getToken('cosminpaduraru1997@gmail.com', 'cosmintest'); // generate user token

            if (!is_string($token))
                return response()->json(['success' => false, 'data' => 'Token generation failed'], 201);

            $user = User::where('email', $payload['email'])->get()->first();

            $user->auth_token = $token; // update user token

            $user->save();

            $response = [
                'success' => true,
                'data'    => [
                    'name'       => $user->name,
                    'id'         => $user->id,
                    'email'      => $payload['email'],
                    'auth_token' => $token
                ]
            ];
        }
        else
            $response = ['success' => false, 'data' => "Couldn't register user"];

        return response()->json($response, 201);
    }
}
