<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUser(Request $request)
    {
        try {
            $uid = $request['user_data']->id;
            $data = User::find($uid);

            return ResponseHelper::baseResponse("Berhasil mendapatkan data", 200, $data);
        } catch (Exception $err) {
            return ResponseHelper::err($err->getMessage());
        }
    }
}
