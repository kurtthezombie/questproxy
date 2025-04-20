import api from "@/utils/api";

const fetchData = async () => {
  // const response = await api.get(`/bookings/${booking_id}/service-details`);
  // console.log(response);
  const details = {
      description: 'Valorant Rank Boosting',
      category_title: 'Rank Boost',
      pilot_username: 'tester',
      client_username: 'young_thug',
      price: 250,
      duration: 2
  };
  return details;
};

export { fetchData };