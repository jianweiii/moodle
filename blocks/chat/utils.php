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
    } catch ( Exception $e) {
        $result['success'] = "false";
        $result['id'] = $e;
    } finally {
        return $result;
    }
    
}

function get_delete_participant($participant) {
    global $DB;
    $twilioAccountSid = getenv('TWILIO_ACCOUNT_SID');
    $twilioAuthToken = getenv("TWILIO_AUTH_TOKEN");
    $convId = $DB->get_record('block_chat', ['id' => 1])->conv;
    $result = null;

    $twilio = new Client($twilioAccountSid, $twilioAuthToken);

    try {
        // Delete participant in Twilio
        $twilio->conversations->v1->conversations($convId)
                                    ->participants($participant['sid'])
                                    ->delete();

        // Add participant to block list in moodle
        $table = 'block_participants';
        $dataobject = [
            'identity' => $participant['identity']
        ]; 
        $transaction = $DB->start_delegated_transaction();
        $DB->insert_record($table, $dataobject, $returnid=false, $bulk=false);
        $transaction->allow_commit();
        $result = "success";    
    } catch ( Exception $e) {
        $result = $e;
        $transaction->rollback($e);
    }
    return $result;
}