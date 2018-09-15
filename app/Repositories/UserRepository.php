<?php 

namespace App\Repositories;

use \App\Repositories\Interfaces\UserInterface;
use \App\Models\User;
use \App\Models\Client;

class UserRepository implements UserInterface
{
    // model property on class instances
    protected $model;

    // Constructor to bind model to repo
    public function __construct(User $user)
    {
        $this->model = $user;
    }

    // create a new record in the database
    public function create(array $data)
    {
        $user = $this->model->create([
            'name'       => $data['name'],
            'email'       => $data['email'],
            'password'  => bcrypt($data['password']),
            'api_token' => bcrypt($data['email'])
        ]);

        return $user;
    }

    // update record in the database
    public function update(array $data, $id)
    {
        $user = $this->model->find($id);
        $user->update($data);

        return $user;
    }

    // Show user details
    public function show($id)
    {
        return $this->model->find($id);
    }

    // remove record from the database
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    // remove record from the database
    public function getTokenUserId($api_token)
    {
        $query = $this->model->where('api_token', $api_token)->select('id')->first();
        return $query['id'];
    }

    // Get the associated model
    public function getModel()
    {
        return $this->model;
    }

    // Check if token belong to user
    public function tokenBelongsToUser($api_token, $user_id)
    {
        $response = true;
        $user = $this->model->find($user_id);
        if($user->api_token != $api_token) {
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

        $response = true;

        if($user["role"] == null){
            $response = "";
        } else {
            if(strcmp($user["role"],'admin') != 0) {
                $response = false;
            }
        }

        return $response;
    }

    // Check if token is authorized client
    public function tokenIsAuthClient($api_token)
    {
        $response = true;

        $client = Client::where('api_token', $api_token)->select('authorize')->first();
        if(empty($client["authorize"])){
            $response = false;
        }

        return $response;
    }

}