<script setup>
import { reactive, onMounted, ref } from 'vue'
import Calendar from "@/Components/Calendar.vue"
import AppLayout from '@/Layouts/AppLayout.vue';
const props = defineProps({
    //tasks: Array,
    user: Object,
    // teams:array
});


</script>

<template>
<AppLayout title="Events" :user="props.user">
    <div class="mt-8">
     <Calendar />
    </div>
</AppLayout>
</template>