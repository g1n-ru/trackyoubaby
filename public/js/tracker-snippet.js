(function () {
    'use strict';
    const TRACKER_BASE_URL = window.TRACKER_BASE_URL || '';
    const MAX_RETRIES = 3;
    const RETRY_DELAY = 1000;

    function getCookie(name) {
        const matches = document.cookie.match(
            new RegExp('(?:^|; )' + name.replace(/([.$?*|{}()[\]\\/+^])/g, '\\$1') + '=([^;]*)')
        );
        return matches ? decodeURIComponent(matches[1]) : null;
    }

    function getYmUid() {
        if (!window.ym) {
            return null;
        }
        for (const cookie of document.cookie.split(';')) {
            const trimmed = cookie.trim();
            if (trimmed.startsWith('_ym_uid=')) {
                return trimmed.substring('_ym_uid='.length);
            }
        }
        return null;
    }

    function sendClientId(clickId, ymUid, retryCount = 0) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', TRACKER_BASE_URL + '/clientid', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('Accept', 'application/json');

        xhr.onreadystatechange = () => {
            if (xhr.readyState !== 4) {
                return;
            }
            if (xhr.status >= 200 && xhr.status < 300) {
                return;
            }
            if (retryCount < MAX_RETRIES) {
                setTimeout(() => {
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
        const clickId = getCookie('click_id');
        if (!clickId) {
            return;
        }
        let attempts = 0;
        const maxAttempts = 10;
        const checkInterval = 500;
        const intervalId = setInterval(() => {
            attempts++;
            const ymUid = getYmUid();
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
