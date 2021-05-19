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
export const deleteParticipant = (participantSid, participantIdentity) => {
  let participantInfo = {
    'sid': participantSid,
    'identity': participantIdentity
  };
  window.console.log(participantInfo);
  let promises = Ajax.call([
    { methodname: 'block_chat_delete_participant', args: { participant: participantInfo } }
  ]);
  promises[0].done(response => {
    window.console.log(response);
  }).fail(err => {
    window.console.log(err);
  });
};
