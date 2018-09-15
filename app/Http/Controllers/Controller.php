<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use \App\Repositories\Interfaces\UserInterface;
use App\Repositories\Interfaces\PhoneInterface;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	
	private $user;
    /** 
    * @var \App\Repositories\Interfaces\PhoneInterface 
    */
    protected $phone;

    /**
     * PhoneController constructor.
     *
     * @param App\Repositories\Interfaces\PhoneInterface $phone
     */
    public function __construct(UserInterface $user, PhoneInterface $phone )
    {
    	$this->user = $user;
        $this->phone = $phone;
    }


    /**
     * Custom validation of api_token
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authorizePhoneToken($method, $request)
    {
    	
    	if($method == 'store') {

    		$user = $this->user->getTokenUserDetails($request['api_token']);

    		if(empty($user)){
    			return response()->json(['error'=>'Invalid api_token'], 404);
    		}

    	}

    }
}
