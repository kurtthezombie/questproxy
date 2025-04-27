import api from "@/utils/api";
import jsPDF from "jspdf";

const fetchData = async (id) => {
  const response = await api.get(`/services/${id}/details`);

  return response.service;
};

const createBooking = async (data) => {
  const response = await api.post(`/bookings/store`, data);
  console.log('response: ', response);
  return response.booking;
}

const fetchPaymentUrl = async (cancelUrl, bookingId) => {
  const formData = {
    cancel_url: cancelUrl,
  }
  const response = await api.post(`/payments/${bookingId}`, formData);
  console.log('response: ', response);
  return response.checkout_url;
}

//generate the pdf
const generatePDF = async (serviceId, bookingId, form) => {
  const details = await fetchData(serviceId);


  console.log("details: ", details);
  if (!details || !serviceId || !bookingId) {
    console.error("Data not loaded yet!");
    return;
  }

  const doc = new jsPDF();
  const currentDate = new Date().toLocaleDateString();
  const contractText = `
Game Service Agreement

Service Title: ${details.description}
Game: ${details.category_title}
Pilot: ${details.pilot_username}
Client: ${details.client_username}
Booking ID: bk-${bookingId}
Agreement Date: ${currentDate}

1. Description of Service
The Pilot agrees to perform a ${details.description} for the client in ${details.category_title}. The expected duration for completion is ${details.duration} day(s) from the agreed start date.

2. Communication & Coordination
The client has provided the following communication link for coordination with the pilot:

Chat Link: ${form.commLink}

The pilot agrees to use this communication link solely for the purpose of fulfilling this service and to maintain professionalism throughout the communication process.

3. Additional Instructions
${form.additional_notes}

4. Payment Terms
The client has agreed to pay PHP ${details.price} for the service, to be processed through the platform.

5. Confidentiality & Security
The pilot shall not disclose or share any of the client’s account information with any third party. The client understands and accepts the risks of account sharing as outlined in the platform’s policy.

6. Completion
The service is considered complete once the service is achieved or after the duration has passed with significant progress. Upon completion, this agreement will be archived.

Date Agreed: ${currentDate}
  `;

  const lines = doc.splitTextToSize(contractText, 180);
  doc.setFont("Times", "Normal");
  doc.setFontSize(12);
  doc.text(lines, 10, 10);
  doc.save(`pilot_bk-${bookingId}_agreement.pdf`);
};

export { fetchData, createBooking, fetchPaymentUrl, generatePDF };