<?php

namespace App\Services;

use App\Models\Instruction;
use Crypt;

class InstructionService
{
    public function __construct(Instruction $instruction){
        $this->instruction = $instruction;
    }

    public function create($booking_id,$data)
    {
        $instruction = new Instruction();
        $instruction->booking_id = $booking_id;
        $instruction->credentials_username = Crypt::encryptString($data['credentials_username']);
        $instruction->credentials_password = Crypt::encryptString($data['credentials_password']);

        if (!$instruction->save()) {
            return false;
        }

        return $instruction;
    }

}