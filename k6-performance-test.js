import http from 'k6/http';
import { check, sleep } from 'k6';
import { Rate } from 'k6/metrics';

const errorRate = new Rate('errors');

export const options = {
  stages: [
    { duration: '30s', target: 50 },
    { duration: '1m', target: 100 },
    { duration: '30s', target: 200 },
    { duration: '1m', target: 200 },
    { duration: '30s', target: 0 },
  ],
  thresholds: {
    errors: ['rate<0.1'],
    http_req_duration: ['p(95)<500'],
  },
};

const BASE_URL = 'http://localhost';

export function setup() {
  // Login and get auth token
  const loginRes = http.post(`${BASE_URL}/api/auth/login`, JSON.stringify({
    email: 'test@example.com',
    password: 'password',
  }), {
    headers: { 'Content-Type': 'application/json' },
  });

  const token = loginRes.json('token');
  return { token };
}

export default function (data) {
  const headers = {
    'Authorization': `Bearer ${data.token}`,
    'Content-Type': 'application/json',
  };

  // Get conversations
  let res = http.get(`${BASE_URL}/api/conversations`, { headers });
  check(res, {
    'conversations loaded': (r) => r.status === 200,
  }) || errorRate.add(1);

  sleep(1);

  // Get messages
  const conversationId = 1;
  res = http.get(`${BASE_URL}/api/conversations/${conversationId}/messages`, { headers });
  check(res, {
    'messages loaded': (r) => r.status === 200,
  }) || errorRate.add(1);

  sleep(1);

  // Send message
  res = http.post(
    `${BASE_URL}/api/conversations/${conversationId}/messages`,
    JSON.stringify({ content: 'Performance test message' }),
    { headers }
  );
  check(res, {
    'message sent': (r) => r.status === 201,
  }) || errorRate.add(1);

  sleep(2);

  // Search messages
  res = http.get(`${BASE_URL}/api/search/messages?q=test`, { headers });
  check(res, {
    'search completed': (r) => r.status === 200,
  }) || errorRate.add(1);

  sleep(1);
}
