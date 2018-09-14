<?php 

namespace App\Repositories;

use \App\Repositories\Interfaces\ClientInterface;
use \App\Models\Client;

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
            $response = false;
        }

        return $response;
    }

    // Check if token is admin type
    public function tokenIsAdmin($api_token, $client_id)
    {
        $response = true;
        $client = $this->model->find($client_id);
        if($client->api_token != $api_token) {
            $response = false;
        }

        return $response;
    }


}