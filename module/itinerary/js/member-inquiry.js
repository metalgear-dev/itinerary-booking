$(document).ready(function() {
    var bindEventHandlers = function() {
        $('.btn-inquiry-detail').on('click', function() {
            var $this = $(this);
            var id = $this.data('id');
            var $modal = $('#modalInquiryDetail');
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
                }
            )
        });
    };
    bindEventHandlers();
    var refreshInquiryTable = function() {
        $.post(
            location.href,
            {
                _token: $("input[name=_token]").val()
            },
            function(res) {
                $('#panelInquiryTable').html(res);
                bindEventHandlers();
            }
        )
    };
    // infoManager.setFunction('refreshInquiryTable', refreshInquiryTable);

    // get maximum inquiry id and set cookie
    let input_array = $("#panelInquiryTable tbody td input.check-item").toArray();
    let max_id = 0;
    input_array.forEach(function(ipnut_item, index){
        if(parseInt(ipnut_item["value"]) > max_id)
            max_id = parseInt(ipnut_item["value"]);});

    if(max_id > parseInt(getCookie('max_inquiry_id')))
        document.cookie = `max_inquiry_id = ${max_id}`;

    // get maximum inquiry id from server
    let getUpdateStatus = function() {
        $.post(
            '/itinerary/member/inquiry/getUpdateStatus',
            {
                cur_id : parseInt(getCookie('max_inquiry_id'))
            },
            function(resp) {
                if(resp.updated){
                    refreshInquiryTable();
                    document.cookie = `max_inquiry_id = ${resp.max_id}`;
                }
            }
        )
    };

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


    infoManager.setFunction('getUpdateStatus', getUpdateStatus);
});
