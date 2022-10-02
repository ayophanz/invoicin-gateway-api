<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Traits\ApiResponser;
use App\Services\OrganizationService;
use App\Http\Requests\Register\PartialRequest;
use Auth;
use Image;

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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\PartialRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PartialRequest $request)
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
                $user->update([
                    'organization_id' => $content['data']['uuid']
                ]);

                return response()->json([
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => auth()->factory()->getTTL() * 60
                ]);
                \DB::commit();
            }
        } catch(\Exception $e) {
            \DB::rollback();
            return $this->errorResponse(['Error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->errorResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Validate partial registration request.
     *
     * @param  \Illuminate\Http\PartialRequest $request
     * @return \Illuminate\Http\Response
     */
    public function formValidate(PartialRequest $request)
    {
        return response()->json(['success' => true]);
    }
}
