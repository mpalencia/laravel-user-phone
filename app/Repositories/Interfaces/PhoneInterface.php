<?php 

namespace App\Repositories\Interfaces;

interface PhoneInterface
{
    public function create(array $data);
    public function show($userPhoneId);
    public function update(array $data, $userPhoneId);
    public function delete($userPhoneId);
    public function getDetails($userPhoneId);
}