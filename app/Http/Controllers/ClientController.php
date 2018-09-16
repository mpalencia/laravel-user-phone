<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientCreate;
use App\Http\Requests\ClientUpdate;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Transformers\ClientTransformer;
use App\Repositories\Interfaces\ClientInterface;
use Validator;

class ClientController extends Controller 
{

    /** @var \App\Repositories\Interfaces\ClientInterface */
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ClientCreate $request): JsonResponse
    {
        try{
            // create record and pass in only fields that are fillable
            $client = $this->client ->create($request->only($this->client ->getModel()->fillable));
        } catch (\Exception $e) {
            // return error response if something goes wrong
            return response()->json(['error'=>$e->getMessage()], 422);
        }

        // prepare response
        $response = fractal($client, new ClientTransformer()) ->addMeta(['token' => $client->api_token])->toArray();

        return response()->json($response, 201);
    }

    /**
     * Show client details
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id): JsonResponse
    {
        try{
            // get client details
            $client = $this->client->show($id);
        } catch (\Exception $e) {
            // return error response if something goes wrong
            return response()->json(['error'=>$e->getMessage()], 422);
        }

        // prepare response
        $response = fractal($client, new ClientTransformer())->toArray();

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
    public function update(ClientUpdate $request, $id): JsonResponse
    {
        try{
            // update record and pass in only fields that are fillable
            $client = $this->client->update($request->only($this->client ->getModel()->fillable), $id);
        } catch (\Exception $e) {
            // return error response if something goes wrong
            return response()->json(['error'=>$e->getMessage()], 422);
        }

        // prepare response
        $response = fractal($client, new ClientTransformer())->toArray();

        return response()->json($response, 201);
    }

    /**
     * Delete client
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try{
            // update record and pass in only fields that are fillable
            $client = $this->client->delete($id);
        } catch (\Exception $e) {
            // return error response if something goes wrong
            return response()->json(['error'=>$e->getMessage()], 422);
        }

        // return response
        return response()->json([
            'message' => 'Client successfully deleted'
        ]);
    }

}