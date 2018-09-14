<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Transformers\ClientTransformer;
use App\Repositories\Interfaces\ClientInterface;
use Validator;

class ClientController extends Controller 
{

    /** 
    * @var \App\Repositories\ClientInterface 
    */
    private $client;

    /**
     * ClientController constructor.
     *
     * @param App\Repositories\ClientInterface $client
     */
    public function __construct( ClientInterface $client )
    {
        $this->client = $client;
    }

    /**
     * Create new client
     *
     * @param \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [ 
            'name'       => 'required',
            'email'       => 'required|email|unique:clients',
            'password'  => 'required|min:6',
        ]);

        if ($validator->fails()) { 
            // return error response if validation failed
            return response()->json(['error'=>$validator->errors()], 401);         
        }

        try{
            // create record and pass in only fields that are fillable
            $client = $this->client ->create($request->only($this->client ->getModel()->fillable));
        } catch (\Exception $e) {
            // return error response if something goes wrong
            return response()->json(['error'=>$e->getMessage()], 422);
        }

        // prepare response
        $response = fractal()
                        ->item($client)
                        ->transformWith(new ClientTransformer)
                        ->addMeta([
                            'token' => $client->api_token,
                        ])
                        ->toArray();

        return response()->json($response, 201);
    }

    /**
     * Show client details
     *
     * @param \Illuminate\Http\Request  $request
     * @param string $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id): JsonResponse
    {
        $tokenCheck = $this->client->tokenBelongsToClient($request['api_token'], $id);
        if (!$tokenCheck) { 
            // return error response if token doesn't belong to client
            return response()->json(['error'=>'Unauthorized Viewing'], 401);         
        }        

        try{
            // get client details
            $client = $this->client->show($id);
        } catch (\Exception $e) {
            // return error response if something goes wrong
            return response()->json(['error'=>$e->getMessage()], 422);
        }

        // prepare response
        $response = fractal()
                        ->item($client)
                        ->transformWith(new ClientTransformer)
                        ->toArray();

        return response()->json($response, 201);
    }

    /**
     * Update client
     *
     * @param \Illuminate\Http\Request  $request
     * @param string $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $tokenCheck = $this->client->tokenBelongsToClient($request['api_token'], $id);
        if (!$tokenCheck) { 
            // return error response if token doesn't belong to client
            return response()->json(['error'=>'Unauthorized update'], 401);         
        }        

        try{
            // update record and pass in only fields that are fillable
            $client = $this->client->update($request->only($this->client ->getModel()->fillable), $id);
        } catch (\Exception $e) {
            // return error response if something goes wrong
            return response()->json(['error'=>$e->getMessage()], 422);
        }

        // prepare response
        $response = fractal()
                        ->item($client)
                        ->transformWith(new ClientTransformer)
                        ->toArray();

        return response()->json($response, 201);
    }

    /**
     * Delete client
     *
     * @param \Illuminate\Http\Request  $request
     * @param string $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $tokenCheck = $this->client->tokenIsAdmin($request['api_token'], $id);
        if (!$tokenCheck) { 
            // return error response if token doesn't belong to client
            return response()->json(['error'=>'Unauthorized update'], 401);         
        }

        try{
            // update record and pass in only fields that are fillable
            $client = $this->client->delete($id);
        } catch (\Exception $e) {
            // return error response if something goes wrong
            return response()->json(['error'=>$e->getMessage()], 422);
        }

        // return response
        return response()->json([
            'message' => 'Client deleted'
        ]);
    }

}