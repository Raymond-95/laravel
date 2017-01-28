<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;

use Input, Validator, Auth, JWTAuth, Hash, Redirect;

use App\User;

class AccountController extends Controller
{
    public function apiPostSignUp() {

        $inputs = array(
            'email'      => Input::get('email'),
            'password'  => Input::get('email')
        );

        $rules = [
            'email' => 'required | unique:users,email',
            'password' => 'required | min:6',
        ];

        $validator = Validator::make($inputs, $rules);

        if ($validator->fails()) {
            return response()->api(['error' => 'validation failed', $validator]);
        } else {
            //To create the user account

            $newUser = new User;
            $newUser->email = trim(Input::get('email')); 
            $newUser->password = Hash::make(Input::get('email'));
            $newUser->name = trim(Input::get('name'));
            $newUser->phonenum = trim(Input::get('phonenum'));
            $newUser->profileUrl = trim(Input::get('profileUrl'));
            $newUser->imageUrl = trim(Input::get('imageUrl'));
            $newUser->save();
        }
        
        //redirect to login page after the suceed of account creation
        return response()->api(['message' => 'Signup successfully']);
    }

    public function verifyUserAcc(){

        $email = Input::get('email');

        $where = ['email' => $email];
        $user = User::where($where)->first();

        $check = 'true';

        if (!empty($user)){
            $check = 'true';
        }
        else{
            $check = 'false';
        }

        $response = [
            'check' => $check
        ];

        return response()->api($response);
    }

    public function apiPostLogin() {

        $field = filter_var(Input::get('email'), FILTER_VALIDATE_EMAIL) ? 'email':

        $rules = array(
            $field => 'required|exists:users,'.$field,
            'password' => 'required|min:6'
        );

        $messages = [
            'email.exists' => 'Username/Email not found!'
        ];

        $inputs = array(
            $field      => Input::get('email'),
            'password'  => Input::get('email')
        );

        try{
            if(! $token = JWTAuth::attempt($inputs)) {
                return response()
                    ->api([], 'Unauthorized', 'Access is denied due to invalid credentials testing.', 401)
                ;

            } else{
             Auth::attempt($inputs);
                return response()
                        ->api(compact('token')); 
            }

        } catch(JWTException $e) {
            return response()
                ->api([], 'Token Error', 'Could not create the token', 500)
            ;
        }
    }

    public function apiPostLogout() {
        Auth::logout();

        return response()->api();
    }

    public function apiGetAccount() {
       
        try {
            // if (! $user = JWTAuth::parseToken()->authenticate()) {
            //    abort(404,"User not found");
             $user = User::find(Auth::user()->id);

            // } else {
                $t = JWTAuth::fromUser($user);
                $data = [
                    'id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name,
                    'phonenum' => $user->phonenum,
                    'profileUrl' => $user->profileUrl,
                    'imageUrl' => $user->imageUrl,
                    'token' => $t
                ];

                return response()->api($data);
            // }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
                abort(401,"The token has expired");
                //return response()->json(['token_expired'], $e->getStatusCode());

            } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                abort(401,"The token is invalid");
                //return response()->json(['token_invalid'], $e->getStatusCode());

            } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
                abort(401,"The token has absent");
                //return response()->json(['token_absent'], $e->getStatusCode());

            }
    }

    public function apiGetUser(Request $request){

        $user = User::find($request->id);

        $data = [
                    'id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name,
                    'phonenum' => $user->phonenum,
                    'profileUrl' => $user->profileUrl,
                    'imageUrl' => $user->imageUrl
                ];

        return response()->api($data);
    }

    public function apiUpdateUser(Request $request){

        $user = User::find(Auth::user()->id);
        $user->email = trim(Input::get('email')); 
        $user->password = Hash::make(Input::get('email'));
        $user->name = trim(Input::get('name'));
        $user->phonenum = trim(Input::get('phonenum'));
        $user->profileUrl = trim(Input::get('profileUrl'));
        $user->imageUrl = trim(Input::get('imageUrl'));
        $user->save();

        $inputs = array(
            'email'      => Input::get('email'),
            'password'  => Input::get('email')
        );

        Auth::attempt($inputs);
                return response()
                        ->api(compact('token')); 
    }


    // public function verifyLicense(Request $request){

    //     return  Redirect::away('http://www.jpj.gov.my/web/guest/tarikh-luput-lesen-memandu')->withInputs(Input::all());

    // }

}
