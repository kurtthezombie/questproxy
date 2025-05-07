import api from "@/utils/api";
import dayjs from "dayjs";
import jsPDF from "jspdf";
import { ref } from 'vue';
import { fetchPilotNegotiations } from '@/services/negotiation.service';

import { qpLogoBase64 } from '@/assets/img/logoData.js';

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

  if (!details || !serviceId || !bookingId) {
    console.error("Data not loaded yet!");
    return;
  }

  const doc = new jsPDF();
  let yPos = 15;
  const margin = 15;
  const contentWidth = doc.internal.pageSize.getWidth() - 2 * margin;
  const lineHeight = 7;
  const pageHeight = doc.internal.pageSize.getHeight();
  const bottomMargin = 15;

  const checkPageBreak = (requiredSpace) => {
    if (yPos + requiredSpace > pageHeight - bottomMargin) {
      doc.addPage();
      yPos = margin;
    }
  };

  doc.setFont("Times", "Normal");
  doc.setFontSize(12);

  const logoBase64 = qpLogoBase64;
  const logoWidth = 25;
  const logoHeight = 25;
  const logoX = (doc.internal.pageSize.getWidth() / 2) - (logoWidth / 2);
  const logoY = margin;

  if (logoBase64) {
    try {
      doc.addImage(logoBase64, 'PNG', logoX, logoY, logoWidth, logoHeight);
      yPos = logoY + logoHeight + 2;
      doc.setFontSize(20);
      doc.setFont("helvetica", "bold");
      doc.setTextColor(34, 197, 94);
      doc.text("QuestProxy", doc.internal.pageSize.getWidth() / 2, yPos, { align: 'center' });
      yPos += 8;
      doc.setFontSize(16);
      doc.setFont("times", "normal");
      doc.setTextColor(0, 0, 0);
    } catch (error) {
      console.error("Error adding logo image:", error);
      doc.text(" [Logo failed to load] ", margin, yPos);
      yPos += 25;
    }
  } else {
    doc.text(" [Your Logo Here - Paste Base64 string or check import] ", margin, yPos);
    yPos += 25;
  }

  checkPageBreak(20);
  doc.setFontSize(18);
  doc.text("Game Service Agreement", doc.internal.pageSize.getWidth() / 2, yPos, { align: 'center' });
  yPos += 20;

  doc.setFontSize(12);

  checkPageBreak(7 * lineHeight + 10);
  doc.setFont("Times", "Bold");
  doc.text("Key Details:", margin, yPos);
  doc.setFont("Times", "Normal");
  yPos += 8;
  doc.text(`Service Title: ${details.description}`, margin, yPos);
  yPos += 8;
  doc.text(`Game: ${details.category_title}`, margin, yPos);
  yPos += 8;
  doc.text(`Pilot: ${details.pilot_username}`, margin, yPos);
  yPos += 8;
  doc.text(`Client: ${details.client_username}`, margin, yPos);
  yPos += 8;
  doc.text(`Booking ID: bk-${bookingId}`, margin, yPos);
  yPos += 8;
  doc.text(`Agreement Date: ${dayjs().format('MMMM D,YYYY')}`, margin, yPos);
  yPos += 20;

  checkPageBreak(3 * lineHeight + 10);
  doc.setFont("Times", "Bold");
  doc.text("1. Description of Service", margin, yPos);
  doc.setFont("Times", "Normal");
  yPos += 8;
  const descriptionText = `The Pilot agrees to perform a ${details.description} for the client in ${details.category_title}.
  The expected duration for completion is ${details.duration} day(s) from the agreed start date:
    ${dayjs(form.start_date).format('MMMM D,YYYY')}.`;
  const descriptionLines = doc.splitTextToSize(descriptionText, contentWidth);
  checkPageBreak((descriptionLines.length * lineHeight) + 10);
  doc.text(descriptionLines, margin, yPos);
  yPos += (descriptionLines.length * lineHeight) + 20;

  checkPageBreak(4 * lineHeight + 10);
  doc.setFont("Times", "Bold");
  doc.text("2. Communication & Coordination", margin, yPos);
  doc.setFont("Times", "Normal");
  yPos += 8;
  const commText = `The client has provided the following communication link for coordination with the pilot:\n\nChat Link: ${form.commLink}\n\nThe pilot agrees to use this communication link solely for the purpose of fulfilling this service and to maintain professionalism throughout the communication process.`;
  const commLines = doc.splitTextToSize(commText, contentWidth);
  checkPageBreak((commLines.length * lineHeight) + 10);
  doc.text(commLines, margin, yPos);
  yPos += (commLines.length * lineHeight) + 20;

  const notesText = form.additional_notes || 'None';
  const notesLines = doc.splitTextToSize(notesText, contentWidth);
  checkPageBreak((notesLines.length * lineHeight) + 8 + 10);
  doc.setFont("Times", "Bold");
  doc.text("3. Additional Instructions", margin, yPos);
  doc.setFont("Times", "Normal");
  yPos += 8;
  doc.text(notesLines, margin, yPos);
  yPos += (notesLines.length * lineHeight) + 20;

  checkPageBreak(2 * lineHeight + 10);
  doc.setFont("Times", "Bold");
  doc.text("4. Payment Terms", margin, yPos);
  doc.setFont("Times", "Normal");
  yPos += 8;
  let paymentText = `The client has agreed to pay ₱${form.negotiablePrice > 0 ? form.negotiablePrice : details.price} for the service,\nto be processed through the QuestProxy platform.`;
  if (form.negotiablePrice > 0) {
    paymentText += ` Negotiated Price: ₱${form.negotiablePrice}.`;
  }
  const paymentLines = doc.splitTextToSize(paymentText, contentWidth);
  doc.text(paymentLines, margin, yPos);
  yPos += (paymentLines.length * lineHeight) + 20;

  checkPageBreak(3 * lineHeight + 10);
  doc.setFont("Times", "Bold");
  doc.text("5. Confidentiality & Security", margin, yPos);
  doc.setFont("Times", "Normal");
  yPos += 8;
  const securityText = `The pilot shall not disclose or share any of the client's account information with any third party. The client understands and accepts the risks of account sharing as outlined in the platform's policy.`;
  const securityLines = doc.splitTextToSize(securityText, contentWidth);
  checkPageBreak((securityLines.length * lineHeight) + 10);
  doc.text(securityLines, margin, yPos);
  yPos += (securityLines.length * lineHeight) + 20;

  checkPageBreak(3 * lineHeight + 10);
  doc.setFont("Times", "Bold");
  doc.text("6. Completion", margin, yPos);
  doc.setFont("Times", "Normal");
  yPos += 8;
  const completionText = `The service is considered complete once the service is achieved or after the duration has passed with significant progress. Upon completion, this agreement will be archived.`;
  const completionLines = doc.splitTextToSize(completionText, contentWidth);
  checkPageBreak((completionLines.length * lineHeight) + 10);
  doc.text(completionLines, margin, yPos);
  yPos += (completionLines.length * lineHeight) + 20;

  checkPageBreak(15);
  doc.text(`Date Agreed: ${dayjs().format('MMMM D,YYYY')}`, margin, yPos);
  yPos += 20;

  doc.save(`pilot_bk-${bookingId}_service_agreement.pdf`);
};

