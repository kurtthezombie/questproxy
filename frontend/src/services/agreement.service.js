import api from "@/utils/api";
import dayjs from "dayjs";
import jsPDF from "jspdf";
import { ref } from 'vue';
import { fetchPilotNegotiations } from '@/services/negotiation.service';
import pdfMake from 'pdfmake/build/pdfmake';
import pdfFonts from 'pdfmake/build/vfs_fonts';
pdfMake.vfs = pdfFonts.vfs;

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
  const logoBase64 = qpLogoBase64.startsWith('data:image') ? qpLogoBase64 : `data:image/png;base64,${qpLogoBase64}`;
  const docDefinition = {
    pageMargins: [40, 60, 40, 60],
    header: {
      columns: [
        { image: logoBase64, width: 65, height: 65, margin: [0, 0, 8, 0], alignment: 'left' },
        { text: 'QuestProxy', style: 'headerBrand', color: '#22c55e', alignment: 'left', margin: [0, 25, 0, 0] },
      ],
      margin: [40, 10, 40, 0],
    },
    content: [
      {
        style: 'detailsBox',
        table: {
          widths: ['*'],
          body: [
            [
              {
                stack: [
                  { text: 'Key Details', style: 'detailsHeader' },
                  { text: `Service Title: ${details.description}` },
                  { text: `Game: ${details.category_title}` },
                  { text: `Pilot: ${details.pilot_username}` },
                  { text: `Client: ${details.client_username}` },
                  { text: `Booking ID: bk-${bookingId}` },
                  { text: `Agreement Date: ${dayjs().format('MMMM D, YYYY')}` },
                ],
                margin: [0, 0, 0, 0],
              }
            ]
          ]
        },
        layout: {
          hLineWidth: () => 0.5,
          vLineWidth: () => 0.5,
          hLineColor: () => '#22c55e',
          vLineColor: () => '#22c55e',
          paddingLeft: () => 10,
          paddingRight: () => 10,
          paddingTop: () => 8,
          paddingBottom: () => 8,
        },
        margin: [0, 30, 0, 20],
      },
      { text: '1. Description of Service', style: 'sectionHeader' },
      { text: `The Pilot agrees to perform a ${details.description} for the client in ${details.category_title}. The expected duration for completion is ${details.duration} day(s) from the agreed start date: ${form.start_date || dayjs().format('MMMM D, YYYY')}.`, style: 'sectionText' },
      { text: '2. Communication & Coordination', style: 'sectionHeader', margin: [0, 16, 0, 0] },
      { text: `The client has provided the following communication link for coordination with the pilot:\n\nChat Link: ${form.commLink}\n\nThe pilot agrees to use this communication link solely for the purpose of fulfilling this service and to maintain professionalism throughout the communication process.`, style: 'sectionText' },
      { text: '3. Additional Instructions', style: 'sectionHeader', margin: [0, 16, 0, 0] },
      { text: form.additional_notes || 'None', style: 'sectionText' },
      { text: '4. Payment Terms', style: 'sectionHeader', margin: [0, 16, 0, 0] },
      {
        text: `The client has agreed to pay ₱${form.negotiablePrice > 0 ? form.negotiablePrice : details.price} for the service, to be processed through the QuestProxy platform.` + (form.negotiablePrice > 0 ? ` Negotiated Price: ₱${form.negotiablePrice}.` : ''),
        style: 'sectionText',
      },
      { text: '5. Confidentiality & Security', style: 'sectionHeader', margin: [0, 16, 0, 0] },
      { text: `The pilot shall not disclose or share any of the client's account information with any third party. The client understands and accepts the risks of account sharing as outlined in the platform's policy.`, style: 'sectionText' },
      { text: '6. Completion', style: 'sectionHeader', margin: [0, 16, 0, 0] },
      { text: `The service is considered complete once the service is achieved or after the duration has passed with significant progress. Upon completion, this agreement will be archived.`, style: 'sectionText' },
    ],
    styles: {
      headerBrand: { fontSize: 20, bold: true },
      headerTitle: { fontSize: 16, bold: false },
      detailsBox: {},
      detailsHeader: { fontSize: 13, bold: true, color: '#22c55e', margin: [0, 0, 0, 8] },
      sectionHeader: { fontSize: 13, bold: true, color: '#22c55e', margin: [0, 16, 0, 4] },
      sectionText: { fontSize: 11, margin: [0, 0, 0, 8], alignment: 'left' },
      signatureHeader: { fontSize: 12, bold: true, margin: [0, 0, 0, 8] },
      signatureLine: { fontSize: 11, margin: [0, 0, 0, 0] },
      signatureDate: { fontSize: 10, italics: true, color: '#888' },
    },
    defaultStyle: {
      fontSize: 11,
      alignment: 'left',
    },
  };
  pdfMake.createPdf(docDefinition).download(`client_bk-${bookingId}_service_agreement.pdf`);
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
  const logoBase64 = qpLogoBase64.startsWith('data:image') ? qpLogoBase64 : `data:image/png;base64,${qpLogoBase64}`;
  const docDefinition = {
    pageMargins: [40, 60, 40, 60],
    header: {
      columns: [
        { image: logoBase64, width: 65, height: 65, margin: [0, 0, 8, 0], alignment: 'left' },
        { text: 'QuestProxy', style: 'headerBrand', color: '#22c55e', alignment: 'left', margin: [0, 25, 0, 0] },
      ],
      margin: [40, 10, 40, 0],
    },
    content: [
      {
        style: 'detailsBox',
        table: {
          widths: ['*'],
          body: [
            [
              {
                stack: [
                  { text: 'Key Details', style: 'detailsHeader' },
                  { text: `Service Title: ${details.description}` },
                  { text: `Game: ${details.category_title}` },
                  { text: `Pilot: ${details.pilot_username}` },
                  { text: `Client: ${details.client_username}` },
                  { text: `Booking ID: bk-${bookingId}` },
                  { text: `Agreement Date: ${dayjs().format('MMMM D, YYYY')}` },
                ],
                margin: [0, 0, 0, 0],
              }
            ]
          ]
        },
        layout: {
          hLineWidth: () => 0.5,
          vLineWidth: () => 0.5,
          hLineColor: () => '#22c55e',
          vLineColor: () => '#22c55e',
          paddingLeft: () => 10,
          paddingRight: () => 10,
          paddingTop: () => 8,
          paddingBottom: () => 8,
        },
        margin: [0, 30, 0, 20],
      },
      { text: '1. Description of Service', style: 'sectionHeader' },
      { text: `The Pilot agrees to perform a ${details.description} for the client in ${details.category_title}. The expected duration for completion is ${details.duration} day(s) from the agreed start date: ${form.start_date || dayjs().format('MMMM D, YYYY')}.`, style: 'sectionText' },
      { text: '2. Communication & Coordination', style: 'sectionHeader', margin: [0, 16, 0, 0] },
      { text: `The client has provided the following communication link for coordination with the pilot:\n\nChat Link: ${form.commLink}\n\nThe pilot agrees to use this communication link solely for the purpose of fulfilling this service and to maintain professionalism throughout the communication process.`, style: 'sectionText' },
      { text: '3. Additional Instructions', style: 'sectionHeader', margin: [0, 16, 0, 0] },
      { text: form.additional_notes || 'None', style: 'sectionText' },
      { text: '4. Payment Terms', style: 'sectionHeader', margin: [0, 16, 0, 0] },
      {
        text: `The client has agreed to pay ₱${form.negotiablePrice > 0 ? form.negotiablePrice : details.price} for the service, to be processed through the QuestProxy platform.` + (form.negotiablePrice > 0 ? ` Negotiated Price: ₱${form.negotiablePrice}.` : ''),
        style: 'sectionText',
      },
      { text: '5. Confidentiality & Security', style: 'sectionHeader', margin: [0, 16, 0, 0] },
      { text: `The pilot shall not disclose or share any of the client's account information with any third party. The client understands and accepts the risks of account sharing as outlined in the platform's policy.`, style: 'sectionText' },
      { text: '6. Completion', style: 'sectionHeader', margin: [0, 16, 0, 0] },
      { text: `The service is considered complete once the service is achieved or after the duration has passed with significant progress. Upon completion, this agreement will be archived.`, style: 'sectionText' },
    ],
    styles: {
      headerBrand: { fontSize: 20, bold: true },
      headerTitle: { fontSize: 16, bold: false },
      detailsBox: {},
      detailsHeader: { fontSize: 13, bold: true, color: '#22c55e', margin: [0, 0, 0, 8] },
      sectionHeader: { fontSize: 13, bold: true, color: '#22c55e', margin: [0, 16, 0, 4] },
      sectionText: { fontSize: 11, margin: [0, 0, 0, 8], alignment: 'left' },
      signatureHeader: { fontSize: 12, bold: true, margin: [0, 0, 0, 8] },
      signatureLine: { fontSize: 11, margin: [0, 0, 0, 0] },
      signatureDate: { fontSize: 10, italics: true, color: '#888' },
    },
    defaultStyle: {
      fontSize: 11,
      alignment: 'left',
    },
  };
  pdfMake.createPdf(docDefinition).download(`client_bk-${bookingId}_service_agreement.pdf`);
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

