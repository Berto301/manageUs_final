<script setup>
import { defineProps } from 'vue';

defineProps({
  show: { type: Boolean, default: true },

});
</script>

<template>
  <div :class="`flex justify-start mr-1 items-center !px-1 py-1 text-center ${show ? 'bg-primary': 'bg-transparent'}  text-white text-4xl`">
    <slot />
    <span><span>ANAGE</span><span class="text-[#7300e6]">US</span></span>
  </div>
</template>
