import api from '@/utils/api'; // Adjust if your API utility is elsewhere

// Send a negotiation offer (gamer)
export async function sendNegotiation(bookingId, negotiablePrice) {
  return api.post(`/bookings/${bookingId}/negotiate`, { negotiable_price: negotiablePrice });
}

// Fetch all pending negotiations for the pilot
export async function fetchPilotNegotiations() {
  return api.get('/pilot/negotiations');
}

// Approve a negotiation (pilot)
export async function approveNegotiation(negotiationId) {
  return api.post(`/negotiations/${negotiationId}/approve`);
}

// Decline a negotiation (pilot)
export async function declineNegotiation(negotiationId) {
  return api.post(`/negotiations/${negotiationId}/decline`);
}
