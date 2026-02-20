(function () {
    'use strict';

    var TRACKER_BASE_URL = window.TRACKER_BASE_URL || '';
    var MAX_RETRIES = 3;
    var RETRY_DELAY = 1000;

    function getCookie(name) {
        var matches = document.cookie.match(
            new RegExp('(?:^|; )' + name.replace(/([.$?*|{}()[\]\\/+^])/g, '\\$1') + '=([^;]*)')
        );
        return matches ? decodeURIComponent(matches[1]) : null;
    }

    function getYmUid() {
        var metrikaCounter = window.ym;
        if (!metrikaCounter) {
            return null;
        }

        var cookies = document.cookie.split(';');
        for (var i = 0; i < cookies.length; i++) {
            var cookie = cookies[i].trim();
            if (cookie.indexOf('_ym_uid=') === 0) {
                return cookie.substring('_ym_uid='.length);
            }
        }

        return null;
    }

    function sendClientId(clickId, ymUid, retryCount) {
        retryCount = retryCount || 0;

        var xhr = new XMLHttpRequest();
        xhr.open('POST', TRACKER_BASE_URL + '/clientid', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('Accept', 'application/json');

        xhr.onreadystatechange = function () {
            if (xhr.readyState !== 4) {
                return;
            }

            if (xhr.status >= 200 && xhr.status < 300) {
                return;
            }

            if (retryCount < MAX_RETRIES) {
                setTimeout(function () {
                    sendClientId(clickId, ymUid, retryCount + 1);
                }, RETRY_DELAY * Math.pow(2, retryCount));
            }
        };

        xhr.send(JSON.stringify({
            click_id: clickId,
            ym_uid: ymUid
        }));
    }

    function init() {
        var clickId = getCookie('click_id');
        if (!clickId) {
            return;
        }

        var attempts = 0;
        var maxAttempts = 10;
        var checkInterval = 500;

        var intervalId = setInterval(function () {
            attempts++;
            var ymUid = getYmUid();

            if (ymUid) {
                clearInterval(intervalId);
                sendClientId(clickId, ymUid);
                return;
            }

            if (attempts >= maxAttempts) {
                clearInterval(intervalId);
            }
        }, checkInterval);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
