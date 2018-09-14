<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Transformers\UserTransformer;
use App\Repositories\Interfaces\UserInterface;
use Validator;

class UserController extends Controller 
{

    /** 
    * @var \App\Repositories\UserInterface 
    */
    private $user;

    /**
     * UserController constructor.
     *
     * @param App\Repositories\UserInterface $user
     */
    public function __construct( UserInterface $user )
    {
        $this->user = $user;
    }

    /**
     * Create new user
     *
     * @param \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [ 
            'name'       => 'required',
            'email'       => 'required|email|unique:users',
            'password'  => 'required|min:6',
        ]);

        if ($validator->fails()) { 
            // return error response if validation failed
            return response()->json(['error'=>$validator->errors()], 401);         
        }

        try{
            // create record and pass in only fields that are fillable
            $user = $this->user ->create($request->only($this->user ->getModel()->fillable));
        } catch (\Exception $e) {
            // return error response if something goes wrong
            return response()->json(['error'=>$e->getMessage()], 422);
        }

        //prepare response
        $response = fractal()
                        ->item($user)
                        ->transformWith(new UserTransformer)
                        ->addMeta([
                            'token' => $user->api_token,
                        ])
                        ->toArray();

        return response()->json($response, 201);
    }

    /**
     * Show user details
     *
     * @param \Illuminate\Http\Request  $request
     * @param string $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id): JsonResponse
    {
        $tokenCheck = $this->user->tokenBelongsToUser($request['api_token'], $id);
        if (!$tokenCheck) { 
            // return error response if token doesn't belong to user
            return response()->json(['error'=>'Unauthorized Viewing'], 401);         
        }        

        try{
            // get user details
            $user = $this->user->show($id);
        } catch (\Exception $e) {
            // return error response if something goes wrong
            return response()->json(['error'=>$e->getMessage()], 422);
        }

        // prepare response
        $response = fractal()
                        ->item($user)
                        ->transformWith(new UserTransformer)
                        ->toArray();

        return response()->json($response, 201);
    }

    /**
     * Create new user
     *
     * @param \Illuminate\Http\Request  $request
     * @param string $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $tokenCheck = $this->user->tokenBelongsToUser($request['api_token'], $id);
        if (!$tokenCheck) { 
            // return error response if token doesn't belong to user
            return response()->json(['error'=>'Unauthorized update'], 401);         
        }        

        try{
            // update record and pass in only fields that are fillable
            $user = $this->user->update($request->only($this->user ->getModel()->fillable), $id);
        } catch (\Exception $e) {
            // return error response if something goes wrong
            return response()->json(['error'=>$e->getMessage()], 422);
        }

        // prepare response
        $response = fractal()
                        ->item($user)
                        ->transformWith(new UserTransformer)
                        ->toArray();

        return response()->json($response, 201);
    }

    /**
     * Delete user
     *
     * @param \Illuminate\Http\Request  $request
     * @param string $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $tokenCheck = $this->user->tokenIsAdmin($request['api_token'], $id);
        if (!$tokenCheck) { 
            // return error response if token doesn't belong to user
            return response()->json(['error'=>'Unauthorized update'], 401);         
        }

        try{
            // update record and pass in only fields that are fillable
            $user = $this->user->delete($id);
        } catch (\Exception $e) {
            // return error response if something goes wrong
            return response()->json(['error'=>$e->getMessage()], 422);
        }

        // return response
        return response()->json([
            'message' => 'User deleted'
        ]);
    }

}