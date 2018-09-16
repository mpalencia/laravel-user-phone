<?php 

namespace App\Repositories;

use \App\Repositories\Interfaces\UserInterface;
use \App\Models\User;
use \App\Models\Client;

class UserRepository implements UserInterface
{
    /** @var \App\Repositories\Interfaces\UserInterface */
    protected $user;

    /**
     * UserRepository constructor
     *
     * @param App\Repositories\Interfaces\UserInterface $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Save data into database
     *
     * @param array $data
     * @return array
     */
    public function create(array $data)
    {
        $user = $this->user->create([
            'name'       => $data['name'],
            'email'       => $data['email'],
            'role'          => $data['role'],
            'password'  => bcrypt($data['password']),
            'api_token' => bcrypt($data['email'])
        ]);

        return $user;
    }

    /**
     * Update data in database
     *
     * @param array $data
     * @param int $id
     * @return array
     */
    public function update(array $data, $id)
    {
        $user = $this->user->find($id);
        if(isset($data['password'])) {
            $data['password'] =  bcrypt($data['password']);
        }
        $user->update($data);

        return $user;
    }

    /**
     * Display data from database
     *
     * @param int $id
     * @return array
     */
    public function show($id)
    {
        return $this->user->find($id);
    }

    /**
     * Delete data from database
     *
     * @param int $id
     * @return array
     */
    public function delete($id)
    {
        return $this->user->destroy($id);
    }

    /**
     * Retrieve user id from database via api_token
     *
     * @param string $api_token
     * @return array
     */
    public function getTokenUserId($api_token)
    {
        $query = $this->user->where('api_token', $api_token)->select('id')->first();
        return $query['id'];
    }

    /**
     * Retrieve user details from database via api_token
     *
     * @param string $api_token
     * @return array
     */
    public function getTokenUserDetails($api_token)
    {
        $query = $this->user->where('api_token', $api_token)->first();
        return $query;
    }

    /**
     * Get the associated model
     *
     * @return array
     */
    public function getModel()
    {
        return $this->user;
    }

    // Check if token belong to user
    // public function tokenBelongsToUser($api_token, $user_id)
    // {
    //     $response = true;
    //     $user = $this->user->find($user_id);
    //     if($user->api_token != $api_token) {
    //         $user = User::where('api_token', $api_token)->select('role')->first();
    //         if($user["role"] != 'admin') {
    //             $response = false;
    //         }
    //     }

    //     return $response;
    // }

    // Check if token is admin type
    // public function tokenIsAdmin($api_token)
    // {
    //     $user = User::where('api_token', $api_token)->select('role')->first();

    //     $response = true;

    //     if($user["role"] == null){
    //         $response = "";
    //     } else {
    //         if(strcmp($user["role"],'admin') != 0) {
    //             $response = false;
    //         }
    //     }

    //     return $response;
    // }

    // // Check if token is authorized client
    // public function tokenIsAuthClient($api_token)
    // {
    //     $response = true;

    //     $client = Client::where('api_token', $api_token)->select('authorize')->first();
    //     if(empty($client["authorize"])){
    //         $response = false;
    //     }

    //     return $response;
    // }

}