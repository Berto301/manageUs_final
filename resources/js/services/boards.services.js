import http from "../axios";

const BoardsService = {
    async getAll() {
        try {
            const response = await http.get(`boards`);
            return response.data;
        } catch (error) {
            throw new Error(`[BoardsService] Get All Lists | ${error}`);
        }
    },
    async deleteById(id) {
        try {
            const response = await http.delete(`boards/${id}`);
            return response.data;
        } catch (error) {
            throw new Error(`[BoardsService] Delete by ID | ${error}`);
        }
    },
    async create(data) {
        try {
            const response = await http.post(`boards`,data);
            return response.data;
        } catch (error) {
            throw new Error(`[BoardsService] Create | ${error}`);
        }
    },
    async update(data) {
        try {
            const response = await http.put(`boards/${data?.id}`,data);
            return response.data;
        } catch (error) {
            throw new Error(`[BoardsService] Create | ${error}`);
        }
    },
    async getById(id) {
        try {
            const response = await http.get(`boards/${id}`);
            return response.data;
        } catch (error) {
            throw new Error(`[BoardsService] Create | ${error}`);
        }
    },
}
export default BoardsService

