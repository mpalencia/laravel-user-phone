<?php 

namespace App\Repositories\Interfaces;

interface UserInterface
{

    public function create(array $data);
    public function update(array $data, $user_id);
    public function show($user_id);
    public function delete($user_id);
    public function getTokenUserId($api_token);
    public function getTokenUserDetails($api_token);

}