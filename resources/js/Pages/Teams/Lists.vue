<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import ShowSetting from "./Show.vue";
import { TeamsService } from "../../services";
import { onMounted, reactive, ref } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";

const props = defineProps({
    teams: Array,
    user: Object,
    availableRoles: Array,
    permissions: Object,
    notifications:Array
});
const state = reactive({
    modal_add_member: null,
    modal_add_teams: null,
    modal_delete_teams: null,
    newTeam: {
        name: "",
        personal_team: false,
    },
    teams: props?.teams,
    selectedTeam: {},
    modalModifyUser: null,
    selectedMember: {},
    avatar: "assets/images/users/profile.jpg",
});
const member = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    role: "employee",
});

onMounted(() => {
    state.modal_add_member = new window.bootstrap.Modal(
        "#modal_new_member",
        {}
    );
    state.modal_add_member._element.addEventListener("hide.bs.modal", () => {});
    state.modal_add_teams = new window.bootstrap.Modal("#modal_new_teams", {});
    state.modal_add_teams._element.addEventListener("hide.bs.modal", () => {});
    state.modal_delete_teams = new window.bootstrap.Modal(
        "#modal_delete_teams",
        {}
    );
    state.modal_delete_teams._element.addEventListener(
        "hide.bs.modal",
        () => {}
    );
    state.modalModifyUser = new window.bootstrap.Modal("#modalModifyUser", {});
    state.modalModifyUser._element.addEventListener("hide.bs.modal", () => {});
});
const { teams } = state;
const toggleDeleteTeam = (team) => {
    state.selectedTeam = team;
    state.modal_delete_teams.show();
};
const toggleModalMember = (team) => {
    member.reset();
    state.selectedTeam = team;
    state.modal_add_member.show();
};
function toggleModalTeams() {
    state.newTeam = { name: null };
    state.modal_add_teams.show();
}
function toggleSettings(team) {
    Inertia.post(route("team_settings", { id: team.id }));
}

function toggleModalModifyUser(member) {
    state.selectedMember = member;
    state.modalModifyUser.show();
}
function toggleEditTeam(team) {
    const { name, personal_team } = team;
    state.newTeam = { name, personal_team };
    state.selectedTeam = team;
    state.modal_add_teams.show();
}
const saveMember = async () => {
    if (state.selectedTeam?.id) {
        if (member.password !== member.password_confirmation)
            return console.log("password invalid");
        console.log({ current_team_id: state.selectedTeam?.id });
        await TeamsService.addMember({
            ...member,
            current_team_id: state.selectedTeam?.id || NULL,
        })
            .then((rep) => {
                console.log({ rep });
                state.selectedTeam = {};
                state.modal_add_member.hide();
                state.teams = state.teams.map((team) => {
                    if (team?.id === rep.user?.current_team_id) {
                        team.users.push(rep?.user);
                    }
                    return team;
                });
                console.log({ teams: state.teams });
            })
            .catch((err) => console.log(err));
    }
};
async function saveTeams() {
    if (state?.newTeam?.name) {
        const data = {
            name: state.newTeam.name,
            user_id: props?.user?.id,
            personal_team: state.newTeam.personal_team ? 1 : 0,
        };
        if (state?.selectedTeam?.id) {
            TeamsService.update({
                ...data,
                id: state?.selectedTeam?.id,
            })
                .then((rep) => {
                    if (rep?.team) {
                        state.teams = state.teams.map((team) => {
                            if (team?.id === rep.team?.id) {
                                team.name = rep.team.name;
                                team.personal_team = rep.team.personal_team;
                            }
                            return team;
                        });
                        state.modal_add_teams.hide();
                    }
                })
                .catch((err) => console.log(err));
        } else {
            await TeamsService.create(data)
                .then((rep) => {
                    if (rep?.team) {
                        state.teams.push(rep.team);
                        state.modal_add_teams.hide();
                    }
                })
                .catch((err) => console.log(err));
        }
    }
}
async function deleteTeam() {
    if (state.selectedTeam?.id) {
        await TeamsService.deleteById(state.selectedTeam?.id)
            .then((rep) => {
                state.teams = state.teams.filter(
                    (team) => team.id !== state.selectedTeam?.id
                );
                state.modal_delete_teams.hide();
            })
            .catch((err) => console.log(err));
    }
}

const photoInput = ref(null);

