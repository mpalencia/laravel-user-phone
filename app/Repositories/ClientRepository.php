<?php 

namespace App\Repositories;

use \App\Repositories\Interfaces\ClientInterface;
use \App\Models\Client;
use \App\Models\User;

class ClientRepository implements ClientInterface
{
    /** @var \App\Repositories\Interfaces\ClientInterface */
    protected $client;

    /**
     * ClientRepository constructor
     *
     * @param App\Repositories\Interfaces\ClientInterface $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Save data into database
     *
     * @param array $data
     * @return array
     */
    public function create(array $data)
    {
        $client = $this->client->create([
            'name'       => $data['name'],
            'email'       => $data['email'],
            'authorize'  => $data['authorize'],
            'password'  => bcrypt($data['password']),
            'api_token' => bcrypt($data['email'])
        ]);

        return $client;
    }

    /**
     * Display data from database
     *
     * @param int $id
     * @return array
     */
    public function show($id)
    {
        return $this->client->find($id);
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
        $client = $this->client->find($id);
        $client->update($data);

        return $client;
    }

    /**
     * Delete data from database
     *
     * @param int $id
     * @return array
     */
    public function delete($id)
    {
        return $this->client->destroy($id);
    }

    /**
     * Retrieve client id from database via api_token
     *
     * @param string $api_token
     * @return array
     */
    public function getTokenClientDetails($api_token)
    {
        $query = $this->client->where('api_token', $api_token)->first();
        return $query;
    }

    /**
     * Get the associated model
     *
     * @return array
     */
    public function getModel()
    {
        return $this->client;
    }
}