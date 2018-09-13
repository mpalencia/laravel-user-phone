<?php
namespace App\Transformers;

use App\Client;
use League\Fractal\TransformerAbstract;

class ClientTransformer extends TransformerAbstract
{
    public function transform(Client $client)
    {
        return [
            'id'             => $client->id,
            'name'        => $client->name,
            'email'        => $client->email,
            'registered' => $client->created_at,
            'password'  => $client->password,
        ];
    }
}
