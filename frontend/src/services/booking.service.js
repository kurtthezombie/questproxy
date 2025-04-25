import api from "@/utils/api"
import toast from "@/utils/toast";


const fetchMyBookings = async () => {
  try {
    const response = await api.get("/bookings/my-bookings")
    console.log("Bookings fetched successfully:", response.bookings);
    return response.bookings;
  } catch (error) {
    console.error("Error fetching bookings:", error);
    toast.error("Failed to fetch bookings. Please try again later.");
  }
}

export { fetchMyBookings }