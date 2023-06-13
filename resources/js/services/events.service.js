
import http from "../axios";

const EventService = {
    async getAll() {
        try {
            const response = await http.get(`events`);
            return response.data;
        } catch (error) {
            throw new Error(`[EventService] Get All Lists | ${error}`);
        }
    },
    async deleteById(id) {
        try {
            const response = await http.delete(`events/${id}`);
            return response.data;
        } catch (error) {
            throw new Error(`[EventService] Delete by ID | ${error}`);
        }
    },
    async create(data) {
        try {
            const response = await http.post(`events/`,data);
            return response.data;
        } catch (error) {
            throw new Error(`[EventService] Create | ${error}`);
        }
    },
    async update(data) {
        try {
            const response = await http.patch(`events/${data.id}`,data);
            return response.data;
        } catch (error) {
            throw new Error(`[EventService] Create | ${error}`);
        }
    },
    async getById(id) {
        try {
            const response = await http.get(`events/${id}`);
            return response.data;
        } catch (error) {
            throw new Error(`[EventService] get | ${error}`);
        }
    },
}
export default EventService

