import { useToast } from "vue-toastification";

const toast = useToast();

// Predefined toast options
const options = {
    position: "bottom-right",
    timeout: 5000,
    closeOnClick: true,
    pauseOnFocusLoss: true,
    pauseOnHover: true,
    draggable: true,
    draggablePercent: 0.6,
    showCloseButtonOnHover: false,
    hideProgressBar: false,
    closeButton: "button",
    icon: true,
    rtl: false
};

// Global toast functions
export default {
    success: (msg) => toast.success(msg, options),
    error: (msg) => toast.error(msg, options),
    info: (msg) => toast.info(msg, options),
    warning: (msg) => toast.warning(msg, options),
};
