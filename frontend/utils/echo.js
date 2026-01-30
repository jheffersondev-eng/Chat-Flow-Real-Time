// frontend/utils/echo.js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

if (typeof window !== 'undefined') {
  window.Pusher = Pusher;
}

const echo = new Echo({
  broadcaster: 'pusher',
  key: process.env.NEXT_PUBLIC_PUSHER_APP_KEY || '854aaeec9943a6c0b494', // .env ou fallback
  cluster: process.env.NEXT_PUBLIC_PUSHER_APP_CLUSTER || 'us2',
  forceTLS: true,
  encrypted: true,
});

export default echo;
