<?php
require_once __DIR__ . '/twilio-php-main/src/Twilio/autoload.php';

use Twilio\Rest\Client;

function get_delete_message($messageSid) {
    $sid = "AC366a8fd9a2425a6e4cbd0b14cd9a740f";
    $token = "c59fcd560d487e003eb47964f84b7ee0";
    $convId = "CH6e668ea65b4c4ee789b28aa1a938049d";
    $result = 0;

    $twilio = new Client($sid, $token);

    try {
        $twilio->conversations->v1->conversations($convId)
                                    ->messages($messageSid)
                                    ->delete();
        $result = "success";    
    } catch ( Exception $e) {
        $result = $e;
    }
    return $result;
}