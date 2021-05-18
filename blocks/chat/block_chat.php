<?php
require_once __DIR__ . '/twilio-php-main/src/Twilio/autoload.php';
require 'token.php';

use Twilio\Rest\Client;

class block_chat extends block_base {

    private $chat_html = "";
    private $is_live = 0; 

    public function init() {
        $this->title = get_string('pluginname', 'block_chat');
    }

    public function get_content() {
        if ($this->content !== null) {
          return $this->content;
        }
        // Initialise content
        global $USER, $DB;
        
        // Check DB
        $this->is_live = $DB->get_record('block_chat', ['activity' => 'current'])->live;
        if (($this->is_live == "0") && is_siteadmin()) {
            $this->page->requires->js_call_amd('block_chat/init-conversation', 'createConversation');
        }
        if ($this->is_live == "1") {
            $this->debug_to_console("true");
        }

        $this->get_chat_box_html();
        $this->content         =  new stdClass;
        $this->content->text   = $this->chat_html;

        // $twilioAccountSid = getenv('TWILIO_ACCOUNT_SID');
        // $twilioAuthToken = getenv("TWILIO_AUTH_TOKEN");
        // $this->twilio = new Client($twilioAccountSid, $twilioAuthToken);
        // if (is_siteadmin()) {
        //     $this->debug_to_console("Yes admin");    
        // } else {
        //     $this->debug_to_console("Not admin");
        // }
        
        
        
        
        // if (!$this->is_live && is_siteadmin()) {
        //     
        // }
        // // create access token to conversation
        // if ($this->is_live) {
        // $this->generatedToken = createAccessToken(100);
        // $this->page->requires->js_call_amd('block_chat/init-chat', 'connectChat', array($this->generatedToken, is_siteadmin()));
        // }
        
        
        
     
        return $this->content;
    }

    /**
     * <div id="create-conversation">
     *     <span id="conv-title">Conversation Title:</span>
     *     <textarea name="conv-friendly-name" id="conv-friendly-name" rows="1" placeholder="Type title here..."></textarea>
     *     <button type="button" id="go-live-btn">Go Live</button>
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
        if (($this->is_live == "0") && is_siteadmin()) {
            $this->chat_html .= html_writer::start_tag('div', array('id' => 'create-conversation', 'class' => 'show'));
            $this->chat_html .= '<span id="conv-title">Conversation Title:</span>';
            $this->chat_html .= '<textarea name="conv-friendly-name" id="conv-friendly-name" rows="1" placeholder="Type title here..."></textarea>';
            $this->chat_html .= '<button type="button" id="go-live-btn">Go Live</button>';
            $this->chat_html .= html_writer::end_tag('div');
        }
        if ($this->is_live == "1") {
            $this->debug_to_console("true");
            $this->chat_html .= html_writer::start_tag('div', array('class' => 'chat-app'));
            $this->chat_html .= html_writer::start_tag('div', array('class' => 'chat-title'));
            $this->chat_html .= '<div class="chat-header">Live Chat</div>';
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
    private function create_participant() {
        global $USER;
        try {
            $participant = $this->twilio->conversations->v1->conversations("CH6e668ea65b4c4ee789b28aa1a938049d")
                                                           ->participants
                                                           ->create([
                                                                        "identity" => $USER->firstname . $USER->lastname
                                                                        // "identity" => "Peter"
                                                                    ]
                                                             );
            
            $this->debug_to_console($participant->sid);
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