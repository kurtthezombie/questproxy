import api from '@/utils/api';
import { callWithErrorHandling } from 'vue';

const fetchTransactions = async (page = 1, searchQuery = '') => {
      try {
            const response = await api.get(`/transactions?page=${page}&search=${searchQuery}`);

            return response.transactions || {};
      } catch (error) {
            console.error("Error fetching transactions: ", error);
            throw error;
      }
}

const exportTransactions = async () => {
      try {
            //treat as a blob
            const response = await api.get('/export-transaction-history', {
                  responseType: 'blob',
            });

            //create blob from the response data
            const blob = new Blob([response], { 
                  type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' 
            });

            //create download link
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'transaction_history.xlsx';
            
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
      } catch (error) {
            console.error("Error exporting transactions: ", error);
            throw error;
      }
}

export { fetchTransactions, exportTransactions };