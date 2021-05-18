<?php

require_once($CFG->libdir . "/externallib.php");
require_once($CFG->dirroot . "/blocks/chat/delete_message.php");
require_once($CFG->dirroot . "/blocks/chat/create_conversation.php");

class chat_external extends external_api {
    
    /**
    * Returns description of delete_message parameters.
    *
    * @return external_function_parameters
     */
    public static function delete_message_parameters() {
        return new external_function_parameters(
                array('sid' => new external_value(PARAM_TEXT, 'sid of message to delete')) 
        );
    }

    /**
     * Deletes admin selected message
     * 
     * @param int $sid The sid of the particular message
     * @return array Success code: 0 for fail, 1 for success
     */
    public static function delete_message($messageSid) {
        $params = self::validate_parameters(self::delete_message_parameters(), array('sid'=>$messageSid));
        $result = get_delete_message($params['sid']);
        return array(
            'result' => $result
        );
    }

    /**
     * Returns value of success of deleted message
     * 
     * @return external_single_structure
     */
    public static function delete_message_returns() {
        return new external_single_structure(
            array(
                'result' => new external_value(PARAM_TEXT, 'results for deletion of message')
            )
            );
    }

    /**
    * Returns description of delete_message parameters.
    *
    * @return external_function_parameters
     */
    public static function delete_participant_parameters() {
        return new external_function_parameters(
                array('sid' => new external_value(PARAM_TEXT, 'sid of participant to delete')) 
        );
    }

    /**
     * Deletes admin selected participant
     * 
     * @param int $sid The sid of the particular participant
     * @return array Success code: 0 for fail, 1 for success
     */
    public static function delete_participant($sid) {
        $params = self::validate_parameters(self::delete_message_parameters(), array('sid'=>$sid));
        return array(
            'sid' => $sid,
            'success' => 0
        );
    }

    /**
     * Returns value of success of deleted participant
     * 
     * @return external_single_structure
     */
    public static function delete_participant_returns() {
        return new external_single_structure(
            array(
                'sid' => new external_value(PARAM_TEXT, 'sid of message deleted'),
                'success' => new external_value(PARAM_INT, '0 for fail, 1 for success')
            )
            );
    }

    /**
    * Returns description of create_conversation parameters.
    *
    * @return external_function_parameters
     */
    public static function create_conversation_parameters() {
        return new external_function_parameters(
            array('title' => new external_value(PARAM_TEXT, 'title to create conversation in twilio')) 
        );
    }

    /**
     * Create conversation based on admin's choice of title
     * 
     * @param int $sid The sid of the particular participant
     * @return array Success code: 0 for fail, 1 for success
     */
    public static function create_conversation($convTitle) {
        global $DB;
        $params = self::validate_parameters(self::create_conversation_parameters(), array('title'=>$convTitle));
        // $result = get_create_conversation($params['title']);
        $result = "CH52ffd5ba92c343b4bbd5f0474f35e621";
        $table = "mdl_block_chat";
        // $data_object = array(
        //     "id"           => 1,
        //     "activity"     => "current",
        //     "live"         => 1,
        //     "conversation" => $result
        // );
        // $data_object = array(
        //     "activity"     => "current",
        //     "live"         => 1,
        //     "conversation" => $result
        // );
        // $dataobj = new stdclass;
        // $dataobj->id = 1;
        // $dataobj->activity = "current";
        // $dataobj->live = 1;
        // $dataobj->conv = $result;
        $dataobject= array(
            'id'          => "1",
            'live'        => "1",
            'conv'        => "hello"
        );
        $sql = "UPDATE mdl_block_chat SET live=1, conv=" . "'" . $result . "'" . "  WHERE id=1";

        global $DB;
        try {
            $transaction = $DB->start_delegated_transaction();
            $DB->execute($sql);
            $transaction->allow_commit();
        } catch(Exception $e) {
            $transaction->rollback($e);
        }
        return array(
            'title'   => $params['title'],
            'success' => "success",
            'id'      => json_encode($result)

        );
    }

    /**
     * Returns value of success of deleted participant
     * 
     * @return external_single_structure
     */
    public static function create_conversation_returns() {
        return new external_single_structure(
            array(
                'title' => new external_value(PARAM_TEXT, 'title of conversation'),
                'success' => new external_value(PARAM_TEXT, 'success of conversation'),
                'id' => new external_value(PARAM_TEXT, 'sid of conversation')
            )
        );
    }
}