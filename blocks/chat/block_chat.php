<?php
require_once __DIR__ . '/twilio-php-main/src/Twilio/autoload.php';
require 'token.php';

use Twilio\Rest\Client;

class block_chat extends block_base {

    private $chat_html = "";
    private $info = 0; 

    public function init() {
        $this->title = get_string('pluginname', 'block_chat');
    }

    public function get_content() {
        if ($this->content !== null) {
          return $this->content;
        }
        // Initialise content
        global $USER, $DB;

        $current_user = $USER->firstname;
        // Check DB if user is blocked
        $is_user_blocked = $DB->record_exists('block_participants', array('identity'=>$current_user));
        if (!$is_user_blocked) {
            // Check DB for live info
            $this->info = $DB->get_record('block_chat', ['id' => 1]);
            if (is_siteadmin()) {
                $this->page->requires->js_call_amd('block_chat/init-conversation', 'createConversation');
                $this->page->requires->js_call_amd('block_chat/init-conversation', 'endConversation');
            }
            if ($this->info->live == "1") {
                // create access token for twilio client
                $this->create_participant($current_user, $this->info->conv);
                $this->generatedToken = createAccessToken(1000, $current_user);
                $this->page->requires->js_call_amd('block_chat/init-chat', 'connectChat', array($this->generatedToken, $this->info->conv, is_siteadmin()));
            }
            $this->get_chat_box_html();
        } else {
            $this->chat_html .= '<div>Sorry, you have been blocked</div>';
        }
        


        $this->content         =  new stdClass;
        $this->content->text   = $this->chat_html; 
        
        return $this->content;
    }

    /**
     * <div id="create-conversation">
     *     <span id="conv-title">Conversation Title:</span>
     *     <textarea name="conv-friendly-name" id="conv-friendly-name" rows="1" placeholder="Type title here..."></textarea>
     *     <button type="button" id="go-live-btn">Go Live</button>
     *     <button type="button" id="end-btn">End</button>
     * </div>
     * <div class="chat-app">
     *     <div class="chat-title">
     *         <div class="chat-header">Live Chat</div>
     *         <div id="connection-status"></div>
     *     </div>
     *     <div class="chat-body">
     *         <div id="messages"></div>
     *     </div>
     *     <div class="chat-message">
     *         <textarea id="user-typed-message"></textarea>
     *         <button id="btn-send-message"></button>
     *     </div>
     * </div>
     * <div class="message-admin-opt">
     *     <button id="delete-message">Delete Message</button>
     *     <button id="delete-participant">Delete Participant</button>
     * </div>
     */
    private function get_chat_box_html() {
        global $USER;
        $this->chat_html .= '<script src="https://media.twiliocdn.com/sdk/js/conversations/v1.1/twilio-conversations.min.js"></script>';
        if (is_siteadmin()) {
            $this->chat_html .= html_writer::start_tag('div', array('id' => 'create-conversation', 'class' => 'show'));
            $this->chat_html .= '<span id="conv-title">Conversation Title:</span>';
            $this->chat_html .= '<textarea name="conv-friendly-name" id="conv-friendly-name" rows="1" placeholder="Type title here..."></textarea>';
            $this->chat_html .= '<button type="button" id="go-live-btn">Go Live</button>';
            $this->chat_html .= '<button type="button" id="end-btn">End</button>';
            $this->chat_html .= html_writer::end_tag('div');
        }
        if ($this->info->live == "1") {
            $this->chat_html .= html_writer::start_tag('div', array('class' => 'chat-app'));
            $this->chat_html .= html_writer::start_tag('div', array('class' => 'chat-title'));
            $this->chat_html .= '<div class="chat-header">Live Chat (' . $USER->firstname . ')</div>';
            $this->chat_html .= '<div id="connection-status"></div>';
            $this->chat_html .= html_writer::end_tag('div');
            $this->chat_html .= html_writer::start_tag('div', array('class' => 'chat-body'));
            $this->chat_html .= '<div id="messages"></div>';
            $this->chat_html .= html_writer::end_tag('div');
            $this->chat_html .= html_writer::start_tag('div', array('class' => 'chat-message'));
            $this->chat_html .= '<textarea name="message" id="user-typed-message" rows="2" placeholder="Type message here..."></textarea>';
            $this->chat_html .= '<button class="btn btn-secondary" type="button" id="btn-send-message">Send</button>';
            $this->chat_html .= html_writer::end_tag('div');
            $this->chat_html .= html_writer::end_tag('div');
            $this->chat_html .= html_writer::start_tag('div', array('id' => 'message-admin-opt', 'class' => 'hide'));
            $this->chat_html .= '<button type="button" id="delete-message">Delete Message</button>';
            $this->chat_html .= '<button type="button" id="delete-participant">Delete Participant</button>';
            $this->chat_html .= html_writer::end_tag('div');
        }
    }

    // Create participant identity if it does not currently exist.
    private function create_participant($identity, $conv) {
        $twilioAccountSid = getenv('TWILIO_ACCOUNT_SID');
        $twilioAuthToken = getenv("TWILIO_AUTH_TOKEN");
        $twilio = new Client($twilioAccountSid, $twilioAuthToken);
        try {
            $participant = $twilio->conversations->v1->conversations($conv)
                                                           ->participants
                                                           ->create([
                                                                        "identity" => $identity
                                                                        // "role"
                                                                    ]
                                                             );
        } catch ( Exception $e) {
            // catch duplicate entries
        }   
    }

    private function debug_to_console($data) {
        $output = $data;
        if (is_array($output))
            $output = implode(',', $output);
    
        echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
    }
}