<script setup>
import { reactive, onMounted, ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue';
import StatsCards from "./Partials/StatsCard.vue"
import Calendar from "./Partials/Calendar.vue"
import Chart from './Partials/Chart.vue';
const props = defineProps({
    //tasks: Array,
    user: Object,
    // teams:array
});


</script>

<template>
<AppLayout title="Dashboard" :user="props.user">
    <main class="flex-grow bg-gray-100 p-6 mt-8">
      <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 mt-8">
        <StatsCards :data="{
          bgColor:'bg-[#43a047]',
          icon:'uil-users-alt',
          title:'Teams',
          value:2
        }"/>
        <StatsCards :data="{
          bgColor:'bg-[#fb8c00]',
          icon:'uil-user',
          title:'Users',
          value:20
        }"/>
        <StatsCards :data="{
          bgColor:'bg-[#26c6da]',
          icon:'uil-briefcase',
          title:'Tasks',
          value:23
        }"/>
      </div>
    </main>
    <main class="flex-grow bg-gray-100 p-6 mt-2">
      <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 mt-8">
        <Chart/>
        <Chart/>
        <Chart/>
      </div>
    </main>
    <main class="flex-grow bg-gray-100 p-6 mt-2">
     <Calendar />
    </main>
   
</AppLayout>
</template>