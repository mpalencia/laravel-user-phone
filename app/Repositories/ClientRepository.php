<?php 

namespace App\Repositories;

use \App\Repositories\Interfaces\ClientInterface;
use \App\Models\Client;
use \App\Models\User;

class ClientRepository implements ClientInterface
{
    // model property on class instances
    protected $model;

    // Constructor to bind model to repo
    public function __construct(Client $client)
    {
        $this->model = $client;
    }

    // create a new record in the database
    public function create(array $data)
    {
        $client = $this->model->create([
            'name'       => $data['name'],
            'email'       => $data['email'],
            'password'  => bcrypt($data['password']),
            'api_token' => bcrypt($data['email'])
        ]);

        return $client;
    }

    // update record in the database
    public function update(array $data, $id)
    {
        $client = $this->model->find($id);
        $client->update($data);

        return $client;
    }

    // remove record from the database
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    // Show user details
    public function show($id)
    {
        return $this->model->find($id);
    }

    
    // get details of client via token
    public function getTokenClientDetails($api_token)
    {
        $query = $this->model->where('api_token', $api_token)->first();
        return $query;
    }

    // Get the associated model
    public function getModel()
    {
        return $this->model;
    }

    // Check if token belong to user
    public function tokenBelongsToClient($api_token, $client_id)
    {
        $response = true;
        $client = $this->model->find($client_id);
        if($client->api_token != $api_token) {
            $user = User::where('api_token', $api_token)->select('role')->first();
            if($user["role"] != 'admin') {
                $response = false;
            }
        }

        return $response;
    }

    // Check if token is admin type
    public function tokenIsAdmin($api_token)
    {
        $user = User::where('api_token', $api_token)->select('role')->first();

        $response = array('status' => true);

        if(empty($user)) {
            $response = array('status' => false, 'message' => 'Invalid token');
        } else {
            if($user["role"] != 'admin') {
                $response = array('status' => false, 'message' => 'Unauthorized User');
            }
        }

        return $response;
    }


}