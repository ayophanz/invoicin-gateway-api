<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\LoginSecurity;
use App\Models\User;
use App\Traits\ApiResponser;
use App\Jobs\ForgotPasswordJob;
use App\Services\OrganizationService;
use \ParagonIE\ConstantTime\Base32;
use Carbon\Carbon; 
use DB;
use Google2FA;
use Hash;
use Auth;
use Crypt;
use Session;
use Redirect;

class AuthController extends Controller
{
    use ApiResponser;
    protected $organizationService;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(OrganizationService $organizationService)
    {
        $this->organizationService = $organizationService;
    }

    /**
     * Check if authenticated
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function isAuth() 
    {
        $isAuth = false;
        if (Auth::check()) $isAuth = true;
        return response()->json([
            'isAuth' => $isAuth,
        ]);
    }


    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        if (Auth::check()) Auth::logout(); 

        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string'
        ]);

        $credentials = $request->only(['email', 'password']);

        $token = Auth::attempt($credentials);
        if (!$token) {
            return $this->errorResponse(['message' => 'Invalid password or email'], Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();
        if (Auth::check() && $user->loginSecurity && $user->loginSecurity->google2fa_enable) {
            Auth::logout();
            return response()->json([
                'user_id'            => $user->id,
                'otp_required'       => true,
                'otp_setup_required' => false
            ]);
        }

        if (Auth::check() && $user->loginSecurity == null) {
            return $this->tokenCreate($token, true);
        }

        return $this->tokenCreate($token);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function tokenCreate($token, $isOtpSetup = false)
    {
        return response()->json([
            'token'              => $token,
            'token_type'         => 'bearer',
            'expires_in'         => Auth::factory()->getTTL() * 1,
            'user_id'            => Auth::id(),
            'otp_setup_required' => $isOtpSetup,
        ]);
    }

    /**
     * Generate 2fa secret key
     */
    public function generate2faSecret()
    {
        $user                             = Auth::user();
        $secret                           = $this->generateSecret();
        $login_security                   = LoginSecurity::firstOrNew(array('user_id' => $user->id));
        $login_security->user_id          = $user->id;
        $login_security->google2fa_enable = 0;
        $login_security->google2fa_secret = Crypt::encrypt($secret);
        $login_security->save();

        Auth::logout();
        return response()->json([
            'user_id'            => $user->id,
            'otp_setup_required' => true,
            'otp_required'       => false
        ]);
    }

    /**
     * Generate a secret key in Base32 format
     *
     * @return string
     */
    private function generateSecret()
    {
        $randomBytes = random_bytes(10);

        return Base32::encodeUpper($randomBytes) ;
    }

    /**
     * Genearte 2f QR Code
     */
    public function generateTwofaQRcode(Request $request)
    {
        $userId     = $request->input('user_id');
        $user       = User::findOrFail($userId);
        $QRImageUrl = '';
        $secret     = '';

        if($user->loginSecurity()->exists()){
            $secret     = Crypt::decrypt($user->loginSecurity->google2fa_secret);
            $QRImageUrl = Google2FA::getQRCodeInline(
                $request->getHttpHost(),
                $user->email,
                $secret,
                200
            );
        }

        return response()->json([
            'qr_image_url' => $QRImageUrl,
            'secret'       => $secret,
        ]);
    }  

    /**
     * Enable 2fa
     */
    public function enable2fa(Request $request)
    {
        $toValidate = [
            'otp_code' => 'required',
        ];
        $validator = Validator::make($request->all(), $toValidate);
        if ($validator->fails()) return $this->errorResponse($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);

        if ($this->cacheOtp($request)) {
            $user  = User::findOrFail($request->input('user_id'));
            $token = Auth::login($user);
            $user->loginSecurity->google2fa_enable = 1;
            $user->loginSecurity->save();

            return $this->tokenCreate($token);
        }

        return $this->errorResponse(['otp_code' => ['OTP not valid']], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $toValidate = [
            'otp_code' => 'required',
        ];
        $validator = Validator::make($request->all(), $toValidate);
        if ($validator->fails()) return $this->errorResponse($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);

        if ($this->cacheOtp($request)) {
            $user  = User::findOrFail($request->input('user_id'));
            $token = Auth::login($user);

            return $this->tokenCreate($token);
        }

        return $this->errorResponse(['otp_code' => ['OTP not valid']], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Store otp in cache for blacklist
     */
    public function cacheOtp(Request $request)
    {
        $userId  = $request->input('user_id');
        $otpCode = $request->input('otp_code');
        $key     = $userId . ':' . $otpCode;

        $user   = User::findOrFail($userId);
        $secret = Crypt::decrypt($user->loginSecurity->google2fa_secret);
        $valid  = Google2FA::verifyKey($secret, $otpCode);

        if ($valid) {
            if (!Cache::has($key)) {
                Cache::add($key, true, 4); //use cache to store token to blacklist
                return true;
            }
        }

        return false;
    }

    /**
     * Disable 2fa
     */
    public function disable2fa(Request $request)
    {
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
    public function logout(Request $request)
    {   
        if (Auth::check()) Auth::logout();

        return $this->successResponse(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->tokenCreate(Auth::refresh(true, true));
    }

    /**
     * Forgot password
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(Request $request)
    {
        $toValidate = [
            'email' => 'required|email',
        ];
        $validator = Validator::make($request->all(), $toValidate);
        if ($validator->fails()) return $this->errorResponse($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);

        $user  = User::where('email', $request->email)->first();
        $token = Str::random(64);

        DB::table('password_resets')->where('email', $request->email)->delete();
        DB::table('password_resets')->insert([
            'email' => $request->email, 
            'token' => $token, 
            'created_at' => Carbon::now()
        ]);

        if (!$user) return $this->errorResponse(['email' => ['This email doesn\'t exist.']], Response::HTTP_UNAUTHORIZED);

        ForgotPasswordJob::dispatch($user, $token);

        return $this->successResponse(['message' => 'Reset link successfully']);
    }

    /**
     * Password Reset Link
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function passwordResetLink(Request $request, $token)
    {
        $passwordReset = DB::table('password_resets')
            ->where([
                'token' => $token
            ])->first();
        
        if (!$passwordReset) {
            return 'Invalid or Expired link';
        }

      return view('resetPassword', ['token' => $token]);
    }

    /**
     * Reset Password
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request, $token)
    {
        $toValidate = [
            'password' => 'required|confirmed|min:6',
        ];
        $validator = Validator::make($request->all(), $toValidate);
        if ($validator->fails()) return view('resetPassword', ['errors' => $validator->errors(), 'token' => $token]);

        $passwordReset = DB::table('password_resets')->where('token', $token)->first();
        if (!$passwordReset) return 'Invalid or Expired link';

        User::where('email', $passwordReset->email)->update([
            'password' => bcrypt($request->password)
        ]);
        DB::table('password_resets')->where('email', $passwordReset->email)->delete();

        return view('resetPasswordSuccess');
    }
}