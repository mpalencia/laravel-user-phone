<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Transformers\ClientTransformer;
use Validator;
use App\Client;

class ClientController extends Controller 
{

    /**
     * The client model instance.
     *
     * @var \App\Client
     */
    protected $client;

    /**
     * Create a client model instance.
     *
     * @param  \App\Client  $client
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Store a new client.
     *
     * @param \Illuminate\Http\Request  $request
     * @param \App\Client $client
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Client $client): JsonResponse
    {

        $validator = Validator::make($request->all(), [ 
            'name'       => 'required',
            'email'       => 'required|email|unique:clients',
            'password'  => 'required|min:6',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);         
        }

        $client = $client->create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => bcrypt($request->password),
            'api_token' => bcrypt($request->email)
        ]);

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
     * Update the given client.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $clientId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $clientId): JsonResponse
    {
        echo 89324;
        // $validator = Validator::make($request->all(), [
        //     'api_token' => 'required',
        //     'name'       => 'required',
        //     'email'       => 'required|email|unique:clients,email,'. $clientId,
        // ]);

        // if ($validator->fails()) { 
        //     return response()->json(['error'=>$validator->errors()], 401);         
        // }

        // return $this->clients->update(
        //     $client, $request->name, $request->email
        // );

    }

    /**
     * Delete the given client.
     *
     * @param  Request  $request
     * @param  string  $clientId
     *
     * @return Response
     */
    public function destroy(Request $request, $clientId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'api_token' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);         
        }

        $client = $this->clients->findForUser($clientId, $request->api_token);

        if (! $client) {
            return new Response('', 404);
        }

        $this->clients->delete(
            $client
        );
    }

}