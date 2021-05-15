<?php

require_once($CFG->libdir . "/externallib.php");
require_once($CFG->dirroot . "/blocks/chat/delete_message.php");

class delete_chat_external extends external_api {
    
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
        $result = get_delete_message($messageSid);
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
}