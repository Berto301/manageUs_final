
import http from "../axios";

const TeamsService = {
    async getAll() {
        try {
            const response = await http.get(`teams`);
            return response.data;
        } catch (error) {
            throw new Error(`[TeamsService] Get All Lists | ${error}`);
        }
    },
    async deleteById(id) {
        try {
            const response = await http.delete(`teams/${id}`);
            return response.data;
        } catch (error) {
            throw new Error(`[TeamsService] Delete by ID | ${error}`);
        }
    },
    async create(data) {
        try {
            const response = await http.post(`teams/create`,data);
            return response.data;
        } catch (error) {
            throw new Error(`[TeamsService] Create | ${error}`);
        }
    },
    async addMember(data) {
        try {
            const response = await http.post(`teams/createMember`,data);
            return response.data;
        } catch (error) {
            throw new Error(`[TeamsService] createMember | ${error}`);
        }
    },
    async update(data) {
        try {
            const response = await http.patch(`teams/${data.id}`,data);
            return response.data;
        } catch (error) {
            throw new Error(`[TeamsService] Create | ${error}`);
        }
    },
    async getById(id) {
        try {
            const response = await http.get(`teams/${id}`);
            return response.data;
        } catch (error) {
            throw new Error(`[TeamsService] Create | ${error}`);
        }
    },
}
export default TeamsService

