
import http from "../axios";

const GroupService = {

    async update(data) {
        try {
            const response = await http.patch(`group/${data.id}`,data);
            return response.data;
        } catch (error) {
            throw new Error(`[GroupService] Create | ${error}`);
        }
    }
}
export default GroupService

