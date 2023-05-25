<script setup>
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js';
import { Pie } from 'vue-chartjs';
import { data, options } from '../utils';

ChartJS.register(ArcElement, Tooltip, Legend);


</script>
<template>
<div>
    <Pie :data="data" :options="options" />
  </div>
</template>