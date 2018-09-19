<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use \App\Models\Client;
use \App\Models\User;

class ClientTest extends TestCase
{
    /** @var stores up client details */
    private $createdClientId;
    private $createdClientIdToken;

    public function setUp()
    {
        parent::setUp();

        //create client user (for token use)

        $this->clientRepo = new \App\Repositories\ClientRepository(new Client);

        $clientData = [
            'name' => 'Client User',
            'email' =>  'client@mail.com',
            'password' => 'secret!',
            'authorize' => 0
        ];
        $client =  $this->clientRepo->create($clientData);

        $this->createdClientId = $client->id;
        $this->createdClientToken = $client->api_token;


        //create admin type user (for token use)

        $this->userRepo = new \App\Repositories\UserRepository(new User);

        $adminData = [
            'name' => 'User Admin',
            'email' =>  'admin@mail.com',
            'password' => 'secret!',
            'role' => 'admin'
        ];
        $admin =  $this->userRepo->create($adminData);

        $this->createdAdminId = $admin->id;
        $this->adminToken = $admin->api_token;
    }

    /** @test */
    public function create_client_successfully(): void
    {
        //create user
        $clientData = [
            'name' => 'Create Client User',
            'email' =>  'create-client@mail.com',
            'password' => 'secret!',
            'authorize' => 0
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('POST', '/api/client-create', $clientData);

        //delete created users
        $content = $response->decodeResponseJson();

        $this->clientRepo->delete($content['data']['id']);

        $response->assertStatus(201)
                    ->assertJson([
                        "data" => [
                            "name"=> "Create Client User",
                            "email"=> "create-client@mail.com",
                            "authorize"=> 0,
                        ]
                    ]);
    }

    /** @test */
    public function update_client_successfully(): void
    {
        $updateData = [
            'name' => 'Client  Update',
            'email' =>  'client@mail.com',
            'authorize' => 0,
            'api_token' => $this->createdClientToken
        ];

        $responseUpdate = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('PUT', '/api/client-update/'.$this->createdClientId.'', $updateData);

        $responseUpdate->assertStatus(201)
                            ->assertJson([
                                "data" => [
                                    "name"=> "Client  Update",
                                    "email"=> "client@mail.com",
                                    "authorize"=> 0,
                                ]
                            ]);
    }

    /** @test */
    public function update_client_successfully_using_admin_account(): void
    {
        $updateData = [
            'name' => 'Client  Update',
            'email' =>  'client@mail.com',
            'authorize' => 0,
            'api_token' => $this->adminToken
        ];

        $responseUpdate = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('PUT', '/api/client-update/'.$this->createdClientId.'', $updateData);

        $responseUpdate->assertStatus(201)
                            ->assertJson([
                                "data" => [
                                    "name"=> "Client  Update",
                                    "email"=> "client@mail.com",
                                    "authorize"=> 0,
                                ]
                            ]);
    }

    /** @test */
    public function update_client_using_other_client_account(): void
    {
        $clientData = [
            'name' => 'Client User',
            'email' =>  'update-client@mail.com',
            'password' => 'secret!',
            'authorize' => 0
        ];
        $client =  $this->clientRepo->create($clientData);

        $updateData = [
            'name' => 'Client  Update',
            'email' =>  'client@mail.com',
            'authorize' => 0,
            'api_token' => $this->createdClientToken
        ];

        $responseUpdate = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('PUT', '/api/client-update/'.$client->id.'', $updateData);

        // delete created client
        $this->clientRepo->delete($client->id);

        $responseUpdate->assertStatus(403)
                            ->assertJson([
                                "message" => "Unauthorized client.",
                                "error"=> [
                                    "api_token" =>[
                                        "Invalid token."
                                    ]
                                ]
                            ]);
    }

    /** @test */
    public function show_client_successfully(): void
    {
        $showData = [
            'api_token' => $this->createdClientToken
        ];

        $responseShow = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('GET', '/api/client/'.$this->createdClientId.'', $showData);

        $responseShow->assertStatus(201)
                            ->assertJson([
                                "data" => [
                                    "name"=> "Client User",
                                    "email"=> "client@mail.com",
                                    "authorize"=> 0,
                                ]
                            ]);
    }

    /** @test */
    public function show_client_successfully_using_admin_account(): void
    {
        $showData = [
            'api_token' => $this->adminToken
        ];

        $responseShow = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('GET', '/api/client/'.$this->createdClientId.'', $showData);

        $responseShow->assertStatus(201)
                            ->assertJson([
                                "data" => [
                                    "name"=> "Client User",
                                    "email"=> "client@mail.com",
                                    "authorize"=> 0,
                                ]
                            ]);
    }

    /** @test */
    public function show_client_using_other_client_account(): void
    {
        $clientData = [
            'name' => 'Client User',
            'email' =>  'show-client@mail.com',
            'password' => 'secret!',
            'authorize' => 0
        ];

        $client =  $this->clientRepo->create($clientData);

        $showData = [
            'api_token' => $this->createdClientToken
        ];

        $responseShow = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('GET', '/api/client/'.$client->id.'', $showData);

        // delete created client
        $this->clientRepo->delete($client->id);

        $responseShow->assertStatus(403)
                            ->assertJson([
                                "message" => "Unauthorized client.",
                                "error"=> [
                                    "api_token" =>[
                                        "Invalid token."
                                    ]
                                ]
                            ]);
    }

    /** @test */
    public function delete_client_successfully(): void
    {
        $deleteData = [
            'api_token' => $this->createdClientToken
        ];

        $responseDelete = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('DELETE', '/api/client-delete/'.$this->createdClientId.'', $deleteData);

        $responseDelete->assertStatus(200)
                            ->assertJson([
                                "message" => "Client successfully deleted"
                            ]);
    }

    /** @test */
    public function delete_client_successfully_using_admin_account(): void
    {
        $deleteData = [
            'api_token' => $this->adminToken
        ];

        $responseDelete = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('DELETE', '/api/client-delete/'.$this->createdClientId.'', $deleteData);

        $responseDelete->assertStatus(200)
                            ->assertJson([
                                "message" => "Client successfully deleted"
                            ]);
    }

    /** @test */
    public function delete_client_using_other_client_account(): void
    {
        $clientData = [
            'name' => 'Client User',
            'email' =>  'delete@mail.com',
            'password' => 'secret!',
            'authorize' => 0
        ];
        $client =  $this->clientRepo->create($clientData);

        $deleteData = [
            'api_token' => $this->createdClientToken
        ];

        $responseDelete = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('DELETE', '/api/client-delete/'.$client->id.'', $deleteData);

        // delete created client
        $this->clientRepo->delete($client->id);

        $responseDelete->assertStatus(403)
                            ->assertJson([
                                "message" => "Unauthorized client.",
                                "error" => [
                                    "api_token" => [
                                        "Invalid token."
                                    ]
                                ]
                            ]);
    }

    public function tearDown()
    {
        $this->clientRepo->delete($this->createdClientId);
        $this->userRepo->delete($this->createdAdminId); 
    }

}
