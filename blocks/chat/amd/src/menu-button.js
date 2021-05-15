import Ajax from 'core/ajax';

/**
 * Ajax call to backend to delete message with corresponding sid
 * @param {*} messageSid 
 */
export const deleteMessage = (messageSid) => {
    let promises = Ajax.call([
      { methodname: 'block_chat_delete_message', args: { sid: messageSid } }
    ]);

    promises[0].done(response => {
      window.console.log(response);
    }).fail(err => {
      window.console.log(err);
    });
};

/**
 * Ajax call to backend to delete participant with corresponding sid
 * @param {*} participantSid 
 */
export const deleteParticipant = (participantSid) => {
    let deleteButton = document.getElementById("delete-participant");
    deleteButton.addEventListener("click", event => {
        event.preventDefault();
        window.alert("Delete Participant Clicked!");
    });
};
