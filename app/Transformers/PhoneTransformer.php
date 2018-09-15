<?php
namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class PhoneTransformer extends TransformerAbstract
{
    public function transform($phone)
    {
        return [
            'id'                     => $phone->id,
            'user_id'             => $phone->user_id,
            'phone_number'  => $phone->phone_number,
        ];
    }
}