//generate pdf for client
const generatePdfForClient = async (serviceId, bookingId) => {
  const details = await fetchData(serviceId);
  const instructions = await fetchInstructions(bookingId);

  if (!details || !serviceId || !bookingId) {
    console.error("Data not loaded yet!");
    return;
  }

  const form = {
    commLink: instructions.communication_link || "N/A",
    additional_notes: instructions.additional_notes || "N/A",
    start_date: dayjs(instructions.start_date).format('MMMM D, YYYY') || "N/A",
    negotiablePrice: instructions.negotiable_price || null,
  };

  const doc = new jsPDF();
  let yPos = 15;
  const margin = 15;
  const contentWidth = doc.internal.pageSize.getWidth() - 2 * margin;
  const lineHeight = 7;
  const pageHeight = doc.internal.pageSize.getHeight();
  const bottomMargin = 15;

  const checkPageBreak = (requiredSpace) => {
    if (yPos + requiredSpace > pageHeight - bottomMargin) {
      doc.addPage();
      yPos = margin;
    }
  };

  doc.setFont("Times", "Normal");
  doc.setFontSize(12);

  const logoBase64 = qpLogoBase64;
  const logoWidth = 25;
  const logoHeight = 25;
  const logoX = (doc.internal.pageSize.getWidth() / 2) - (logoWidth / 2);
  const logoY = margin;

  if (logoBase64) {
    try {
      doc.addImage(logoBase64, 'PNG', logoX, logoY, logoWidth, logoHeight);
      yPos = logoY + logoHeight + 2;
      doc.setFontSize(20);
      doc.setFont("helvetica", "bold");
      doc.setTextColor(34, 197, 94);
      doc.text("QuestProxy", doc.internal.pageSize.getWidth() / 2, yPos, { align: 'center' });
      yPos += 8;
      doc.setFontSize(16);
      doc.setFont("times", "normal");
      doc.setTextColor(0, 0, 0);
    } catch (error) {
      console.error("Error adding logo image:", error);
      doc.text(" [Logo failed to load] ", margin, yPos);
      yPos += 25;
    }
  } else {
    doc.text(" [Your Logo Here - Paste Base64 string or check import] ", margin, yPos);
    yPos += 25;
  }

  checkPageBreak(20);
  doc.setFontSize(18);
  doc.text("Game Service Agreement", doc.internal.pageSize.getWidth() / 2, yPos, { align: 'center' });
  yPos += 20;

  doc.setFontSize(12);

  checkPageBreak(7 * lineHeight + 10);
  doc.setFont("Times", "Bold");
  doc.text("Key Details:", margin, yPos);
  doc.setFont("Times", "Normal");
  yPos += 8;
  doc.text(`Service Title: ${details.description}`, margin, yPos);
  yPos += 8;
  doc.text(`Game: ${details.category_title}`, margin, yPos);
  yPos += 8;
  doc.text(`Pilot: ${details.pilot_username}`, margin, yPos);
  yPos += 8;
  doc.text(`Client: ${details.client_username}`, margin, yPos);
  yPos += 8;
  doc.text(`Booking ID: bk-${bookingId}`, margin, yPos);
  yPos += 8;
  doc.text(`Agreement Date: ${dayjs().format('MMMM D,YYYY')}`, margin, yPos);
  yPos += 20;

  checkPageBreak(3 * lineHeight + 10);
  doc.setFont("Times", "Bold");
  doc.text("1. Description of Service", margin, yPos);
  doc.setFont("Times", "Normal");
  yPos += 8;
  const descriptionText = `The Pilot agrees to perform a ${details.description} for the client in ${details.category_title}.
   The expected duration for completion is ${details.duration} day(s) from the agreed start date:
    ${form.start_date}.`;
  const descriptionLines = doc.splitTextToSize(descriptionText, contentWidth);
  checkPageBreak((descriptionLines.length * lineHeight) + 10);
  doc.text(descriptionLines, margin, yPos);
  yPos += (descriptionLines.length * lineHeight) + 20;

  checkPageBreak(4 * lineHeight + 10);
  doc.setFont("Times", "Bold");
  doc.text("2. Communication & Coordination", margin, yPos);
  doc.setFont("Times", "Normal");
  yPos += 8;
  const commText = `The client has provided the following communication link for coordination with the pilot:\n\nChat Link: ${form.commLink}\n\nThe pilot agrees to use this communication link solely for the purpose of fulfilling this service and to maintain professionalism throughout the communication process.`;
  const commLines = doc.splitTextToSize(commText, contentWidth);
  checkPageBreak((commLines.length * lineHeight) + 10);
  doc.text(commLines, margin, yPos);
  yPos += (commLines.length * lineHeight) + 20;

  const notesText = form.additional_notes || 'None';
  const notesLines = doc.splitTextToSize(notesText, contentWidth);
  checkPageBreak((notesLines.length * lineHeight) + 8 + 10);
  doc.setFont("Times", "Bold");
  doc.text("3. Additional Instructions", margin, yPos);
  doc.setFont("Times", "Normal");
  yPos += 8;
  doc.text(notesLines, margin, yPos);
  yPos += (notesLines.length * lineHeight) + 20;

  checkPageBreak(2 * lineHeight + 10);
  doc.setFont("Times", "Bold");
  doc.text("4. Payment Terms", margin, yPos);
  doc.setFont("Times", "Normal");
  yPos += 8;
  let paymentText = `The client has agreed to pay ₱${form.negotiablePrice > 0 ? form.negotiablePrice : details.price} for the service,\nto be processed through the QuestProxy platform.`;
  if (form.negotiablePrice > 0) {
    paymentText += ` Negotiated Price: ₱${form.negotiablePrice}.`;
  }
  const paymentLines = doc.splitTextToSize(paymentText, contentWidth);
  doc.text(paymentLines, margin, yPos);
  yPos += (paymentLines.length * lineHeight) + 20;

  checkPageBreak(3 * lineHeight + 10);
  doc.setFont("Times", "Bold");
  doc.text("5. Confidentiality & Security", margin, yPos);
  doc.setFont("Times", "Normal");
  yPos += 8;
  const securityText = `The pilot shall not disclose or share any of the client's account information with any third party. The client understands and accepts the risks of account sharing as outlined in the platform's policy.`;
  const securityLines = doc.splitTextToSize(securityText, contentWidth);
  checkPageBreak((securityLines.length * lineHeight) + 10);
  doc.text(securityLines, margin, yPos);
  yPos += (securityLines.length * lineHeight) + 20;

  checkPageBreak(3 * lineHeight + 10);
  doc.setFont("Times", "Bold");
  doc.text("6. Completion", margin, yPos);
  doc.setFont("Times", "Normal");
  yPos += 8;
  const completionText = `The service is considered complete once the service is achieved or after the duration has passed with significant progress. Upon completion, this agreement will be archived.`;
  const completionLines = doc.splitTextToSize(completionText, contentWidth);
  checkPageBreak((completionLines.length * lineHeight) + 10);
  doc.text(completionLines, margin, yPos);
  yPos += (completionLines.length * lineHeight) + 20;

  checkPageBreak(15);
  doc.text(`Date Agreed: ${dayjs().format('MMMM D,YYYY')}`, margin, yPos);
  yPos += 20;

  doc.save(`client_bk-${bookingId}_service_agreement.pdf`);
};

const fetchInstructions = async (bookingId) => {
  const response = await api.get(`bookings/instructions/${bookingId}`);

  return response.instruction;
}

// You will need a new function to fetch the pilot's decision
const fetchPilotDecision = async (bookingId) => {
  // This endpoint needs to be created in your backend
  const response = await api.get(`/bookings/${bookingId}/pilot-decision`);
  return response.data; // Assuming the response contains a status like { status: 'approved' } or { status: 'declined' }
}

const negotiations = ref([]);

const showNegotiations = async () => {
  activeTab.value = 'negotiations';
  // Fetch negotiations for the pilot
  if (role.value === 'game pilot') {
    const response = await fetchPilotNegotiations();
    negotiations.value = response.data; // Adjust if your API returns differently
  } else {
    // For gamers, fetch their own negotiations (implement a similar endpoint if needed)
    // negotiations.value = await fetchGamerNegotiations();
  }
};


export { fetchData, createBooking, fetchPaymentUrl, generatePDF, generatePdfForClient, fetchPilotDecision };
