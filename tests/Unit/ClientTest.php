<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use \App\Models\Client;

class ClientTest extends TestCase
{
    /** @var stores up the created client id for deletion on teardown */
    private $createdClientId;

    public function setUp()
    {
        parent::setUp();
        
        $this->clientRepo = new \App\Repositories\ClientRepository(new Client);
    }

    /**
     * Test create function
     *
     * @return void
     */
    public function testCanCreateClient(): void
    {
        $data = [
            'name' => 'Matthew Palencia',
            'email' =>  'matthew.palencia@mail.com',
            'password' => 'secret!',
            'authorize' => 0
        ];

        $client = $this->clientRepo->create($data);
        $this->createdClientId = $client->id;
        $found = $this->clientRepo->show($client->id);

        $this->assertInstanceOf(Client::class, $client);
        $this->assertEquals($data['name'], $found->name);
        $this->assertEquals($data['email'], $found->email);
        $this->assertEquals($data['authorize'], $found->authorize);
    }

    /**
     * Test update function
     *
     * @return void
     */
    public function testCanUpdateClient(): void
    {
        $clientFactory = factory(Client::class)->create();

        $data = [
            'name' => 'John Doe',
            'email' =>  'john.doe@mail.com',
            'authorize'  => 1
        ];

        $this->createdClientId = $clientFactory->id;
        $updated = $this->clientRepo->update($data, $clientFactory->id);
        $found = $this->clientRepo->show($clientFactory->id);

        $this->assertEquals($data['name'], $found->name);
        $this->assertEquals($data['email'], $found->email); 
        $this->assertEquals($data['authorize'], $found->authorize);
    }

    /**
     * Test show function
     *
     * @return void
     */
    public function testCanShowClient(): void
    {
        $clientFactory = factory(Client::class)->create();

        $this->createdClientId = $clientFactory->id;
        $client = $this->clientRepo->show($clientFactory->id);

        $this->assertInstanceOf(Client::class, $client);
        $this->assertEquals($clientFactory->name, $client->name);
        $this->assertEquals($clientFactory->email, $client->email);
        $this->assertEquals($clientFactory->authorize, $client->authorize);
    }

    /**
     * Test delete function
     *
     * @return void
     */
    public function testCanDeleteClient(): void
    {
        $clientFactory = factory(Client::class)->create();

        $client = $this->clientRepo->delete($clientFactory->id);

        $this->assertEquals(1, $client);
    }

    /**
     * Test function to get client details using api_token
     *
     * @return void
     */
    public function testCanGetClientDetailsByToken(): void
    {
        $clientFactory = factory(Client::class)->create();

        $this->createdClientId = $clientFactory->id;
        $client = $this->clientRepo->getTokenClientDetails($clientFactory->api_token);

        $this->assertInstanceOf(Client::class, $client);
        $this->assertEquals($clientFactory->name, $client->name);
        $this->assertEquals($clientFactory->email, $client->email);
        $this->assertEquals($clientFactory->authorize, $client->authorize);
    }

    public function tearDown()
    {
        //delete test data in `clients` table
        $this->clientRepo->delete($this->createdClientId);
    }

}
