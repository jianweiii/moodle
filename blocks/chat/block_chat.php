<?php
require_once __DIR__ . '/twilio-php-main/src/Twilio/autoload.php';
require 'token.php';

use Twilio\Rest\Client;

class block_chat extends block_base {

    private $chat_html = "";
    private $sid = "AC366a8fd9a2425a6e4cbd0b14cd9a740f";
    private $token = "c59fcd560d487e003eb47964f84b7ee0";

    public function init() {
        $this->title = get_string('pluginname', 'block_chat');
    }
    // The PHP tag and the curly bracket for the class definition 
    // will only be closed after there is another function added in the next section.

    public function get_content() {
        if ($this->content !== null) {
          return $this->content;
        }
        // Initialise content inside chat box
        $this->get_chat_box_html();

        $this->twilio = new Client($this->sid, $this->token);
        
        // $this->create_participant();

        // create access token to conversation
        $output = createAccessToken(3600);
        $this->debug_to_console2($output);
        
        // $this->page->requires->js_call_amd('block_chat/helloworld', 'init', array($first, $last));
        
        $this->content         =  new stdClass;
        $this->content->text   = $this->chat_html;
     
        return $this->content;
    }

    private function get_chat_box_html() {
        global $USER;
        $this->chat_html .= html_writer::start_tag('div', array('class' => 'chat-app'));
        $this->chat_html .= html_writer::start_tag('div', array('class' => 'chat-title'));
        $this->chat_html .= '<div class="chat-header">Live Chat</div>';
        $this->chat_html .= '<div class="chat-username">Displayed name: ' . $USER->firstname . '</div>';
        $this->chat_html .= html_writer::end_tag('div');
        $this->chat_html .= html_writer::start_tag('div', array('class' => 'chat-body'));
        $this->chat_html .= html_writer::end_tag('div');
        $this->chat_html .= html_writer::start_tag('div', array('class' => 'chat-message'));
        $this->chat_html .= '<textarea name="message" id="user-typed-message" rows="2" placeholder="Type message here..."></textarea>';
        $this->chat_html .= '<button class="btn btn-secondary" type="button" id="btn-send-message">Send</button>';
        $this->chat_html .= html_writer::end_tag('div');
        $this->chat_html .= html_writer::end_tag('div');
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
    private function debug_to_console2($data) {
        if(is_array($data) || is_object($data)) {
          echo("<script>console.log('PHP: ".json_encode($data)."');</script>");
        } else {
          echo("<script>console.log('PHP: $data');</script>");
        }
    }
}