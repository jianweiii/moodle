let messageWindow = document.getElementById("messages");

// Include cdn script for Twilio, ignore warnings
export const connectChat = async (token) => {
    let conversationsClient = await Twilio.Conversations.Client.create(token);
    let conversationJoined = null;

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
        }
        if (state === "denied") {
            connectionStatus.innerHTML = "Denied";
        }
    });

    // Upon joining conversation, populate messages and load it into message window
    conversationsClient.on("conversationJoined", (conversation) => {
        conversationJoined = conversation;
        loadMessages(conversation);
    });

    // When new messages are added, update message window
    conversationsClient.on("messageAdded", message => {
        messageBubble(message.author, message.body);
    });

    // Set up send message button to send message input to conversation
    let sendMessageInput = document.getElementById("btn-send-message");
    sendMessageInput.addEventListener("click", event => {
        event.preventDefault();
        let messageInput = document.getElementById("user-typed-message");
        window.console.log(messageInput.value);
        conversationJoined.sendMessage(messageInput.value);
        messageInput.value = "";
    });
};


const loadMessages = (conversationJoined) => {
    conversationJoined.getMessages()
        .then( async (messageList) => {
            let messages = await messageList.items;
            // window.console.log(messages);
            messages.forEach( item => {
                messageBubble(item.author, item.body);
            });
        })
        .catch( err => {
            window.console.error("Couldn't fetch messages", err);
        });
};

const messageBubble = (identity, message) => {
    let messageContainer = document.createElement("div");
    messageContainer.className = "message-container";
    let messageAuthor = document.createElement("div");
    messageAuthor.className = "message-author";
    messageAuthor.textContent = identity;
    let messageBody = document.createElement("div");
    messageBody.className = "message-body";
    messageBody.textContent = message;
    messageContainer.appendChild(messageAuthor);
    messageContainer.appendChild(messageBody);
    messageWindow.appendChild(messageContainer);
};