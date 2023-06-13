<script setup>
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js';
import { Pie } from 'vue-chartjs';
import {  options } from '../utils';

ChartJS.register(ArcElement, Tooltip, Legend);
const props = defineProps({
    //tasks: Array,
    data: Object,
});

</script>
<template>
<div>
    <Pie :data="data" :options="options" />
  </div>
</template>