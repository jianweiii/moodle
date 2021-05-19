<?php
require_once __DIR__ . '/twilio-php-main/src/Twilio/autoload.php';

use Twilio\Rest\Client;

function get_delete_message($messageSid) {
    global $DB;
    $twilioAccountSid = getenv('TWILIO_ACCOUNT_SID');
    $twilioAuthToken = getenv("TWILIO_AUTH_TOKEN");
    $convId = $DB->get_record('block_chat', ['id' => 1])->conv;
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