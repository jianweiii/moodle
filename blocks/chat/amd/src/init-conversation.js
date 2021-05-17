import Ajax from 'core/ajax';

export const createConversation = () => {
    window.console.log("CREATED CONVO!");
    let goLiveBtn = document.getElementById("go-live-btn");
    goLiveBtn.addEventListener("click", () => {
        let getConvTitle = document.getElementById("conv-friendly-name");
        let promises = Ajax.call([
            { methodname: 'block_chat_create_conversation', args: { title: getConvTitle.value } }
          ]);
        promises[0].done(response => {
            window.console.log(response);
        }).fail(err => {
            window.console.log(err);
        });
    });
};