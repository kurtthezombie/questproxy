<?php

namespace App\Services;

use App\Models\Instruction;
use Crypt;
use Exception;

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

    public function update($booking_id, $data){
        $instruction = Instruction::firstWhere('booking_id', $booking_id);
        
        if (!$instruction) {
            throw new Exception('Instruction not found.');
        }

        //update
        $instruction->update([
            'additional_notes' => $data['additional_notes'] ?? $instruction->additional_notes,
            'credentials_username' => isset($data['credentials_username']) 
                ? Crypt::encryptString($data['credentials_username'])
                : $instruction->credentials_username,
            'credentials_password' => isset($data['credentials_password'])
                ? Crypt::encryptString($data['credentials_password'])
                : $instruction->credentials_password
        ]);

        if (!$instruction->save()) {
            throw new Exception('Instruction update unsuccessful.');
        }

        return $instruction;
    }

    public function retrieveInstructions($booking_id)
    {
        $instruction = Instruction::firstWhere('booking_id',$booking_id);

        if(!$instruction)
        {
            throw new Exception("Instruction not found for booking ID $booking_id.");
        }

        // Decrypt and set values directly on the instruction object
        $instruction->credential_username = Crypt::decryptString($instruction->credential_username);
        $instruction->credential_password = Crypt::decryptString($instruction->credential_password);
        
        return $instruction;
    }
}