<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>push notification</title>
</head>

<body>
    <script>
        navigator.serviceWorker.register("sw.js");

        function requestPermission() {
            Notification.requestPermission().then((permission) => {
                if (permission === 'granted') {
                    //get service worker
                    navigator.serviceWorker.ready.then((sw) => {
                        // subscribe
                        sw.pushManager.subscribe({
                            userVisibleOnly: true,
                            applicationServerKey: "MFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAEi0j7GOnQdGdeRoaQvyd+HVgfbtf"
                        }).then((subscription) => {
                            // subscription successfull
                            fetch("/api/push-subscribe", {
                                method: "post",
                                body: JSON.stringify(subscription)
                            }).then(alert("ok"));
                        });
                    });
                }
            });
        }
    </script>
    <button onclick="requestPermission()">Enable Notification</button>
</body>

</html>