// PDFMake version of agreement PDF
const generatePDFMakeAgreement = async (serviceId, bookingId) => {
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
  const logoBase64 = qpLogoBase64.startsWith('data:image') ? qpLogoBase64 : `data:image/png;base64,${qpLogoBase64}`;
  const docDefinition = {
    pageMargins: [40, 60, 40, 60],
    header: {
      columns: [
        { image: logoBase64, width: 65, height: 65, margin: [0, 0, 8, 0], alignment: 'left' },
        { text: 'QuestProxy', style: 'headerBrand', color: '#22c55e', alignment: 'left', margin: [0, 25, 0, 0] },
      ],
      margin: [40, 10, 40, 0],
    },
    content: [
      {
        style: 'detailsBox',
        table: {
          widths: ['*'],
          body: [
            [
              {
                stack: [
                  { text: 'Key Details', style: 'detailsHeader' },
                  { text: `Service Title: ${details.description}` },
                  { text: `Game: ${details.category_title}` },
                  { text: `Pilot: ${details.pilot_username}` },
                  { text: `Client: ${details.client_username}` },
                  { text: `Booking ID: bk-${bookingId}` },
                  { text: `Agreement Date: ${dayjs().format('MMMM D, YYYY')}` },
                ],
                margin: [0, 0, 0, 0],
              }
            ]
          ]
        },
        layout: {
          hLineWidth: () => 0.5,
          vLineWidth: () => 0.5,
          hLineColor: () => '#22c55e',
          vLineColor: () => '#22c55e',
          paddingLeft: () => 10,
          paddingRight: () => 10,
          paddingTop: () => 8,
          paddingBottom: () => 8,
        },
        margin: [0, 30, 0, 20],
      },
      { text: '1. Description of Service', style: 'sectionHeader' },
      { text: `The Pilot agrees to perform a ${details.description} for the client in ${details.category_title}. The expected duration for completion is ${details.duration} day(s) from the agreed start date: ${form.start_date || dayjs().format('MMMM D, YYYY')}.`, style: 'sectionText' },
      { text: '2. Communication & Coordination', style: 'sectionHeader', margin: [0, 16, 0, 0] },
      { text: `The client has provided the following communication link for coordination with the pilot:\n\nChat Link: ${form.commLink}\n\nThe pilot agrees to use this communication link solely for the purpose of fulfilling this service and to maintain professionalism throughout the communication process.`, style: 'sectionText' },
      { text: '3. Additional Instructions', style: 'sectionHeader', margin: [0, 16, 0, 0] },
      { text: form.additional_notes || 'None', style: 'sectionText' },
      { text: '4. Payment Terms', style: 'sectionHeader', margin: [0, 16, 0, 0] },
      {
        text: `The client has agreed to pay ₱${form.negotiablePrice > 0 ? form.negotiablePrice : details.price} for the service, to be processed through the QuestProxy platform.` + (form.negotiablePrice > 0 ? ` Negotiated Price: ₱${form.negotiablePrice}.` : ''),
        style: 'sectionText',
      },
      { text: '5. Confidentiality & Security', style: 'sectionHeader', margin: [0, 16, 0, 0] },
      { text: `The pilot shall not disclose or share any of the client's account information with any third party. The client understands and accepts the risks of account sharing as outlined in the platform's policy.`, style: 'sectionText' },
      { text: '6. Completion', style: 'sectionHeader', margin: [0, 16, 0, 0] },
      { text: `The service is considered complete once the service is achieved or after the duration has passed with significant progress. Upon completion, this agreement will be archived.`, style: 'sectionText' },
    ],
    styles: {
      headerBrand: { fontSize: 20, bold: true },
      headerTitle: { fontSize: 16, bold: false },
      detailsBox: {},
      detailsHeader: { fontSize: 13, bold: true, color: '#22c55e', margin: [0, 0, 0, 8] },
      sectionHeader: { fontSize: 13, bold: true, color: '#22c55e', margin: [0, 16, 0, 4] },
      sectionText: { fontSize: 11, margin: [0, 0, 0, 8], alignment: 'left' },
      signatureHeader: { fontSize: 12, bold: true, margin: [0, 0, 0, 8] },
      signatureLine: { fontSize: 11, margin: [0, 0, 0, 0] },
      signatureDate: { fontSize: 10, italics: true, color: '#888' },
    },
    defaultStyle: {
      fontSize: 11,
      alignment: 'left',
    },
  };
  pdfMake.createPdf(docDefinition).download(`pilot_bk-${bookingId}_service_agreement.pdf`);
};

export { fetchData, createBooking, fetchPaymentUrl, generatePDF, generatePdfForClient, fetchPilotDecision, generatePDFMakeAgreement };
