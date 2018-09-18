<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use \App\Models\User;

class UserTest extends TestCase
{
    /** @var stores up the created user id for deletion on teardown */
    private $createdUserId;

    /** @var stores up admin details */
    private $createdAdminId;
    private $adminToken;

    /** @var stores up non-admin details */
    private $createdNonAdminId;
    private $nonAdminToken;

    public function setUp()
    {
        parent::setUp();

        $this->userRepo = new \App\Repositories\UserRepository(new User);

        //create admin type user (for token use)
        $adminData = [
            'name' => 'User Admin',
            'email' =>  'admin@mail.com',
            'password' => 'secret!',
            'role' => 'admin'
        ];
        $admin =  $this->userRepo->create($adminData);

        $this->createdAdminId = $admin->id;
        $this->adminToken = $admin->api_token;

        //create non-admin type user (for token use)
        $nonAdminData = [
            'name' => 'User Non Admin',
            'email' =>  'non-admin@mail.com',
            'password' => 'secret!',
            'role' => 'non-admin'
        ];
        $nonAdmin =  $this->userRepo->create($nonAdminData);

        $this->createdNonAdminId = $nonAdmin->id;
        $this->nonAdminToken = $nonAdmin->api_token;

    }

    /** @test */
    public function create_user_successfully(): void
    {
       //create user
        $data = [
            'name' => 'User Tester',
            'email' =>  'create@mail.com',
            'password' => 'secret!',
            'role' => 'admin',
            'api_token' =>  $this->adminToken
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('POST', '/api/user-create', $data);

        //delete created users
        $content = $response->decodeResponseJson();
        $this->userRepo->delete($content['data']['id']);

        $response->assertStatus(201)
                        ->assertJson([
                            "data" => [
                                "name"=> "User Tester",
                                "email"=> "create@mail.com",
                                "role"=> "admin",
                            ]
                        ]);
    }

    /** @test */
    public function create_admin_role_from_non_admin_user()
    {      
        $data = [
            'name' => 'User Tester',
            'email' =>  'create-admin@mail.com',
            'password' => 'secret!',
            'role' => 'admin',
            'api_token' => $this->nonAdminToken 
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('POST', '/api/user-create', $data);

        $response->assertStatus(403)
                        ->assertJson([
                            "message" => "Unauthorized user.",
                            "error" => [
                                "api_token" => [
                                    "Invalid token."
                                ]
                            ]
                        ]);
    }

    /** @test */
    public function update_user_successfully(): void
    {
        $updateData = [
            'name' => 'User Update',
            'email' =>  'update@mail.com',
            'role' => 'admin',
            'api_token' =>  $this->adminToken
        ];

        $responseUpdate = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('PUT', '/api/user-update/'.$this->createdAdminId.'', $updateData);

        $responseUpdate->assertStatus(201)
                                ->assertJson([
                                    "data" => [
                                        "name"=> "User Update",
                                        "email"=> "update@mail.com",
                                        "role"=> "admin",
                                    ]
                                ]);
    }

    /** @test */
    public function update_user_successfully_using_admin_account(): void
    {
        $updateData = [
            'name' => 'User Update',
            'email' =>  'update@mail.com',
            'role' => 'admin',
            'api_token' =>  $this->adminToken
        ];

        $responseUpdate = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('PUT', '/api/user-update/'.$this->createdNonAdminId.'', $updateData);

        $responseUpdate->assertStatus(201)
                                ->assertJson([
                                    "data" => [
                                        "name"=> "User Update",
                                        "email"=> "update@mail.com",
                                        "role"=> "admin",
                                    ]
                                ]);
    }

    /** @test */
    public function update_user_using_other_users_account(): void
    {
        $updateData = [
            'name' => 'User Update',
            'email' =>  'update@mail.com',
            'role' => 'admin',
            'api_token' =>  $this->nonAdminToken 
        ];

        $responseUpdate = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('PUT', '/api/user-update/'.$this->createdAdminId.'', $updateData);

        $responseUpdate->assertStatus(403)
                                ->assertJson([
                                    "message" => "Unauthorized user.",
                                    "error"=> [
                                        "api_token" =>[
                                            "Invalid role."
                                        ]
                                    ]
                                ]);
    }

    /** @test */
    public function show_user_successfully(): void
    {
        $showData = [
            'api_token' => $this->adminToken
        ];

        $responseShow = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('GET', '/api/user/'.$this->createdAdminId.'', $showData);

        $responseShow->assertStatus(201)
                                ->assertJson([
                                    "data" => [
                                        "name" => "User Admin",
                                        "email" => "admin@mail.com",
                                        "role" => "admin"
                                    ]
                                ]);
    }

    /** @test */
    public function show_user_successfully_using_admin_account(): void
    {
        $showData = [
            'api_token' => $this->adminToken
        ];

        $responseShow = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('GET', '/api/user/'.$this->createdNonAdminId.'', $showData);

        $responseShow->assertStatus(201)
                                ->assertJson([
                                    "data" => [
                                        "name" => "User Non Admin",
                                        "email" => "non-admin@mail.com",
                                        "role" => "non-admin"
                                    ]
                                ]);
    }

    /** @test */
    public function show_user_using_other_users_account(): void
    {
        $showData = [
            'api_token' => $this->nonAdminToken
        ];

        $responseShow = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('GET', '/api/user/'.$this->createdAdminId.'', $showData);

        $responseShow->assertStatus(403)
                                ->assertJson([
                                    "message" => "Unauthorized user.",
                                    "error" => [
                                        "api_token" => [
                                            "Invalid token."
                                        ]
                                    ]
                                ]);
    }

    /** @test */
    public function delete_user_successfully(): void
    {
        $deleteData = [
            'api_token' => $this->nonAdminToken
        ];

        $responseDelete = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('DELETE', '/api/user-delete/'.$this->createdNonAdminId.'', $deleteData);

        $responseDelete->assertStatus(200)
                                ->assertJson([
                                    "message" => "User successfully deleted"
                                ]);
    }

    /** @test */
    public function delete_user_successfully_using_admin_account(): void
    {
        $deleteData = [
            'api_token' => $this->adminToken
        ];

        $responseDelete = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('DELETE', '/api/user-delete/'.$this->createdNonAdminId.'', $deleteData);

        $responseDelete->assertStatus(200)
                                ->assertJson([
                                    "message" => "User successfully deleted"
                                ]);
    }

    /** @test */
    public function delete_user_using_other_users_account(): void
    {
        $deleteData = [
            'api_token' => $this->nonAdminToken
        ];

        $responseDelete = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('DELETE', '/api/user-delete/'.$this->createdAdminId.'', $deleteData);

        $responseDelete->assertStatus(403)
                                ->assertJson([
                                    "message" => "Unauthorized user.",
                                    "error" => [
                                        "api_token" => [
                                            "Invalid token."
                                        ]
                                    ]
                                ]);
    }

    public function tearDown()
    {
        $this->userRepo->delete($this->createdAdminId);
        $this->userRepo->delete($this->createdNonAdminId); 
    }

}
