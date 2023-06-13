<script setup>
import { ref,reactive , onMounted} from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { useForm, usePage } from '@inertiajs/inertia-vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import ActionSection from '@/Components/ActionSection.vue';
import Button from '@/Components/Button.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import Modal from '@/Components/SimpleModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SuccessButton from '@/Components/SuccessButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import FormSection from '@/Components/FormSection.vue';
import Input from '@/Components/Input.vue';
import InputError from '@/Components/InputError.vue';
import Label from '@/Components/Label.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import { TeamsService } from "../../../services";

const props = defineProps({
    team: Object,
    availableRoles: Array,
    userPermissions: Object,
});

// const state = reactive({
//     selectedInvitations :{}
// })
// onMounted(()=>{
//     state.selectedInvitations = {}
// },[])
const addTeamMemberForm = useForm({
    email: '',
    role: null,
});
console.log({roles:props?.availableRoles})
const newMember = useForm({
    name:"",
    email:"",
    password:"12345678",
    role:"editor",
    current_team_id:"",
    canAddTeamMembers:false,
    canRemoveTeamMembers:false,
    canDeleteTeam:false,
    avatar:"assets/images/users/profile.jpg"
})
const updateRoleForm = useForm({
    role: null,
});

const leaveTeamForm = useForm();
const removeTeamMemberForm = useForm();

const currentlyManagingRole = ref(false);
const managingRoleFor = ref(null);
const confirmingLeavingTeam = ref(false);
const teamMemberBeingRemoved = ref(null);
const addMember = ref(null);



const saveMember = async () => {
    if (newMember?.current_team_id) {
        await TeamsService.addMember(newMember)
            .then(async (rep) => {
                await cancelTeamInvitation(addMember.value)
                props.team.team_invitations = props?.team?.team_invitations?.filter((_invitation)=>addMember.value.id !== _invitation.id)
                props?.team?.users?.push(rep?.user)
                addMember.value = null;
            })
            .catch((err) => console.log(err));
    }
};

const addTeamMember = () => {
    addTeamMemberForm.post(route('team-members.store', props.team), {
        errorBag: 'addTeamMember',
        preserveScroll: true,
        onSuccess: () => {
            addTeamMemberForm.reset()
            
        },
    });
};

const cancelTeamInvitation = (invitation) => {
    Inertia.delete(route('team-invitations.destroy', invitation), {
        preserveScroll: true,
    });
    props.team.team_invitations = props?.team?.team_invitations?.filter((_invitation)=>invitation.id !== _invitation.id)
};

const manageRole = (teamMember) => {
    managingRoleFor.value = teamMember;
    updateRoleForm.role = teamMember.role;
    currentlyManagingRole.value = true;
};

const updateRole = () => {
    updateRoleForm.put(route('team-members.update', [props.team, managingRoleFor.value]), {
        preserveScroll: true,
        onSuccess: () => currentlyManagingRole.value = false,
    });
};

const confirmLeavingTeam = () => {
    confirmingLeavingTeam.value = true;
};

const leaveTeam = () => {
    leaveTeamForm.delete(route('team-members.destroy', [props.team, usePage().props.value.user]));
};

const confirmTeamMemberRemoval = (teamMember) => {
    teamMemberBeingRemoved.value = teamMember;
};
const acceptMemberToATeam = (val) => {
    addMember.value = val
    newMember.email = val?.email
    newMember.current_team_id = val?.team_id
    newMember.role = val?.role
}

const removeTeamMember = () => {
    removeTeamMemberForm.delete(route('team-members.destroy', [props.team, teamMemberBeingRemoved.value]), {
        errorBag: 'removeTeamMember',
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => teamMemberBeingRemoved.value = null,
    });
};

const displayableRole = (role) => {
    return props.availableRoles.find(r => r.name === role)?.name || "founder";
};

</script>


