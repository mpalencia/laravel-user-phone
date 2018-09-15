<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PhoneRequest ;
use App\Transformers\PhoneTransformer;
use App\Repositories\Interfaces\PhoneInterface;
use Illuminate\Http\JsonResponse;
use Validator;

class PhoneController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PhoneRequest $request): JsonResponse
    {
        $verify = $this->authorizePhoneToken('store', $request);


        try{
            // create record and pass in only fields that are fillable
            $phone = $this->phone->create($request->only($this->phone ->getModel()->fillable));
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PhoneRequest $request, $id):  JsonResponse
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
            'message' => "User's phone number is deleted"
        ]);
    }
}
