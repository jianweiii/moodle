
// Include cdn script for Twilio, ignore warnings
export const connectChat = async (token) => {
    let conversationsClient = await Twilio.Conversations.Client.create(token);
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
};