<template>
    <div>
        <div v-if="userPermissions.canAddTeamMembers">
            <SectionBorder />

            <!-- Add Team Member -->
            <FormSection @submitted="addTeamMember">
                <template #title>
                    Add Team Member
                </template>

                <template #description>
                    Add a new team member to your team, allowing them to collaborate with you.
                </template>

                <template #form>
                    <div class="col-span-6">
                        <div class="max-w-xl text-sm text-gray-600">
                            Please provide the email address of the person you would like to add to this team.
                        </div>
                    </div>

                    <!-- Member Email -->
                    <div class="col-span-6 sm:col-span-4">
                        <Label for="email" value="Email" />
                        <Input
                            id="email"
                            v-model="addTeamMemberForm.email"
                            type="email"
                            class="mt-1 block w-full"
                        />
                        <InputError :message="addTeamMemberForm.errors.email" class="mt-2" />
                    </div>

                    <!-- Role -->
                    <div v-if="availableRoles.length > 0" class="col-span-6 lg:col-span-4">
                        <Label for="roles" value="Role" />
                        <InputError :message="addTeamMemberForm.errors.role" class="mt-2" />

                        <div class="relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer">
                            <button
                                v-for="(role, i) in availableRoles"
                                :key="role.name"
                                :id="role.id"
                                type="button"
                                style="margin-right: 1rem;"
                                class="btn btn-primary relative px-4  py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200"
                                :class="{'border-top border-gray-200 rounded-top-none': i > 0, 'rounded-bottom-none': i != Object.keys(availableRoles).length - 1}"
                                @click="addTeamMemberForm.role = role.name"
                            >
                                <div :class="{'opacity-50': addTeamMemberForm.role && addTeamMemberForm.role != role.name}">
                                    <!-- Role Name -->
                                    <div class="flex items-center">
                                        <div class="text-sm text-white" :class="{'font-semibold': addTeamMemberForm.role == role.name}">
                                            {{ role.name }}
                                        </div>

                                        <svg
                                            v-if="addTeamMemberForm.role == role.name"
                                            class="ml-2 h-5 w-5 text-green-400"
                                            fill="none"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>

                                    <!-- Role Description -->
                                    <div class="mt-2 text-xs text-white text-left">
                                        {{ role.description }}
                                    </div>
                                </div>
                            </button>

                        </div>
                    </div>
                </template>

                <template #actions>
                    <ActionMessage :on="addTeamMemberForm.recentlySuccessful" class="mr-3">
                        Added.
                    </ActionMessage>

                    <Button :class="{ 'opacity-25': addTeamMemberForm.processing }" :disabled="addTeamMemberForm.processing">
                        Add
                    </Button>
                </template>
            </FormSection>
        </div>

        <div v-if="userPermissions.canAddTeamMembers">
            <SectionBorder />

            <!-- Team Member Invitations -->
            <ActionSection class="mt-10 sm:mt-0">
                <template #title>
                    Pending Team Invitations
                </template>

                <template #description>
                    They may join the team by here.
                </template>

                <!-- Pending Team Member Invitation List -->
                <template #content>
                    <div class="space-y-6">
                        <div v-for="invitation in team?.team_invitations" :key="invitation.id" class="flex items-center justify-between">
                            <div class="text-gray-600">
                                {{ invitation.email }}
                            </div>

                            <div class="flex items-center">
                                <!-- Cancel Team Invitation -->
                                <button
                                    v-if="userPermissions.canRemoveTeamMembers"
                                    class="cursor-pointer ml-6 text-sm text-gray-500 focus:outline-none"
                                    @click="acceptMemberToATeam(invitation)"
                                >
                                    Add to member
                                </button>
                                <button
                                    v-if="userPermissions.canRemoveTeamMembers"
                                    class="cursor-pointer ms-2 ml-6 text-sm text-red-500 focus:outline-none"
                                    @click="cancelTeamInvitation(invitation)"
                                >
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </ActionSection>
        </div>

        <div v-if="team?.users?.length > 0">
            <SectionBorder />

            <!-- Manage Team Members -->
            <ActionSection class="mt-10 sm:mt-0">
                <template #title>
                    Team Members
                </template>

                <template #description>
                    All of the people that are part of this team.
                </template>

                <!-- Team Member List -->
                <template #content>
                    <div class="space-y-6">
                        <div v-for="user in team?.users" :key="user?.id" class="flex items-center justify-between">
                            <div class="flex items-center">
                                <img class="w-8 h-8 rounded-full" :src="user?.profile_photo_url" :alt="user.name">
                                <div class="ml-4">
                                    {{ user.name }}
                                </div>
                            </div>

                            <div class="flex items-center">
                                <!-- Manage Team Member Role -->
                                <span
                                    v-if="user.role"
                                    class="badge bg-info ms-2"
                                    > {{ displayableRole(user.role) }}</span
                                >
                                <span
                                    v-if="userPermissions.canAddTeamMembers && availableRoles.length && !(user.role==='founder')"
                                    class="ml-2 ms-2 text-sm text-gray-400 underline"
                                    @click="manageRole(user)"
                                >
                                    <i class="uil-pen"></i>
                                </span>

                                <!-- Leave Team -->
                                <button
                                    v-if="$page.props.user?.id === user?.id"
                                    class="cursor-pointer ml-6 text-sm text-red-500"
                                    @click="confirmLeavingTeam"
                                >
                                    Leave
                                </button>

                                <!-- Remove Team Member -->
                                <button
                                    v-if="userPermissions.canRemoveTeamMembers"
                                    class="cursor-pointer ml-6 text-sm text-red-500"
                                    @click="confirmTeamMemberRemoval(user)"
                                >
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </ActionSection>
        </div>

        <!-- Role Management Modal -->
        <DialogModal :show="currentlyManagingRole" @close="currentlyManagingRole = false">
            <template #title>
                Manage Role
            </template>

            <template #content>
                <div v-if="managingRoleFor">
                    <div class="relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer">
                        <button
                            v-for="(role, i) in availableRoles"
                            :key="role.name"
                            type="button"
                            class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200"
                            :class="{'border-t border-gray-200 rounded-t-none': i > 0, 'rounded-b-none': i !== Object.keys(availableRoles).length - 1}"
                            @click="updateRoleForm.role = role.name"
                        >
                            <div :class="{'opacity-50': updateRoleForm.role && updateRoleForm.role !== role.name}">
                                <!-- Role Name -->
                                <div class="flex items-center">
                                    <div class="text-sm text-gray-600" :class="{'font-semibold': updateRoleForm.role === role.name}">
                                        {{ role.name }}
                                    </div>

                                    <svg
                                        v-if="updateRoleForm.role === role.name"
                                        class="ml-2 h-5 w-5 text-green-400"
                                        fill="none"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    ><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>

                                <!-- Role Description -->
                                <div class="mt-2 text-xs text-gray-600">
                                    {{ role.description }}
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="currentlyManagingRole = false">
                    Cancel
                </SecondaryButton>

                <Button
                    class="ml-3"
                    :class="{ 'opacity-25': updateRoleForm.processing }"
                    :disabled="updateRoleForm.processing"
                    @click="updateRole"
                >
                    Save
                </Button>
            </template>
        </DialogModal>

        <!-- Leave Team Confirmation Modal -->
        <ConfirmationModal :show="confirmingLeavingTeam" @close="confirmingLeavingTeam = false">
            <template #title>
                Leave Team
            </template>

            <template #content>
                Are you sure you would like to leave this team?
            </template>

            <template #footer>
                <SecondaryButton @click="confirmingLeavingTeam = false">
                    Cancel
                </SecondaryButton>

                <DangerButton
                    class="ml-3"
                    :class="{ 'opacity-25': leaveTeamForm.processing }"
                    :disabled="leaveTeamForm.processing"
                    @click="leaveTeam"
                >
                    Leave
                </DangerButton>
            </template>
        </ConfirmationModal>

        <!-- Remove Team Member Confirmation Modal -->
        <ConfirmationModal :show="teamMemberBeingRemoved" @close="teamMemberBeingRemoved = null">
            <template #title>
                Remove Team Member
            </template>

            <template #content>
                Are you sure you would like to remove this person from the team?
            </template>

            <template #footer>
                <SecondaryButton @click="teamMemberBeingRemoved = null">
                    Cancel
                </SecondaryButton>

                <DangerButton
                    class="ml-3"
                    :class="{ 'opacity-25': removeTeamMemberForm.processing }"
                    :disabled="removeTeamMemberForm.processing"
                    @click="removeTeamMember"
                >
                    Remove
                </DangerButton>
            </template>
        </ConfirmationModal>

        <!-- Add user to member -->
        <Modal :show="addMember" @close="addMember = null">
            <template #title>
                Add a member to the team
            </template>

            <template #content>
                <form>
                            <div class="text-center mb-3 ">
                                <label
                                    for="photo"
                                    class="form-label visually-hidden"
                                    >Photo</label
                                >
                                <div class="mb-3 flex justify-center">
                                    <img
                                        id="previewPhoto"
                                        alt="Preview"
                                        class="img-fluid rounded-circle mb-3"
                                        :src="newMember.avatar"
                                        style="
                                            width: 150px;
                                            height: 150px;
                                            object-fit: cover;
                                        "
                                    />
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="name" class="form-label d-flex"
                                    >Name</label
                                >
                                <input
                                    type="text"
                                    class="form-control"
                                    id="name"
                                    placeholder="Enter name"
                                    v-model="newMember.name"
                                />
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label d-flex"
                                    >Email</label
                                >
                                <input
                                    type="email"
                                    class="form-control"
                                    id="email"
                                    placeholder="Enter email"
                                    v-model="newMember.email"
                                />
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label d-flex"
                                    >Avatar</label
                                >
                                <input
                                    type="file"
                                    class="form-control"
                                    id="avatar"
                                    placeholder="Enter avatar"
                                />
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label d-flex"
                                    >Role</label
                                >
                                <select
                                    class="form-select"
                                    id="role"
                                    v-model="newMember.role"
                                >
                                    <option   v-for="role in props?.availableRoles" :key="role.id" :value="role.name" >{{ role?.name }}</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label d-flex"
                                    >Permissions</label
                                >
                                <div class="col-sm-6 mb-2 mb-sm-0">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="canAddTeamMembers" v-model="newMember.canAddTeamMembers">
                                        <label class="form-check-label flex" for="canAddTeamMembers">
                                            User can add Team
                                        </label>
                                    </div> <!-- end checkbox -->
                                </div> <!-- end col -->
                                <div class="col-sm-6 mb-2 mb-sm-0">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="canRemoveTeamMembers" v-model="newMember.canRemoveTeamMembers">
                                        <label class="form-check-label flex" for="canRemoveTeamMembers">
                                            User can remove Team members
                                        </label>
                                    </div> <!-- end checkbox -->
                                </div> <!-- end col -->
                                <div class="col-sm-6 mb-2 mb-sm-0">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="canDeleteTeam" v-model="canDeleteTeam">
                                        <label class="form-check-label flex" for="canDeleteTeam">
                                            User can delete Team
                                        </label>
                                    </div> <!-- end checkbox -->
                                </div> <!-- end col -->

                            </div>
                        </form>
            </template>

            <template #footer>
                <SecondaryButton @click="addMember = null">
                    Cancel
                </SecondaryButton>

                <SuccessButton
                    class="ml-3"
                    @click="saveMember"
                >
                    Add
                </SuccessButton>
            </template>
        </Modal>
    </div>
</template>
