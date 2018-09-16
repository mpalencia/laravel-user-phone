<?php
namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform($user)
    {
        return [
            'id'             => $user->id,
            'name'        => $user->name,
            'email'        => $user->email,
            'role'          => $user->role,
            'registered' => $user->created_at,
        ];
    }
}
