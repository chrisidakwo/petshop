<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

class ResetPasswordController extends Controller
{
    public function showResetForm()
    {
        return 'Reset Password Form';
    }
}
