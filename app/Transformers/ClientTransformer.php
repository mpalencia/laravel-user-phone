<?php
namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class ClientTransformer extends TransformerAbstract
{
    public function transform($client)
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