function handlePhotoUpload(event) {
    event.preventDefault();
    const file = event.target.files[0];

    const reader = new FileReader();
    reader.onload = function (e) {
        // Lorsque le chargement est terminé, vous pouvez accéder à l'URL de la photo
        const photoURL = e.target.result;

        // Mettez à jour le state.avatar avec l'URL de la photo
        state.avatar = photoURL;

        // Affichez la photo sur la page
        const photoElement = document.getElementById("photo");
        photoElement.src = photoURL;
    };

    // Lecture du contenu du fichier en tant que Data URL
    reader.readAsDataURL(file);
}
</script>

<style>
.upload-photo {
    text-align: center;
    margin-bottom: 20px;
}
</style>
<template>
    <AppLayout title="Teams" :user="props.user"  :notifications="props?.notifications">
        <div class=" !bg-transparent mb-0 space-y-4" style="margin-top: 30px; padding: 30px 20px">
            <div class="card-header">
                <div
                    class="d-flex justify-content-between p-2 align-items-center"
                >
                    <h3>Teams</h3>
                    <span @click="toggleModalTeams">
                        <i class="uil-plus-circle"></i>
                        Add a team
                    </span>
                </div>
            </div>
            <div class="space-y-4">
                <Disclosure v-for="team in teams" :key="team.id">
                    <DisclosureButton class="py-2 w-full bg-white p-2 ">
                        <div
                            class="d-flex justify-content-between align-items-center my-2"
                        >
                            <h5 class="m-0 pb-2">
                                <a
                                    class="text-dark"
                                    :data-bs-toggle="
                                        team?.users && team?.users.length
                                            ? 'd-block'
                                            : ''
                                    "
                                    :href="'#team-' + team?.id"
                                    aria-expanded="false"
                                    :aria-controls="'team-' + team?.id"
                                >
                                    <i class="uil uil-angle-down font-18"></i
                                    >{{ team.name }}
                                    <span class="text-muted"
                                        >({{
                                            team?.users?.length || 0
                                        }})</span
                                    >
                                </a>
                                <span
                                    v-if="team?.personal_team"
                                    class="badge bg-info ms-2"
                                    >Personnal team</span
                                >
                            </h5>

                            <div class="dropdown">
                                <button
                                    class="btn btn-secondary dropdown-toggle"
                                    type="button"
                                    :id="'dropdownMenu-' + team?.id"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false"
                                >
                                    Actions
                                </button>
                                <ul
                                    class="dropdown-menu"
                                    :aria-labelledby="
                                        'dropdownMenu-' + team?.id
                                    "
                                >
                                    <li @click="toggleModalMember(team)">
                                        <a class="dropdown-item" href="#"
                                            ><i class="uil-user-plus"></i> Add a
                                            member</a
                                        >
                                    </li>
                                    <li @click="toggleEditTeam(team)">
                                        <a class="dropdown-item" href="#"
                                            ><i class="uil-pen"></i>Edit</a
                                        >
                                    </li>
                                    <li @click="toggleDeleteTeam(team)">
                                        <a class="dropdown-item" href="#"
                                            ><i class="uil-trash-alt"></i
                                            >Delete</a
                                        >
                                    </li>
                                    <li @click="toggleSettings(team)">
                                        <a class="dropdown-item" href="#"
                                            ><i class="uil-trash-alt"></i
                                            >Settings</a
                                        >
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </DisclosureButton>
                    <DisclosurePanel class="text-gray-500">
                        <ShowSetting
                            :available-roles="availableRoles"
                            :permissions="permissions"
                            :team="team"
                            :user="user"
                        />
                    </DisclosurePanel>
                </Disclosure>

                <!-- end .mt-2-->
            </div>
        </div>

        <!-- Modals Add Member -->

        <div
            id="modal_new_member"
            class="modal fade"
            tabindex="-1"
            role="dialog"
            aria-labelledby="modal_task_form_label"
            aria-hidden="true"
        >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header modal-colored-header bg-primary">
                        <h4 class="modal-title" id="modal_task_form_label">
                            Add a new Member
                        </h4>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-hidden="true"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="fullname" class="form-label"
                                >Full Name</label
                            >
                            <input
                                v-model="member.name"
                                class="form-control"
                                type="text"
                                id="fullname"
                                placeholder="Enter your name"
                                required
                            />
                        </div>

                        <div class="mb-3">
                            <label for="emailaddress" class="form-label"
                                >Email address</label
                            >
                            <input
                                v-model="member.email"
                                class="form-control"
                                type="email"
                                id="emailaddress"
                                required
                                placeholder="Enter your email"
                            />
                        </div>
                        <div>
                            <label>Role</label>
                            <select
                                class="form-select"
                                aria-label="Default select example"
                                :v-model="member.role"
                            >
                                <option value="admin">Admin</option>
                                <option value="employee">Employee</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label"
                                >Password</label
                            >
                            <div class="input-group input-group-merge">
                                <input
                                    v-model="member.password"
                                    type="password"
                                    id="password"
                                    class="form-control"
                                    placeholder="Enter your password"
                                />
                                <div
                                    class="input-group-text"
                                    data-password="false"
                                >
                                    <span class="password-eye"></span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirm" class="form-label"
                                >Confirm Password</label
                            >
                            <div class="input-group input-group-merge">
                                <input
                                    v-model="member.password_confirmation"
                                    type="password"
                                    id="password_confirm"
                                    class="form-control"
                                    placeholder="Confirm your password"
                                />
                                <div
                                    class="input-group-text"
                                    data-password="false"
                                >
                                    <span class="password-eye"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal"
                        >
                            Close
                        </button>
                        <button
                            type="button"
                            class="btn btn-primary"
                            @click="saveMember"
                        >
                            Save
                        </button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->

        <!-- Modals Add Teams -->

        <div
            id="modal_new_teams"
            class="modal fade"
            tabindex="-1"
            role="dialog"
            aria-labelledby="modal_task_form_label"
            aria-hidden="true"
        >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header modal-colored-header bg-primary">
                        <h4 class="modal-title" id="modal_task_form_label">
                            Add a new Teams
                        </h4>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-hidden="true"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="team_name" class="form-label"
                                >Name</label
                            >
                            <input
                                id="team_name"
                                class="form-control"
                                v-model="state.newTeam.name"
                                type="text"
                                required
                            />
                        </div>
                        <div class="form-check mb-3">
                            <input
                                v-model="state.newTeam.personal_team"
                                type="checkbox"
                                class="form-check-input"
                                id="personal_team"
                            />
                            <label class="form-check-label" id="personal_team">
                                Personnal team
                            </label>
                        </div>
                        <!-- end checkbox -->
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal"
                        >
                            Close
                        </button>
                        <button
                            type="button"
                            class="btn btn-primary"
                            @click="saveTeams"
                        >
                            Save
                        </button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
        <div
            id="modal_delete_teams"
            class="modal fade"
            tabindex="-1"
            role="dialog"
            aria-labelledby="modal_task_form_label"
            aria-hidden="true"
        >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header modal-colored-header bg-primary">
                        <h4 class="modal-title" id="modal_task_form_label">
                            Delete team
                        </h4>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-hidden="true"
                        ></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you would like to delete this team?
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal"
                        >
                            Close
                        </button>
                        <button
                            type="button"
                            class="btn btn-primary"
                            @click="deleteTeam"
                        >
                            Save
                        </button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->

        <div
            class="modal fade"
            id="modalModifyUser"
            tabindex="-1"
            aria-labelledby="modalModifyUserLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalModifyUserLabel">
                            Modify User
                        </h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="text-center mb-3">
                                <label
                                    for="photo"
                                    class="form-label visually-hidden"
                                    >Photo</label
                                >
                                <div class="mb-3">
                                    <img
                                        id="previewPhoto"
                                        :src="state.avatar"
                                        alt="Preview"
                                        class="img-fluid rounded-circle mb-3"
                                        style="
                                            width: 150px;
                                            height: 150px;
                                            object-fit: cover;
                                        "
                                    />
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="name" class="form-label"
                                    >Name</label
                                >
                                <input
                                    type="text"
                                    class="form-control"
                                    id="name"
                                    placeholder="Enter name"
                                    v-model="state.selectedMember.name"
                                />
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label"
                                    >Email</label
                                >
                                <input
                                    type="email"
                                    class="form-control"
                                    id="email"
                                    placeholder="Enter email"
                                    v-model="state.selectedMember.email"
                                />
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label"
                                    >Avatar</label
                                >
                                <input
                                    type="file"
                                    class="form-control"
                                    id="email"
                                    placeholder="Enter email"
                                    @change="handlePhotoUpload"
                                />
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label"
                                    >Role</label
                                >
                                <select
                                    class="form-select"
                                    id="role"
                                    v-model="state.selectedMember.role"
                                >
                                    <option value="admin">Admin</option>
                                    <option value="user">User</option>
                                    <option value="lead">Team lead</option>
                                    <option value="founder">
                                        Founder - CEO
                                    </option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                        >
                            Close
                        </button>
                        <button type="button" class="btn btn-primary">
                            Save changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
