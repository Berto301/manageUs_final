<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import CreateTeamForm from "@/Pages/Teams/Partials/CreateTeamForm.vue";
import { formatDistanceToNow } from 'date-fns';
import { enGB } from 'date-fns/locale';

const props = defineProps({
    tasks: Array,
    editing_task: Object,
    editTask: Function,
    editTask_cancel: Function,
    toggleCompleteTask: Function,
});
</script>

<template>
    <div
        v-for="task in tasks"
        :key="task.id"
        class="row justify-content-sm-between"
    >
        <div class="col-lg-6 mb-2 mb-lg-0 d-flex align-items-center">
            <div
                v-show="!editing_task || editing_task.id !== task.id"
                @click="editTask(task)"
                class="ln-icon-btn me-3"
            >
                <div class="mdi mdi-pencil"></div>
            </div>
            <div
                v-show="editing_task && editing_task.id === task.id"
                @click="editTask_cancel"
                class="ln-icon-btn me-3"
            >
                <div class="mdi mdi-cancel"></div>
            </div>

            <div class="form-check">
                <input
                    @change="toggleCompleteTask(task)"
                    v-model="task.is_complete"
                    type="checkbox"
                    class="form-check-input"
                    :id="task.id"
                />
                <label class="form-check-label" :for="task.id">
                    {{ task.title }}
                </label>
            </div>
        </div>
        <div class="col-lg-6 d-flex justify-content-between align-items-center">
            <div id="tooltip-container">
                <img
                    src="assets/images/users/avatar-9.jpg"
                    alt="image"
                    class="avatar-xs rounded-circle me-1"
                    data-bs-container="#tooltip-container"
                    data-bs-toggle="tooltip"
                    data-bs-placement="bottom"
                    title="Assigned to Arya S"
                />
            </div>
            <div>
                <ul class="list-inline font-13 text-end mb-0">
                    <li class="list-inline-item">
                        <i class="uil uil-schedule font-16 me-1"></i> {{ formatDistanceToNow(new Date(task?.updated_at),{ addSuffix: true, locale: enGB }) }}
                    </li>
                   
                    <li class="list-inline-item ms-1">
                        <div id="tooltip-container">
                            <i class="uil uil-comment-message font-16 me-1" :title="task?.description"></i> 
                        </div>
                    </li>
                    <li class="list-inline-item ms-2">
                        <span class="badge badge-danger-lighten p-1">High</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
