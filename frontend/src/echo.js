import axios from 'axios';
import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

const useEcho = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    authorizer: (channel) => {
        return {
            authorize: (socketId, callback) => {
                axios.post('http://127.0.0.1:8000/api/broadcasting/auth', {
                    socket_id: socketId,
                    channel_name: channel.name
                },{
                    headers: {
                        'Authorization': `Bearer 7|iqL4jC5IYYOyBnqviIq7K5WeI8fdse5ipULaWduF2aa59aa3` //replace with stored token after login
                    }
                })
                .then(response => {
                    console.log('Authorized successfully:', response.data);
                    callback(false, response.data);
                })
                .catch(error => {
                    console.log('Authorized failed:', error);
                    callback(true, error);
                });
            }
        };
    },
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
    debug: true,
});

export default useEcho
