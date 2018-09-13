<?php 

namespace App\Repositories;

use \App\Repositories\Interfaces\UserInterface;
use \App\Models\User;

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
    public function tokenBelongsToUser($api_token, $user_id)
    {
        $response = true;
        $user = $this->model->find($user_id);
        if($user->api_token != $api_token) {
            $response = false;
        }

        return $response;
    }

    // Check if token is admin type
    public function tokenIsAdmin($api_token, $user_id)
    {
        $response = true;
        $user = $this->model->find($user_id);
        if($user->api_token != $api_token) {
            $response = false;
        }

        return $response;
    }


}