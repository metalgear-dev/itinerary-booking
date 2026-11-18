(function($) {

    function getCookie(cname) {
        let name = cname + "=";
        let decodedCookie = decodeURIComponent(document.cookie);
        let ca = decodedCookie.split(';');
        for(let i = 0; i <ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) === 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    MemberStatusManager = function(option) {

        var self = this;

        var _option = {
            interval: 5000
        };

        Object.assign(_option, option);

        var updateFunctions = {};

        var setFunction = function(key, fn) {
            updateFunctions[key] = fn;
        };

        var getFunction = function(key) {
            return defaultOption[key];
        };

        var removeFunction = function(key) {
            updateFunctions[key] = undefined;
        };

        var getFunctions = function() {
            var fnList = [];
            for (var key in updateFunctions) {
                fnList.push(updateFunctions[key]);
            }
            return fnList;
        };

        var run = function() {
            self._handle = setInterval(function() {
                var fnList = getFunctions();
                fnList.forEach(function(fn) {
                    if (typeof fn === 'function') {
                        fn();
                    }
                });
            }, _option.interval);
        };

        var stop = function() {
            if (self._handle) {
                clearInterval(self._handle);
            }
        };

        var showInquiry = function(id) {
            let $modal = $("#modalInquiryDetail");
            $.get(
                '/itinerary/member/inquiry/getFormData/' + id,
                {},
                function(resp) {
                    resp = JSON.parse(resp);
                    var $modalBody = $modal.find('.modal-body');
                    $modalBody.html('');
                    for(var key in resp) {
                        $(`<div class="row mt-2">
                        <div class="col-md-3 font-weight-bold">${key}</div>
                        <div class="col-md-9">${resp[key]}</div>
                    </div>`).appendTo($modalBody);
                    }
                    $modal.find('.modal-footer a').attr('href', '/itinerary/consult/' + id);
                    $modal.modal('show', function() {
                        $this.parent().parent().addClass('table-primary');
                    });
                    updateMemberStatus();
                }
            )
        };

        var lastInquiryId = 0;

        return {setFunction, getFunction, removeFunction, getFunctions, run, stop, showInquiry, lastInquiryId}

    };

    window.updateMemberStatus = function() {
        // var $a = $('.ion-ios-help-buoy').parents('a');
        // $.get(
        //     '/itinerary/member/updateInfo',
        //     {},
        //     function(resp) {
        //         var appliedTotal = resp.applied.length;
        //         var unreadTotal = resp.unread.length;
        //         var unnotifiedTotal = resp.unnotified.length;
        //         if (appliedTotal && $a.length) {
        //             $a.find('.badge').remove();$a.find('.sr-only').remove();
        //             $a.append(`<span class="badge badge-danger badge-pill p-1">${appliedTotal}</span><span class="sr-only">unreplied inquiries</span>`)
        //         }
        //         if (unnotifiedTotal) {
        //             toastr.info(`${unnotifiedTotal}件の問い合わせが入ってきました`)
        //         }
        //     }
        // );

        $.get(
            '/itinerary/member/updateInfo',
            {},
            function(resp) {
                if (resp.unread && resp.unread.length > 1) {
                    let newInquiriesHtml = '';
                    let inquiry_senders = [];
                    let max_inquiry_id = 0;
                    resp.unread.forEach(function(inquiry) {
                        if(inquiry.inquiry_id > max_inquiry_id)
                            max_inquiry_id = inquiry.inquiry_id;
                        if (inquiry.inquiry_id > infoManager.lastInquiryId) {
                            var form_content = JSON.parse(inquiry.form_content);
                            var username = inquiry.user_name;
                            var content = '';
                            if (form_content['お名前']) username = form_content['お名前'];
                            if (form_content['name']) username = form_content['name'];
                            if (form_content['問い合わせ']) content = form_content['問い合わせ'];
                            if (form_content['content']) content = form_content['content'];
                            var trunc_content = content.substring(0, 20);
                            inquiry_senders.push(username);
                            newInquiriesHtml += `
                                <li>
                                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); window.infoManager.showInquiry(${inquiry.inquiry_id});">
                                        <div><strong>${username}</strong></div>
                                        <div>${trunc_content}</div>
                                    </a>
                                </li>
                            `;
                        }
                    });
                    infoManager.lastInquiryId = max_inquiry_id;
                    toastr.options = {
                        "positionClass": "toast-bottom-right"
                    };

                    // check if the number of unread inquiries changes
                    let inquiry_num_updated = false;
                    if((getCookie('inquiry_number') !== "" && parseInt(getCookie('inquiry_number')) !== inquiry_senders.length) ||
                        getCookie('inquiry_number') === ""){
                        inquiry_num_updated = true;
                        document.cookie = `inquiry_number = ${inquiry_senders.length}`;
                    }

                    // if it's changed, then toastr starts
                    if(inquiry_num_updated){
                        if(inquiry_senders.length === 1){
                            toastr.info(`${inquiry_senders[0]}さんからの問い合わせが入ってきました。`).css("width", "330px")
                        }else if(inquiry_senders.length > 0){
                            toastr.info(`${inquiry_senders.length}件の未読の問い合わせがあります。`).css("width", "330px")
                        }
                    }

                    $('.itinerary-inbox-content').prepend(newInquiriesHtml);
                    $(".itinerary-mailbox").parent().find(".badge").remove();
                    $(".itinerary-mailbox").after(`<span class="badge badge-danger badge-pill">${resp.unread.length}</span>`);
                } else {
                    $('.itinerary-inbox-content').html(`<li class="dropdown-item my-2 text-dark">新しい問い合わせがありません</li>`);
                    $(".itinerary-mailbox .badge").remove();
                }

                // if (unnotifiedTotal = resp.unnotified.length) {
                //     toastr.info(`お客様からの問い合わせを受信しました。`)
                // }
            }
        );
    };

    window.infoManager = new MemberStatusManager();
    infoManager.lastInquiryId = 0;
    updateMemberStatus();
    infoManager.setFunction('updateMemberStatus', updateMemberStatus);
    infoManager.run();

})(jQuery);
