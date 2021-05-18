import Ajax from 'core/ajax';

export const createConversation = () => {
    let goLiveBtn = document.getElementById("go-live-btn");
    goLiveBtn.addEventListener("click", () => {
        let getConvTitle = document.getElementById("conv-friendly-name");
        let promises = Ajax.call([
            { methodname: 'block_chat_create_conversation', args: { title: getConvTitle.value } }
          ]);
        promises[0].done(response => {
            window.console.log(response);
            if(response['success'] == 'true' ) {
                window.location.reload();
            }
        }).fail(err => {
            window.console.log(err);
        });
    });
};

export const endConversation = () => {
    let endBtn = document.getElementById("end-btn");
    endBtn.addEventListener("click", () => {
        let promises = Ajax.call([
            { methodname: 'block_chat_end_conversation', args: {} }
          ]);
        promises[0].done(response => {
            window.console.log(response);
            // if(response['success'] == 'true' ) {
            //     window.location.reload();
            // }
        }).fail(err => {
            window.console.log(err);
        });
    });
};