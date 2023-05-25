import axios from "axios";

const http = axios.create({
  baseURL: process.env.MIX_HOST || 'http://localhost:8001/'
})

export default http;
