
import http from "../axios";

const TasksService = {
    async getAll() {
        try {
            const response = await http.get(`tasks`);
            return response.data;
        } catch (error) {
            throw new Error(`[TasksService] Get All Lists | ${error}`);
        }
    },
    async deleteById(id) {
        try {
            const response = await http.delete(`tasks/${id}`);
            return response.data;
        } catch (error) {
            throw new Error(`[TasksService] Delete by ID | ${error}`);
        }
    },
    async create(data) {
        try {
            const response = await http.post(`tasks`,data);
            return response.data;
        } catch (error) {
            throw new Error(`[TasksService] Create | ${error}`);
        }
    },
    async update(data) {
        try {
            const response = await http.put(`tasks/${data?.id}`,data);
            return response.data;
        } catch (error) {
            throw new Error(`[TasksService] Create | ${error}`);
        }
    },
    async getById(id) {
        try {
            const response = await http.get(`tasks/${id}`);
            return response.data;
        } catch (error) {
            throw new Error(`[TasksService] Create | ${error}`);
        }
    },
}
export default TasksService

