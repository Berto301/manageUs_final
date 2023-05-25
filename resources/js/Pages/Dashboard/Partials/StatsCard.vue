<script setup>
const props = defineProps({
    data:Object
});
</script>
<template>
      <div class="w-72 h-32 mx-auto bg-white rounded shadow-md">
        <div class="relative ">
        <div :class="`absolute -top-10 left-5 ${data?.bgColor || 'bg-[#43a047]'} rounded-lg shadow-md  w-20 h-20`">
            <div class="py-2 text-center font-bold text-white text-4xl mt-[10px]"><i :class="`${data?.icon || 'uil-users-alt'}`"></i></div>
        </div>
        <div class="p-3">
            <div class="flex flex-col space-y-2">
                <span class="text-end font-normal text-[#999] text-sm">{{ data?.title || '' }}</span>
                <span class="text-end font-bold text-black text-4xl">{{ data?.value || 0 }}</span>
            </div>
            <slot name="actions"></slot>
        </div>
        </div>
    </div>
  </template>
