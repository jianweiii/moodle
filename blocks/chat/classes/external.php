<?php

require_once($CFG->libdir . "/externallib.php");
require_once($CFG->dirroot . "/blocks/chat/utils.php");

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
    * Returns description of delete_participant parameters.
    *
    * @return external_function_parameters
     */
    public static function delete_participant_parameters() {
        return new external_function_parameters(
                array(
                    'participant' => new external_single_structure(
                        array(
                            'sid'      => new external_value(PARAM_TEXT, 'sid of participant to delete'),
                            'identity' => new external_value(PARAM_TEXT, 'identity of participant to delete') 
                        )
                    )
                )
        );
    }

    /**
     * Deletes admin selected participant
     * 
     * @param array $participant includes sid and identity of participant
     * @return array Success code: 0 for fail, 1 for success
     */
    public static function delete_participant($participant) {
        $params = self::validate_parameters(self::delete_participant_parameters(), array('participant'=>$participant));
        $result = get_delete_participant($params['participant']);
        return array(
            'result' => $result
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
                'result' => new external_value(PARAM_TEXT, 'results for deletion of participant')
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
        $result = get_create_conversation($params['title']);
        $result['success'] = "true";
        $sql = "UPDATE mdl_block_chat SET live=1, conv=" . "'" . $result['id'] . "'" . "  WHERE id=1";

        try {
            $transaction = $DB->start_delegated_transaction();
            $DB->execute($sql);
            $transaction->allow_commit();
        } catch(Exception $e) {
            $transaction->rollback($e);
        }
        return array(
            'title'   => $params['title'],
            'success' => $result['success'],
            'id'      => $result['id']
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

    /**
    * Returns description of end_conversation parameters.
    *
    * @return external_function_parameters
     */
    public static function end_conversation_parameters() {
        return new external_function_parameters(
            array() 
        );
    }

    /**
     * End current session of conversation
     * 
     * @return array Success code: 0 for fail, 1 for success
     */
    public static function end_conversation() {
        global $DB;
        $table = "mdl_block_chat";
        $sql = "UPDATE mdl_block_chat SET live=0, conv='' WHERE id=1";
        $result = "";
        try {
            $transaction = $DB->start_delegated_transaction();
            $DB->execute($sql);
            $transaction->allow_commit();
            $result = "true";
        } catch(Exception $e) {
            $transaction->rollback($e);
            $result = "false";
        }
        return array(
            'success' => $result
        );
    }

    /**
     * Returns value of success of deleted participant
     * 
     * @return external_single_structure
     */
    public static function end_conversation_returns() {
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_TEXT, 'success of ending conversation')
            )
        );
    }
}