<?php

namespace App\Models;

class Message
{
    public $id;
    public $sender_id;
    public $receiver_id;
    public $content;
    public $is_read;
    public $created_at;

    // Propriétés additionnelles pour l'affichage
    public $donor_name;
    public $donor_avatar;
    public $donation_amount;

    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
