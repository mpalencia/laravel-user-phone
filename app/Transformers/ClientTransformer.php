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
            'authorize'  => $client->authorize,
            'registered' => $client->created_at,
        ];
    }
}
