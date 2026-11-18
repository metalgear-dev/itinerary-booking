(function($) {

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

        return {setFunction, getFunction, removeFunction, getFunctions, run, stop}

    };

    window.updateMemberStatus = function() {
        var $a = $('.ion-ios-help-buoy').parents('a');
        $.get(
            '/itinerary/member/updateInfo',
            {},
            function(resp) {
                var appliedTotal = resp.applied.length;
                var unreadTotal = resp.unread.length;
                var unnotifiedTotal = resp.unnotified.length;
                if (appliedTotal && $a.length) {
                    $a.find('.badge').remove();$a.find('.sr-only').remove();
                    $a.append(`<span class="badge badge-danger badge-pill p-1">${appliedTotal}</span><span class="sr-only">unreplied inquiries</span>`)
                }
                if (unnotifiedTotal) {
                    toastr.info(`${unnotifiedTotal}件の問い合わせが入ってきました`)
                }
            }
        );
    };

    window.infoManager = new MemberStatusManager();
    updateMemberStatus();
    infoManager.setFunction('updateMemberStatus', updateMemberStatus);
    infoManager.run();

})(jQuery);
