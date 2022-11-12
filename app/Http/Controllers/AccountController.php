<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Organization;
use App\Traits\ApiResponser;
use App\Services\OrganizationService;
use App\Http\Requests\Account\StoreRequest;
use App\Http\Requests\Account\UpdateRequest;
use App\Events\RegisteredEvent;
use Auth;
use Image;
use Carbon\Carbon;
use Hashids\Hashids;
use Redirect;

class AccountController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Validate user registration request.
     *
     * @param  \Illuminate\Http\StoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function userValidate(StoreRequest $request)
    {
        return response()->json(['success' => true]);
    }

    /**
     * Validate org registration request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function orgValidate(Request $request)
    {
        return $this->organizationService->validateOrganization($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\StoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        if (Auth::check()) Auth::logout();
        \DB::beginTransaction();
        try {
            /** Create account */
            $user             = new User();
            $user->first_name = $request->firstname;
            $user->last_name  = $request->lastname;
            $user->email      = $request->email;
            $user->password   = bcrypt($request->password);
            $user->save();
            \DB::commit();

            if (count($request->image) > 0) {
                $profile = 'profile.jpg';
                $path = storage_path() . '/app/files/user_' . $user->id. '/profile/';
                \File::isDirectory($path) or \File::makeDirectory($path, 0777, true, true);
                Image::make($request->image[0])->save($path . $profile);
            }

            $credentials = request(['email', 'password']);
            if ($token = auth()->attempt($credentials)) {

                /** Save the organization */
                $request->headers->set('Authorization', 'Bearer ' . $token);
                $payload      = $this->organizationService->storeOrganization($request);
                $content      = json_decode($payload->getContent(), true);
                $organization = new Organization([], collect($content['data'])->only(['uuid'])->toArray());
                
                $user->organization_id = $organization->uuid;
                $user->save();

                RegisteredEvent::dispatch($user);
                \DB::commit();
            }
        } catch(\Exception $e) {
            \DB::rollback();
            return $this->errorResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->successResponse(['status' => 'Success'], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $user = auth()->user();
        $payload      = $this->organizationService->fetchOrganization($request);
        $organization = json_decode($payload->getContent(), true);
        $user->organization_name = $organization['data']['name'];
        $user->organization_email = $organization['data']['email'];
        $user->organization_email_verified_at = $organization['data']['email_verified_at'];
        
        return response()->json([
            'me' => $user,
        ]);
    }

    public function verifyUserLink($token)
    {
        return view('verifyUser', ['token' => $token]);
    }

    public function verifyUser($token)
    {
        $hashids   = new Hashids('secretkey', 12);
        $decodedID = $hashids->decode($token)[0];

        $user = User::find($decodedID);
        if ($user->email_verified_at != null) {
            return view('verifyUser', ['success' => true, 'message' => 'Your account is already verified!']);
        }

        $user->email_verified_at = Carbon::now();
        $user->save();

        return view('verifyUser', ['success' => true, 'message' => 'Your account is successfully verified!']);
    }

    public function verifyOrganizationLink($token)
    {
      return view('verifyOrganization', ['token' => $token]);
    }

    public function verifyOrganization(Request $request, $token)
    {
        $hashids   = new Hashids('secretkey', 12);
        $decodedID = hex2bin($hashids->decodeHex($token));
        $request->merge(['id' => $decodedID]);
        $payload = $this->organizationService->verifyOrganization($request);
        $content = json_decode($payload->getContent(), true);
        $status = $content['data']['status'];

        return view('verifyOrganization', ['success' => true, 'message' => $status]);
    }

    public function updateProfile(Request $request, User $user)
    {
        $user->first_name = $request->firstname;
        $user->last_name = $request->lastname;
        $user->email = $request->email;
        $user->save();        

        if (count($request->image) > 0) {
            $profile = 'profile.jpg';
            $path = storage_path() . '/app/files/user_' . $user->id. '/profile/';
            \File::isDirectory($path) or \File::makeDirectory($path, 0777, true, true);
            Image::make($request->image[0])->save($path . $profile);
        }
        return $this->successResponse(['status' => 'Success'], Response::HTTP_OK);
    }
}
