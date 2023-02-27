<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
Use Illuminate\Support\Facades\Validator;

class ApiAuthController extends BaseController
{
    // Create new user
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if($validator->fails()) {
            return $this->sendError('validation Error', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);

        $user = User::create($input);

        // Generate Auth Token
        $success['token'] =$user->createToken("AuthToken")->accessToken;
        $success['account'] = $user;

        return $this->sendResponse($success, 'Account Created Successfully!!!');
    }

    // Login for existing Users

    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->get('email')   , 'password' => $request->password]))
        {
            $user = Auth::user();
            $success['token'] = $user->createToken("AuthToken")->accessToken;
            $success['user'] = $user;

            return $this->sendResponse($success, 'You Logged in Successfully ');
        }
        
        else {
            return $this->sendError('UnAuthenticated', ['error' => 'UnAuthorized..']);

        }
        

        
    
    }
}
