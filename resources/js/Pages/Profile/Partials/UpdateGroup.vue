<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import Button from '@/Components/Button.vue';
import FormSection from '@/Components/FormSection.vue';
import Input from '@/Components/Input.vue';
import InputError from '@/Components/InputError.vue';
import Label from '@/Components/Label.vue';
import { LISTS_ACTIVITY_AREA } from '../../Auth/lists';
import {GroupService} from "../../../services/"

// const passwordInput = ref(null);
// const currentPasswordInput = ref(null);
const props = defineProps({
    group:Object,
    role:String
});
const form = useForm({
    name: props?.group?.name ,
    activityArea: props?.group?.activityArea,
});

const updateGroup = () => {
    GroupService.update({
        ...form,
        id:props?.group?.id
    }).then((rep)=>{
        
    })
    .catch((err)=>console.log({err}))
};
console.log({ev: process.env.MIX_HOST})
</script>

<template>
    <FormSection @submitted="updateGroup">
        <template #title>
            Group Information
        </template>

        <template #description>
            Here is your group information.
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <Label for="name" value="Name" />
                <Input
                    id="name"
                    ref="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="name"
                    :disabled="!role==='founder'"
                />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <Label for="activityArea" value="Activity Area" />
                <select
                    class="form-select"
                    aria-label="Default select example"
                    v-model="form.activityArea"
                    :disabled="!!role==='founder'"
                >
                    <option :value="null">Please select</option>
                    <option v-for="(area,i) in LISTS_ACTIVITY_AREA" :key="'area.value'+'-'+i" :value="area.value">{{ area.title }}</option>
                </select>
            </div>

        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>

            <Button :class="{ 'opacity-25': form.processing }" :disabled="form.processing" v-if="role==='founder'">
                Save
            </Button>
        </template>
    </FormSection>
</template>
