<?php

namespace App\Http\Middleware;

use \App\Repositories\Interfaces\UserInterface;
use \App\Repositories\Interfaces\PhoneInterface;
use \App\Repositories\Interfaces\ClientInterface;
use Closure;

class VerifyApiToken
{
    private $user;
    private $phone;
    private $client;

    public function __construct(UserInterface $user, PhoneInterface $phone, ClientInterface $client)
    {
        $this->user = $user;
        $this->client = $client;
        $this->phone = $phone;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $api_token = $request->api_token;

        if (!$api_token) {
            return response()->json(['error'=>'api_token is required'], 401); 
        } 

        /*
        * Get user details using api_token
        */
        $user = $this->user->getTokenUserDetails($api_token);

        /*
        * Get client details using api_token
        */
        $client = $this->client->getTokenClientDetails($api_token);

        /*
        * If token is user 'admin' type, allow process
        */
        if($user['role'] == 'admin') {
            return $next($request);
        }

        /*
        * check token authorizations
        */
        $uri1 = \Request::segment(1);
        $uri2 = \Request::segment(2);
        $uri3 = \Request::segment(3);

        $module = str_replace(array("create", "delete", "update"), "", $uri2);

        /*
        * User authorization verification
        */
        if (strpos($module, 'user') !== false) {

            $userId = $uri3;

            $method = str_replace("user", "", $uri2);
            $method = str_replace("-", "", $method);

            switch ($method) {

                 /** create */
                case 'create':
                    if($user['role'] == 'non-admin') {
                        $error = ['message'=>'Unauthorized user.', 'error' => ['api_token' => ['Invalid token.']]];
                        return response()->json($error, 403);                        
                    } else if($client['authorize'] === 0) {
                        $error = ['message'=>'Unauthorized client.', 'error' => ['api_token' => ['Invalid token.']]];
                        return response()->json($error, 403);    
                    } else {
                        $error = ['message'=>'Token does not exist.', 'error' => ['api_token' => ['Invalid token.']]];
                        return response()->json($error, 404);  
                    }
                    if($request->role == 'admin' && !empty($client['id'])) {
                        $error = ['message'=>'Only admin type user can create admin role.', 'error' => ['api_token' => ['Invalid role.']]];
                        return response()->json($error, 403);  
                    }
                    break;

                /** update, delete, show */
                default:
                    if(empty($user)) {
                        $error = ['message'=>'Token does not exist.', 'error' => ['api_token' => ['Invalid token.']]];
                        return response()->json($error, 404);
                    }
                    $userDetails = $this->user->show($userId);
                    if($request->role == 'admin' && $user['role'] == 'non-admin') {
                        $error = ['message'=>'Only admin type user can create admin role.', 'error' => ['api_token' => ['Invalid role.']]];
                        return response()->json($error, 403);  
                    }
                    if($userDetails['id'] != $user['id']) {
                        $error = ['message'=>'Unauthorized user.', 'error' => ['api_token' => ['Invalid token.']]];
                        return response()->json($error, 403);
                    }
                    break;
            }

        /*
        * Client authorization verification
        */
        } else if (strpos($module, 'client') !== false) {
            

            $client = $this->client->getTokenClientDetails($api_token);

        /*
        * User Phone authorization verification
        */
        } else {

            $phoneId = $uri3;
            
            if(empty($user)){
                // return error response if token doen't belong to any user
                $error = ['message'=>'Token does not exist.', 'error' => ['api_token' => ['Invalid token.']]];
                return response()->json($error, 404);
            }

            $method = str_replace("phone", "", $uri2);
            $method = str_replace("-", "", $method);

            switch ($method) {
                case 'create':
                    /** create */
                    break;
                default:
                    /** update, delete, show */
                    $phoneDetails = $this->phone->getDetails($phoneId);
                    if($phoneDetails['user_id'] != $user['id']) {
                        // return error response if user is not authorized
                        $error = ['message'=>'Unauthorized process.', 'error' => ['api_token' => ['Invalid token.']]];
                        return response()->json($error, 403);
                    }
                    break;
            }
        }

        return $next($request);
    }
}
