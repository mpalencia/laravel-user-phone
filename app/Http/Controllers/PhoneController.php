<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PhoneCreate;
use App\Http\Requests\PhoneUpdate;
use App\Http\Requests\PhonePaginate;
use App\Transformers\PhoneTransformer;
use App\Repositories\Interfaces\PhoneInterface;
use Illuminate\Http\JsonResponse;

class PhoneController extends Controller
{
     /** @var \App\Repositories\Interfaces\PhoneInterface */
    protected $phone;

    /**
     * PhoneController constructor.
     *
     * @param App\Repositories\Interfaces\PhoneInterface $phone
     */
    public function __construct(PhoneInterface $phone)
    {
        $this->phone = $phone;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showAll(PhonePaginate $request, $user_id)
    {
        try{
            // get client details
            $phone = $this->phone->showAll($request->input(), $user_id);
        } catch (\Exception $e) {
            // return error response if something goes wrong
            return response()->json(['error'=>$e->getMessage()], 422);
        }

        // prepare response
        $response = fractal($phone, new PhoneTransformer())->toArray();

        return response()->json($response, 201);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param App\Http\Requests\PhoneCreate $request
     * @return Illuminate\Http\JsonResponse
     */
    public function store(PhoneCreate $request): JsonResponse
    {
        try{
            // create record and pass in only fields that are fillable
            $phone = $this->phone->create($request->only($this->phone->getModel()->fillable));
        } catch (\Exception $e) {
            // return error response if something goes wrong
            return response()->json(['error'=>$e->getMessage()], 422);
        }

        //prepare response
        $response = fractal($phone, new PhoneTransformer())->toArray();

        return response()->json($response, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            // get client details
            $phone = $this->phone->show($id);
        } catch (\Exception $e) {
            // return error response if something goes wrong
            return response()->json(['error'=>$e->getMessage()], 422);
        }

        // prepare response
        $response = fractal($phone, new PhoneTransformer())->toArray();

        return response()->json($response, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param App\Http\Requests\PhoneUpdate $request
     * @param int  $id
     * @return Illuminate\Http\JsonResponse
     */
    public function update(PhoneUpdate $request, $id):  JsonResponse
    {
        $phone = $this->phone->getDetails($id);
        if ($phone === null) {
            // return error response if $id does not exist
            return response()->json(['error'=>'User phone number id - '.$id.' not found' ], 404);
        }

        try{
            // update record and pass in only fields that are fillable
            $phone = $this->phone ->update($request->only($this->phone ->getModel()->fillable), $id);
        } catch (\Exception $e) {
            // return error response if something goes wrong
            return response()->json(['error'=>$e->getMessage()], 422);
        }

        //prepare response
        $response = fractal($phone, new PhoneTransformer())->toArray();

        return response()->json($response, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $phone = $this->phone->getDetails($id);
        if ($phone === null) {
            // return error response if $id does not exist
            return response()->json(['error'=>'User phone number id - '.$id.' not found' ], 404);
        }

        try{
            // update record and pass in only fields that are fillable
            $phone = $this->phone->delete($id);
        } catch (\Exception $e) {
            // return error response if something goes wrong
            return response()->json(['error'=>$e->getMessage()], 422);
        }

        // return response
        return response()->json([
            'message' => "User's phone number successfully deleted"
        ]);
    }
}
