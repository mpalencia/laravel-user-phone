<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use \App\Models\Client;
use \App\Models\User;
use \App\Models\UserPhone;

class PhoneTest extends TestCase
{
    /** @var stores up admin details */
    private $userRepo;
    private $createdAdminId;
    private $adminToken;

    /** @var stores up user phone */
    private $userPhoneRepo;
    private $createdUserPhoneId;

    public function setUp()
    {
        parent::setUp();

        //create admin type user (for token use)
        $this->userRepo = new \App\Repositories\UserRepository(new User);

        $adminData = [
            'name' => 'User Phone Admin',
            'email' =>  'admin-phone@mail.com',
            'password' => 'secret!',
            'role' => 'admin'
        ];
        $admin =  $this->userRepo->create($adminData);

        $this->createdAdminId = $admin->id;
        $this->adminToken = $admin->api_token;

        //create user phone
        $this->userPhoneRepo = new \App\Repositories\PhoneRepository(new UserPhone);

        $userPhoneData = [
            'api_token' => $admin->api_token,
            'user_id' => $admin->id,
            'phone_number' => '09174669444',
        ];
        $userPhone =  $this->userPhoneRepo->create($userPhoneData);

        $this->createdUserPhoneId = $userPhone->id;
    }

    /** @test */
    public function create_user_phone_successfully(): void
    {
        //create user phone
        $createData = [
            'api_token' => $this->adminToken,
            'phone_number' =>  '09174669111',
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('POST', '/api/phone-create', $createData);

        //delete created user phone
        $content = $response->decodeResponseJson();

        $this->userPhoneRepo->delete($content['data']['id']);

        $response->assertStatus(201)
                ->assertJson([
                    "data" => [
                        "user_id" => $this->createdAdminId,
                        "phone_number" => "09174669111"
                    ]
                ]);
    }

    /** @test */
    public function create_duplicate_user_phone(): void
    {
        //create user phone
        $createData = [
            'api_token' => $this->adminToken,
            'phone_number' =>  '09174669444',
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('POST', '/api/phone-create', $createData);

        $response->assertStatus(422)
                ->assertJson([
                        "message" => "The given data was invalid.",
                        "errors" => [
                            "phone_number" => [
                                "phone_number is already used"
                            ]
                        ]
                ]); 
    }

    /** @test */
    public function create_user_phone_doesnt_belong_to_philippines(): void
    {
        //create user phone
        $createData = [
            'api_token' => $this->adminToken,
            'phone_number' =>  '15417543010',
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('POST', '/api/phone-create', $createData);

        $response->assertStatus(422)
                ->assertJson([
                    "message" => "The given data was invalid.",
                    "errors" => [
                        "phone_number" => [
                            "The phone number field contains an invalid number. Please use a Philippine number."
                        ]
                    ]
                ]);
    }

    /** @test */
    public function update_user_phone_successfully(): void
    {
        $updateData = [
            'api_token' => $this->adminToken,
            'phone_number' =>  '09174669111',
        ];

        $responseUpdate = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('PUT', '/api/phone-update/'.$this->createdUserPhoneId.'', $updateData);

        $responseUpdate->assertStatus(201)
                        ->assertJson([
                            "data" => [
                                "user_id" => $this->createdAdminId,
                                "phone_number" => "09174669111"
                            ]
                        ]);
    }

    /** @test */
    public function update_user_phone_successfully_using_admin_account(): void
    {
        $updateData = [
            'api_token' => $this->adminToken,
            'phone_number' =>  '09174669111',
        ];

        $responseUpdate = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('PUT', '/api/phone-update/'.$this->createdUserPhoneId.'', $updateData);

        $responseUpdate->assertStatus(201)
                        ->assertJson([
                            "data" => [
                                "user_id" => $this->createdAdminId,
                                "phone_number" => "09174669111"
                            ]
                        ]);
    }

    /** @test */
    public function update_user_phone_using_other_user_phone_account(): void
    {
        $nonAdminData = [
            'name' => 'User Non Admin',
            'email' =>  'non-admin-phone@mail.com',
            'password' => 'secret!',
            'role' => 'non-admin'
        ];
        $nonAdmin =  $this->userRepo->create($nonAdminData);

        $updateData = [
            'api_token' => $nonAdmin->api_token,
            'phone_number' =>  '09174669111',
        ];

        $responseUpdate = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('PUT', '/api/phone-update/'.$this->createdUserPhoneId.'', $updateData);

        // delete created user
        $this->userRepo->delete($nonAdmin->id);

        $responseUpdate->assertStatus(403)
                        ->assertJson([
                            "message" => "Unauthorized process.",
                            "error"=> [
                                "api_token" =>[
                                    "Invalid token."
                                ]
                            ]
                        ]);
    }

    /** @test */
    public function show_user_phone_successfully(): void
    {
        $showData = [
            'api_token' => $this->adminToken
        ];

        $responseShow = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('GET', '/api/phone/'.$this->createdUserPhoneId.'', $showData);

        $responseShow->assertStatus(201)
                        ->assertJson([
                            "data" => [
                                "user_id" => $this->createdAdminId,
                                "phone_number" => "09174669444"
                            ]
                        ]);
    }

    /** @test */
    public function show_user_phone_successfully_using_admin_account(): void
    {
        $showData = [
            'api_token' => $this->adminToken
        ];

        $responseShow = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('GET', '/api/phone/'.$this->createdUserPhoneId.'', $showData);

        $responseShow->assertStatus(201)
                        ->assertJson([
                            "data" => [
                                "user_id" => $this->createdAdminId,
                                "phone_number" => "09174669444"
                            ]
                        ]);
    }

    /** @test */
    public function show_user_phone_using_other_user_phone_account(): void
    {
        $nonAdminData = [
            'name' => 'User Non Admin',
            'email' =>  'non-admin-phone@mail.com',
            'password' => 'secret!',
            'role' => 'non-admin'
        ];
        $nonAdmin =  $this->userRepo->create($nonAdminData);

        $showData = [
            'api_token' => $nonAdmin->api_token
        ];

        $responseShow = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('GET', '/api/phone/'.$this->createdUserPhoneId.'', $showData);

        // delete created user
        $this->userRepo->delete($nonAdmin->id);

        $responseShow->assertStatus(403)
                        ->assertJson([
                            "message" => "Unauthorized process.",
                            "error"=> [
                                "api_token" =>[
                                    "Invalid token."
                                ]
                            ]
                        ]);
    }

    /** @test */
    public function show_user_phone_10_reponse(): void
    {
        $created10phone = array();
        for ($i=0; $i < 11; $i++) { 
            $userPhoneData = [
                'api_token' => $this->adminToken,
                'phone_number' => '0917466943'.$i.'',
            ];
            $userPhone =  $this->userPhoneRepo->create($userPhoneData);

            $created10phone[$i] = $userPhone->id;
        }

        $showData = [
            'api_token' => $this->adminToken,
            'page' => 1
        ];

        $responseShow = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('GET', '/api/phones/'.$this->createdAdminId.'', $showData);

        //delete created user phones
        foreach ($created10phone as $key => $value) {
            $this->userPhoneRepo->delete($value);
        }

        $responseShow->assertStatus(201)
                ->assertJson([
                    "meta"=> [
                        "pagination" => [
                            "total" => 12,
                            "count" => 10,
                            "per_page" => 10,
                            "current_page" => 1,
                            "total_pages" =>2,
                        ]
                    ]
                ]);
    }

    /** @test */
    public function show_user_phone_10_reponse_page_2(): void
    {
        $created10phone = array();
        for ($i=0; $i < 11; $i++) { 
            $userPhoneData = [
                'api_token' => $this->adminToken,
                'phone_number' => '0917466943'.$i.'',
            ];
            $userPhone =  $this->userPhoneRepo->create($userPhoneData);

            $created10phone[$i] = $userPhone->id;
        }

        $showData = [
            'api_token' => $this->adminToken,
            'page' => 2
        ];

        $responseShow = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('GET', '/api/phones/'.$this->createdAdminId.'', $showData);

        //delete created user phones
        foreach ($created10phone as $key => $value) {
            $this->userPhoneRepo->delete($value);
        }

        $responseShow->assertStatus(201)
                ->assertJson([
                    "meta"=> [
                        "pagination" => [
                            "total" => 12,
                            "count" => 2,
                            "per_page" => 10,
                            "current_page" => 2,
                            "total_pages" =>2,
                        ]
                    ]
                ]);
    }

    // /** @test */
    public function delete_user_phone_successfully(): void
    {
        $deleteData = [
            'api_token' => $this->adminToken
        ];

        $responseDelete = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('DELETE', '/api/phone-delete/'.$this->createdUserPhoneId.'', $deleteData);

        $responseDelete->assertStatus(200)
                        ->assertJson([
                            "message" => "User's phone number successfully deleted"
                        ]);
    }

    /** @test */
    public function delete_user_phone_successfully_using_admin_account(): void
    {
        $deleteData = [
            'api_token' => $this->adminToken
        ];

        $responseDelete = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('DELETE', '/api/phone-delete/'.$this->createdUserPhoneId.'', $deleteData);

        $responseDelete->assertStatus(200)
                            ->assertJson([
                                "message" => "User's phone number successfully deleted"
                            ]);
    }

    /** @test */
    public function delete_user_phone_using_other_user_phone_account(): void
    {
        $nonAdminData = [
            'name' => 'User Non Admin',
            'email' =>  'non-admin-phone@mail.com',
            'password' => 'secret!',
            'role' => 'non-admin'
        ];
        $nonAdmin =  $this->userRepo->create($nonAdminData);

        $deleteData = [
            'api_token' => $nonAdmin->api_token
        ];

        $responseDelete = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('DELETE', '/api/phone-delete/'.$this->createdUserPhoneId.'', $deleteData);

        // delete created user
        $this->userRepo->delete($nonAdmin->id);

        $responseDelete->assertStatus(403)
                        ->assertJson([
                            "message" => "Unauthorized process.",
                            "error" => [
                                "api_token" => [
                                    "Invalid token."
                                ]
                            ]
                        ]);
    }

    public function tearDown()
    {
        $this->userPhoneRepo->delete($this->createdUserPhoneId);
        $this->userRepo->delete($this->createdAdminId); 
    }

}
