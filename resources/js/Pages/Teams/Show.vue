<script setup>
import Container from './Partials/Container.vue';
import DeleteTeamForm from '@/Pages/Teams/Partials/DeleteTeamForm.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import TeamMemberManager from '@/Pages/Teams/Partials/TeamMemberManager.vue';
import UpdateTeamNameForm from '@/Pages/Teams/Partials/UpdateTeamNameForm.vue';

defineProps({
    team: Object,
    availableRoles: Array,
    permissions: Object,
    user:Object
});
</script>

<template>
     <Container>
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Team Settings
        </h2>
      </template>
  
      <div class="container-xl mx-auto py-2 sm:px-6 lg:px-8">
        <UpdateTeamNameForm :team="team" :permissions="permissions" />
  
        <TeamMemberManager
          class="mt-10 sm:mt-0"
          :team="team"
          :available-roles="availableRoles"
          :user-permissions="permissions"
        />
  
        <template v-if="permissions.canDeleteTeam ">
          <SectionBorder />
  
          <DeleteTeamForm class="mt-10 sm:mt-0" :team="team" :canDelete="team.personal_team" />
        </template>
     </div>
    </Container>
  </template>
  
