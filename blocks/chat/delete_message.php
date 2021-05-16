<?php
require_once __DIR__ . '/twilio-php-main/src/Twilio/autoload.php';

use Twilio\Rest\Client;

function get_delete_message($messageSid) {
    $twilioAccountSid = getenv('TWILIO_ACCOUNT_SID');
    $twilioAuthToken = getenv("TWILIO_AUTH_TOKEN");
    $convId = "CH6e668ea65b4c4ee789b28aa1a938049d";
    $result = 0;

    $twilio = new Client($twilioAccountSid, $twilioAuthToken);

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