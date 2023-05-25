import { createStore } from "vuex";

const store = createStore({
    state: {
        user: {
            data: {},
            token: null,
        },
        groupId:null
    }
});

export default store;