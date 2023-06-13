<script setup>
import { reactive, onMounted, ref } from 'vue'
import Calendar from "@/Components/Calendar.vue"
import AppLayout from '@/Layouts/AppLayout.vue';
const props = defineProps({
    //tasks: Array,
    user: Object,
    // teams:array
    events:Array,
    notifications:Array
});



</script>

<template>
<AppLayout title="Events" :user="props.user"  :notifications="props?.notifications" >
    <div class="mt-8">
     <Calendar :events="props?.events"/>
    </div>
</AppLayout>
</template>