$(document).ready(function() {
    $('.btn-inquiry-detail').on('click', function() {
        var url = $(this).data('url');
        var $modal = $('#modalDetail');
        $.get(
            url,
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
                $modal.modal('show');
                updateMemberStatus();
            }
        )
    });

});
