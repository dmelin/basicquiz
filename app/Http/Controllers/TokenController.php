<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Answer;

class TokenController extends Controller
{
    public function generateToken()
    {
        do {
            $token = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 5));
            $exists = Answer::where('user_token', $token)->exists();
        } while ($exists);

        return response()->json(['token' => $token]);
    }
}
