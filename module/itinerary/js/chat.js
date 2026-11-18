let Chat = function () {

    const options = {
        selectors: {
            body:           $('body')
        },
        userInfo: {
            roomId:         $('input[name=room_id]').val(),
            userId:         $('input[name=user_id]').val(),
            userName:       $('input[name=user_name]').val(),
            avatarUrl:      $('input[name=avatar_url]').val()
        },
        socketInfo: {
            url: location.protocol + '//' + location.host + ':8893'
        }
    };

    let handleAppendChatBox = function () {
        let chatBoxHtml = `<div class="bravo_chat"><div class="bravo_chat_icon position-fixed px-2 py-1"><i class="fa fa-commenting fa-2x text-white"></i></div><div class="bravo_chat_box position-fixed"><div class="bravo_chat_box_topbar text-white px-3 py-2"><img class="avatar align-bottom" src="${options.userInfo.avatarUrl}" alt=""><span class="ml-2">${options.userInfo.userName}</span></div><div class="bravo_chat_box_content"><ul class="list-unstyled p-3"></ul></div><div class="bravo_chat_box_control input-group"><textarea id="msgInput" class="form-control" placeholder="ここに入力してください"></textarea><div class="input-group-append "><button type="button" class="btn btn-link text-primary bg-white px-3 py-1" id="sendMsgBtn"><i class="fa fa-paper-plane"></i></button></div></div></div></div>`;
        options.selectors.body.append(chatBoxHtml);
        $.ajax({
            url: location.protocol + '//' +  location.host + '/itinerary/chatting/' + options.userInfo.roomId,
            type: 'get',
            success: function (data) {
                if (data.status === 200) {
                    $.each(data.chat_history, function (key, chatInfo) {
                        if (chatInfo.user_id == options.userInfo.userId) {
                            handleAppendSendMsg(chatInfo.avatar_url, chatInfo.content, new Date(chatInfo.created_at).toLocaleString('jp-JP', {timeZone: 'Asia/Tokyo'}));
                        } else {
                            handleAppendReceiveMsg(chatInfo.avatar_url, chatInfo.content, new Date(chatInfo.created_at).toLocaleString('jp-JP', {timeZone: 'Asia/Tokyo'}));
                        }
                    });
                    scrollToBottom();
                }
            }
        });
    };

    let handleSocket = function () {
        window.onload = function () {
            socket.emit('present', {
                status:     'on',
                room_id:    options.userInfo.roomId,
                user_id:    options.userInfo.userId,
                user_name:  options.userInfo.userName
            });
        };
        window.onunload = function () {
            socket.emit('present', {
                status:     'off',
                room_id:    options.userInfo.roomId,
                user_id:    options.userInfo.userId,
                user_name:  options.userInfo.userName
            });
        };
        socket.on('chat', function (data) {
            handleAppendReceiveMsg(data.avatar_url, data.msg, data.time);
        });
        socket.on('toastr', function (data) {
            if (toastr !== undefined) {
                if (data.status === 'on') {
                    toastr.success(data.msg);
                } else {
                    toastr.error(data.msg);
                }
            }
        });
    };

    let handleChatBox = function () {
        $('.bravo_chat_icon').on('click', function () {
            $('.bravo_chat_box').fadeToggle();
        });
        $('#msgInput').on('keypress', function (e) {
            if (e.which === 13 && !event.shiftKey) {
                e.preventDefault();
                $('#sendMsgBtn').click();
            }
        });
        $('#sendMsgBtn').on('click', function () {
            let sendMsg = $('#msgInput').val().trim();
            if (sendMsg !== '') {
                handleAppendSendMsg(options.userInfo.avatarUrl, sendMsg, new Date().toLocaleString('jp-JP', {timeZone: 'Asia/Tokyo'}));
                socket.emit('chat', {
                    room_id:    options.userInfo.roomId,
                    user_id:    options.userInfo.userId,
                    avatar_url: options.userInfo.avatarUrl,
                    msg:        sendMsg,
                    time:       new Date().toLocaleString('ja-JP', {timeZone: 'Asia/Tokyo'})
                });
                $('#msgInput').val('');
                scrollToBottom();
            }
        });
    };

    let handleAppendSendMsg = function (avatar, msg, time) {
        let chatBoxContentHtml = `<li class="d-flex mb-2"><img class="avatar" src="${avatar}" alt=""><span class="chat-msg send-msg text-white ml-2 p-2">${msg}</span></li><li class="d-flex justify-content-center text-black-50 font-italic mb-2"><small>${time}</small></li>`;
        $('.bravo_chat_box_content ul').append(chatBoxContentHtml);
    };

    let handleAppendReceiveMsg = function (avatar, msg, time) {
        let chatBoxContentHtml = `<li class="d-flex justify-content-end mb-2"><span class="chat-msg receive-msg text-white mr-2 p-2">${msg}</span><img class="avatar" src="${avatar}" alt=""></li><li class="d-flex justify-content-center text-black-50 font-italic mb-2"><small>${time}</small></li>`;
        $('.bravo_chat_box_content ul').append(chatBoxContentHtml);
    };

    let scrollToBottom = function () {
        let chatDiv = $('.bravo_chat_box_content');
        chatDiv.scrollTop(chatDiv[0].scrollHeight);
    };

    let init = function () {
        window.socket = io.connect(options.socketInfo.url);
        handleAppendChatBox();
        handleSocket();
        handleChatBox();
    };

    return {
        init: init
    }
}();

(function ($) {

    Chat.init();

    /*   var timer = 0;
    $('body').on('mousemove', function () {
    clearTimeout(timer);
    timer = setTimeout(function () {
    window.history.back;
    }, 20000)
    })
    */
})(jQuery);
