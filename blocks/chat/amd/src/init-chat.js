import { deleteMessage, deleteParticipant } from './menu-button';

let messageWindow = document.getElementById("messages");
let userAdminRole = null;
let messageToDelete = null;
let participantToDelete = null;
let participantIdentity = null;
let conversationJoined = null;
// let clickedMessageContainer = null;

/**
 * Core function that establishes connection with Twilio API
 * @param {String} token
 * @param {Boolean} isAdmin
 */
export const connectChat = async (token, convId, isAdmin) => {
    userAdminRole = isAdmin;

    // Connect to Twilio
    const Twilio = window.Twilio;
    let conversationsClient = await Twilio.Conversations.Client.create(token);

    initDeleteButtons();

    // Check connection state
    conversationsClient.on("connectionStateChanged", (state) => {
        let connectionStatus = document.getElementById("connection-status");
        if (state === "connecting") {
            connectionStatus.innerHTML = "Connecting";
        }
        if (state === "connected") {
            connectionStatus.innerHTML = "Connected";
        }
        if (state === "disconnecting") {
            connectionStatus.innerHTML = "Disconnecting";
        }
        if (state === "disconnected") {
            connectionStatus.innerHTML = "Disconnected";
            // remove all nodes inside message window
            let messageNode = document.getElementById("messages");
            messageNode.querySelectorAll('*').forEach(messageBubble => messageBubble.remove());
        }
        if (state === "denied") {
            connectionStatus.innerHTML = "Denied";
        }
    });

    // Get conversation's messages
    conversationsClient.getConversationBySid(convId)
    .then(conversation => {
        conversationJoined = conversation;
        loadMessages(conversationJoined);
    })
    .catch(err => {
        window.console.log("Failed to get conversation:" + err);
    });

    // When new messages are added, update message window
    conversationsClient.on("messageAdded", message => {
        messageBubble(message.author, message.body, message.sid, message.index, message.participantSid);
    });

    // When messages are removed, update message window
    conversationsClient.on("messageRemoved", message => {
        deleteMessageBubble(message.index);
    });

    // Set up send message button to send message input to conversation
    let sendMessageInput = document.getElementById("btn-send-message");
    sendMessageInput.addEventListener("click", event => {
        event.preventDefault();
        let messageInput = document.getElementById("user-typed-message");
        conversationJoined.sendMessage(messageInput.value);
        messageInput.value = "";
    });
};

/**
 * After joining a conversation, load individual messages as a message bubble
 * @param {*} conversationJoined
 */
const loadMessages = async (conversationJoined) => {
    await conversationJoined.getMessages()
        .then( async (messageList) => {
            let messages = await messageList.items;
            messages.forEach( item => {
                messageBubble(item.author, item.body, item.sid, item.index, item.participantSid);
            });
        })
        .catch( err => {
            window.console.error("Couldn't fetch messages", err);
        });
};

/**
 * Each message bubble has:
 * <div class="message-container" id="index">
 *     <div class="message-author"></div>
 *     <div class="message-body"></div>
 * </div>
 *
 * Only admin has the rights to invoke delete functionality
 *
 * @param {*} identity
 * @param {*} message
 * @param {*} messageSid
 * @param {*} index
 */
const messageBubble = (identity, message, messageSid, index, participantSid) => {
    let messageContainer = document.createElement("div");
    messageContainer.className = "message-container";
    messageContainer.id = index;
    let messageAuthor = document.createElement("div");
    messageAuthor.className = "message-author";
    messageAuthor.textContent = identity;
    let messageBody = document.createElement("div");
    messageBody.className = "message-body";
    messageBody.textContent = message;
    messageContainer.appendChild(messageAuthor);
    messageContainer.appendChild(messageBody);

    // Give admin roles options to configure messages
    if (userAdminRole) {
        messageContainer.addEventListener("click", event => {
            event.preventDefault();
            toggleDeleteMenu(event.clientX, event.clientY);
            // clickedMessageContainer = messageContainer;
            messageToDelete = messageSid;
            participantToDelete = participantSid;
            participantIdentity = identity;
        });
    }
    messageWindow.appendChild(messageContainer);
};

/**
 * Toggles show and hide for delete menu
 */
const toggleDeleteMenu = (leftPos, topPos) => {
    let menuToggle = document.getElementById("message-admin-opt");
    let getBlockChat = document.getElementsByClassName("block_chat");
    let blockChatX = getBlockChat[0].getBoundingClientRect().x;
    let blockChatY = getBlockChat[0].getBoundingClientRect().y;
    menuToggle.style.left = "calc(" + (leftPos+20) + "px - " + blockChatX + "px)";
    menuToggle.style.top = "calc(" + (topPos-20) + "px - " + blockChatY + "px)";
    if (menuToggle.classList.contains("hide")) {
        menuToggle.classList.remove("hide");
        menuToggle.classList.add("show");
    } else if (menuToggle.classList.contains("show")) {
        menuToggle.classList.remove("show");
        menuToggle.classList.add("hide");
    }
};

/**
 * Initiate event listeners for delete buttons inside delete menu
 */
const initDeleteButtons = () => {
    let deleteMessageButton = document.getElementById("delete-message");
    deleteMessageButton.addEventListener("click", event => {
        event.preventDefault();
        deleteMessage(messageToDelete);
        let menuToggle = document.getElementById("message-admin-opt");
        menuToggle.classList.remove("show");
        menuToggle.classList.add("hide");
    });

    let deleteParticipantButton = document.getElementById("delete-participant");
    deleteParticipantButton.addEventListener("click", event => {
        event.preventDefault();
        deleteParticipant(participantToDelete, participantIdentity);
        let menuToggle = document.getElementById("message-admin-opt");
        menuToggle.classList.remove("show");
        menuToggle.classList.add("hide");
    });
};

/**
 * Deletes message bubble with corresponding index number
 * @param {*} index
 */
const deleteMessageBubble = (index) => {
    let messageBubble = document.getElementById(index);
    messageBubble.parentNode.removeChild(messageBubble);
};