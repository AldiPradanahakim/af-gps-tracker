import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "pusher",

    key: import.meta.env.VITE_PUSHER_APP_KEY,

    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,

    wsHost: import.meta.env.VITE_PUSHER_HOST || undefined,

    wsPort: Number(import.meta.env.VITE_PUSHER_PORT || 6001),

    wssPort: Number(import.meta.env.VITE_PUSHER_PORT || 6001),

    forceTLS: import.meta.env.VITE_PUSHER_SCHEME === "https",

    enabledTransports: ["ws", "wss"],

    disableStats: true,

    activityTimeout: 30000,

    authEndpoint: "/broadcasting/auth",

    auth: {
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content"),
        },
    },
});
