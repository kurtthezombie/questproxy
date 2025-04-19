<script setup>
import { jsPDF } from "jspdf"

const generatePDF = () => {
  const doc = new jsPDF()

  const contractText = `
Game Service Agreement

Service Title: Diamond Rank Boost
Game: League of Legends
Category: Boosting
Pilot: PilotX (pilot_username)
Client: Gamer123 (client_username)
Booking ID: #1023
Agreement Date: April 19, 2025

1. Description of Service
The Pilot agrees to perform a Diamond Rank Boost for the client in League of Legends. The expected duration for completion is 3 days from the agreed start date.

2. Account Access
The client has provided the following credentials to the pilot to access their game account:

Username: myGameUser123
Password: (hidden/encrypted for storage)

The pilot agrees to use the account solely for the purpose of fulfilling this service and to log out immediately after each session.

3. Additional Instructions
Do not use any rare or legendary items.
Avoid playing in Stage 5.
Notify the client before starting each session.

4. Payment Terms
The client has agreed to pay $30 USD for the service, to be processed through the platform.

5. Confidentiality & Security
The pilot shall not disclose or share any of the client’s account information with any third party. The client understands and accepts the risks of account sharing as outlined in the platform’s policy.

6. Completion
The service is considered complete once the Diamond rank is achieved or after the duration has passed with significant progress. Upon completion, this agreement will be archived.

Client Signature: _____________________
Pilot Signature: ______________________
Date Signed: April 19, 2025
  `

  const lines = doc.splitTextToSize(contractText, 180)
  doc.setFont("Times", "Normal")
  doc.setFontSize(12)
  doc.text(lines, 10, 10)
  doc.save("game_service_agreement.pdf")
}
</script>

<template>
  <div class="p-4">
    <button
      @click="generatePDF"
      class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
    >
      Download Game Service Contract
    </button>
  </div>
</template>
