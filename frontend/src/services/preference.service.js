import api from "@/utils/api";

const createPreference = async (data) => {
   const req = await api.post(`/preferences/`, data);
   
   return req;
}

const getPreference = async (user_id) => {
   const req = await api.get(`/preferences/${user_id}`);
   return req;
}

const updatePreference = async (data) => {
   const req = await api.put(`/preferences/`, data);
   return req;
}

export { createPreference, getPreference, updatePreference };