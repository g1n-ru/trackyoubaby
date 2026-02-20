<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page Example</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 40px 20px; }
        h1 { color: #333; }
        .info { background: #f5f5f5; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .btn { display: inline-block; background: #6c5ce7; color: #fff; padding: 12px 24px; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; text-decoration: none; }
        .btn:hover { background: #5f4fcf; }
        code { background: #eee; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>Landing Page Example</h1>

    <div class="info">
        <p>This is an example landing page with Yandex Metrica and tracker snippet integration.</p>
        <p>Click ID from cookie: <strong id="click-id-display">-</strong></p>
    </div>

    <a href="#" class="btn" id="convert-btn">Convert</a>

    <!-- Yandex.Metrika counter -->
    <script type="text/javascript">
        (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
        m[i].l=1*new Date();
        for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
        k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

        ym({{ Config::get('tracker.metrica.counter_id') ?: 0 }}, "init", {
            clickmap: true,
            trackLinks: true,
            accurateTrackBounce: true,
            webvisor: true
        });
    </script>
    <!-- /Yandex.Metrika counter -->

    <!-- Tracker snippet -->
    <script>
        window.TRACKER_BASE_URL = '{{ rtrim(Config::get('app.url'), '/') }}';
    </script>
    <script src="/js/tracker-snippet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var clickIdDisplay = document.getElementById('click-id-display');
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = cookies[i].trim();
                if (cookie.indexOf('click_id=') === 0) {
                    clickIdDisplay.textContent = cookie.substring('click_id='.length);
                    break;
                }
            }

            document.getElementById('convert-btn').addEventListener('click', function (e) {
                e.preventDefault();
                var clickId = clickIdDisplay.textContent;
                if (!clickId || clickId === '-') {
                    alert('No click_id found');
                    return;
                }

                var xhr = new XMLHttpRequest();
                xhr.open('POST', window.TRACKER_BASE_URL + '/conversion', true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status >= 200 && xhr.status < 300) {
                        alert('Conversion recorded!');
                    }
                };
                xhr.send(JSON.stringify({
                    click_id: clickId,
                    revenue: 100,
                    currency: 'RUB'
                }));
            });
        });
    </script>
</body>
</html>
