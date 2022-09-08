<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\LoginSecurity;
use Google2FA;
use Hash;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
       //
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string'
        ]);

        $credentials = $request->only(['email', 'password']);

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status'  => 'Error',
                'message' => 'Unauthorized'
            ], 401);
        }

        return $this->tokenCreate($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $isTwofa = false;
        $user = auth()->user();
        // if ($twofa = $user::find($user->id)) {
        //     if (!is_null($twofa->twofa_secret)) {
        //         $isTwofa = true; 
        //     }
        // }
        return response()->json([
            'me' => $user,
            'is_twofa' => $isTwofa,
        ]);
    }

    /**
     * Generate 2fa secret key
     */
    public function generate2faSecret(Request $request){
        $user = Auth::user();

        $login_security = LoginSecurity::firstOrNew(array('user_id' => $user->id));
        $login_security->user_id = $user->id;
        $login_security->google2fa_enable = 0;
        $login_security->google2fa_secret = Google2FA::generateSecretKey();
        $login_security->save();

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Genearte 2f QR Code
     */
    public function generateTwofaQRcode()
    {
        $user       = Auth::user();
        $QRImageUrl = '';
        $secret     = '';

        if($user->loginSecurity()->exists()){
            $secret_key = $user->loginSecurity->google2fa_secret;
            $QRImageUrl = Google2FA::getQRCodeInline(
                config('app.name'),
                $user->email,
                $user->loginSecurity->google2fa_secret
            );
        }

        return response()->json([
            'qr_image_url' => $QRImageUrl,
            'secret' => $secret,
        ]);
    }  

    /**
     * Enable 2fa
     */
    public function enable2fa(Request $request){
        $user = Auth::user();

        \Log::debug($request->all());
        $toValidate = [
            'optCode' => 'required',
        ];
        $validator = Validator::make($request->all(), $toValidate);
        if ($validator->fails()) return $this->errorResponse($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);

        $optCode = $request->input('optCode');
        $valid = Google2FA::verifyKey($user->loginSecurity->google2fa_secret, $optCode);

        if($valid){
            $user->loginSecurity->google2fa_enable = 1;
            $user->loginSecurity->save();
            return response()->json([
                'success' => true, 
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification Code, Please try again.'
            ]);
        }
    }
    
    /**
     * Disable 2fa
     */
    public function disable2fa(Request $request){
        if (!(Hash::check($request->get('current_password'), Auth::user()->password))) {
            return response()->json([
                'success' => false,
                'message' => 'Your password does not matches with your account password. Please try again.'
            ]);
        }

        $toValidate = [
            'current_password' => 'required',
        ];
        $validator = Validator::make($request->all(), $toValidate);
        if ($validator->fails()) return $this->errorResponse($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);

        $user = Auth::user();
        $user->loginSecurity->google2fa_enable = 0;
        $user->loginSecurity->save();
        return response()->json([
            'success' => true,
            'message' => '2FA is now disabled.'
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->tokenCreate(auth()->refresh(true, true));
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function tokenCreate($token)
    {
        return response()->json([
            'token'      => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 1,
            'user_id'    => auth()->id(),
        ]);
    }
}