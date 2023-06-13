<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import { reactive, onMounted } from 'vue'
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import Item from './Partials/Item.vue';

const props = defineProps({
    tasks: Array,
    user: Object,
    notifications:Array
});

const state = reactive({
    task: newTask(),
    editing_task: null,
    deleting_task: null,
    tasks: [],

    modal_task_form: null,
    modal_task_delete_confirm: null,
})


onMounted(() => {

    state.tasks = JSON.parse(JSON.stringify(props.tasks))

    state.modal_task_delete_confirm = new window.bootstrap.Modal('#modal_task_delete_confirm', {})
    state.modal_task_delete_confirm._element.addEventListener('hide.bs.modal', () => {})


    state.modal_task_form = new window.bootstrap.Modal('#modal_task_form', {})
    state.modal_task_form._element.addEventListener('hide.bs.modal', () => {})
})

function newTask()
{
    return {
        title: null,
        description: null
    }
}

function newTask_init()
{
    state.task = newTask()
    state.editing_task = null
    state.modal_task_form.show()
}

function saveTask()
{

    if (state.editing_task && state.editing_task.id)
    {
        return axios.patch(`/tasks/${state.editing_task.id}`, {
            title: state.editing_task.title,
            description: state.editing_task.description,
        })
        .then(res => {
            for (let i = 0; i < state.tasks.length; i++)
            {
                if (res.data.task.id === state.tasks[i].id)
                {
                    state.tasks[i].title = res.data.task.title
                    state.tasks[i].description = res.data.task.description
                }
            }

            state.modal_task_form.hide()

        })
        .catch(err => {
            debugger
        })
    }

    return axios.post(`/teams/${props.user.current_team_id}/tasks`, {
        title: state.task.title,
        description: state.task.description,
    })
    .then(res => {
        state.tasks.push(res.data.task)

        state.modal_task_form.hide()
    })
    .catch(err => {
        debugger
    })

}

function editTask(_task)
{
    state.editing_task = JSON.parse(JSON.stringify(_task))
}

function editTask_cancel(_task)
{
    state.editing_task = null
}

function toggleCompleteTask(task)
{

    axios.patch(`tasks/${task.id}`, {
        is_complete: task.is_complete
    })
        .then(res => {
            for (let i = 0; i < state.tasks.length; i++)
            {
                if (res.data.task.id === state.tasks[i].id)
                {
                    state.tasks[i].is_complete = res.data.task.is_complete
                }
            }
        })
        .catch(err => {
            debugger
        })
}

function deleteTask_init(_task)
{
    state.deleting_task = JSON.parse(JSON.stringify(_task))
    state.modal_task_delete_confirm.show()
}


function deleteTask()
{
    if (! state.deleting_task || ! state.deleting_task.id)
    {
        return
    }

    return axios.delete(`/tasks/${state.deleting_task.id}`)
        .then(res => {
            state.tasks = state.tasks.filter(t => t.id != res.data.task_id)

            state.modal_task_delete_confirm.hide()

            state.deleting_task = null
            state.editing_task = null
        })
        .catch(err => {
            debugger
        })
}

</script>

<template>
<AppLayout title="Dashboard Custom Head" :user="props.user"  :notifications="props?.notifications">
  <div class="row">
    <div class="w-full">
         <!-- start page title -->
         <div class="page-title-box">
                <div class="page-title-right">
                    <div class="app-search">
                        <form>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search..." />
                                <span class="mdi mdi-magnify search-icon"></span>
                                <button class="input-group-text btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class='uil uil-sort-amount-down'></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="#">Due Date</a>
                                    <a class="dropdown-item" href="#">Added Date</a>
                                    <a class="dropdown-item" href="#">Assignee</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <h4 class="page-title">Tasks <a href="#" class="btn btn-success btn-sm ms-3" @click="newTask_init">Add New</a></h4>
            </div>
            <!-- end page title -->

            <Disclosure>
                    <DisclosureButton class="py-2 w-full bg-white p-2 ">
                        <h5 class="m-0 pb-2 flex">
                                <i class='uil uil-angle-down font-18'></i>New <span class="text-muted">({{state.tasks.length}})</span>
                                <span class="list-inline-item ms-1 ">
                         <i class="uil uil-align-alt font-16 me-1"></i> 0/{{state.tasks.length}}
                        </span>
                        </h5>
                       
                    </DisclosureButton>
                    <DisclosurePanel class="text-gray-500">
                        <Item
                            :editTask="editTask"
                            :tasks="state.tasks"
                            :editing_task="state.editing_task"
                            :editTask_cancel="editTask_cancel"
                            :toggleCompleteTask="toggleCompleteTask"
                        />
                    </DisclosurePanel>
            </Disclosure>

    </div>
  </div>
  <div
        id="modal_task_form"
        class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal_task_form_label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-colored-header bg-primary">
                    <h4 class="modal-title" id="modal_task_form_label">Modal Heading</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">



                    <div class="mb-3">
                        <label for="task_title" class="form-label">Title</label>
                        <input
                            id="task_title"
                            class="form-control"
                            v-model="state.task.title"

                            type="text"
                            >
                    </div>

                    <div class="mb-3">
                        <label for="task_description" class="form-label">Description</label>
                        <input
                            v-model="state.task.description"
                            type="text" id="task_description" class="form-control">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" @click="saveTask">Save</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div
        id="modal_task_delete_confirm"
        class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal_task_delete_confirm_label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-colored-header bg-primary">
                    <h4 class="modal-title" id="modal_task_delete_confirm_label">Delete Task</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this task?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" @click="deleteTask">Confirm</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</AppLayout>
</template>
