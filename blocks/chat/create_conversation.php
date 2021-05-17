<?php
require_once __DIR__ . '/twilio-php-main/src/Twilio/autoload.php';

use Twilio\Rest\Client;

function get_create_conversation($title) {
    $twilioAccountSid = getenv('TWILIO_ACCOUNT_SID');
    $twilioAuthToken = getenv("TWILIO_AUTH_TOKEN");
    $result = array(
        "success" => "false",
        "id"  => ""
    );

    $twilio = new Client($twilioAccountSid, $twilioAuthToken);

    try {
        $conversation = $twilio->conversations->v1->conversations
                                          ->create([
                                                       "friendlyName" => $title
                                                   ]
                                          );
        $result['success'] = "true";
        $result['id'] = $conversation->sid;
        // $result['id'] = "CH6181545e13874480bbed3f0612ce1a40";
    } catch ( Exception $e) {
        $result['success'] = "false";
        $result['id'] = $e;
    } finally {
        return $result;
    }
    
}