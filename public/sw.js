self.addEventListener("push", (event) => {

    notification = event.data.json();
//{"title":"Hi", "body":"check this out", "url":"/?message"}
    event.waitUntil(self.registration.showNotification(notification.title , {
        body: notification.body,
        icon: "icon.jpg",
        data: {
            notifURL: notification.URL
        }
    }));
});
self.addEventListener("notificationClick", (event)=>
{
    event.waitUntil(clients.openWindow(event.notification.data.json));
});
