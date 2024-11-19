<script setup>
import {Head, useForm} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {onMounted, ref, watch} from "vue";
import Input from '@/Components/TextInput.vue';
import Label from '@/Components/InputLabel.vue';
import Button from '@/Components/PrimaryButton.vue';

const props = defineProps(['course']);
const form = useForm({
    name: props.course.name,
    description: props.course.description,
    price: props.course.price,
    is_active: props.course.is_active,
    start_date: props.course.start_date,
    end_date: props.course.end_date,
    remaining_slots: props.course.remaining_slots,
});

const errors = ref({});
const submit = () => {
    form.put(route('courses.update', { course: props.course.id }), {
        onSuccess: () => {
            form.reset();
        },
        onError: (err) => {
            errors.value = err;
            console.log(errors.value);
        }
    });
};

const checkboxRef = ref(null);

watch(
    () => form.is_active,
    (newValue) => {
        if (checkboxRef.value) {
            checkboxRef.value.checked = newValue;
        }
    }
);

onMounted(() => {
    form.is_active = props.course?.is_active;
});


console.log(props, 'props');
console.log(form, 'form');
</script>

<template>
    <Head title="Editar Curso"/>

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Editar Curso</h2>
                <Button @click="showForm = !showForm">Novo Curso</Button>
            </div>
        </template>

        <div class="mt-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <form @submit.prevent="submit" class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full md:w-1/2 px-3 mb-4">
                        <Label for="name" value="Nome do Curso"/>
                        <Input id="name" v-model="form.name" type="text" class="mt-1 block w-full" required/>
                        <div v-if="errors.name" class="text-danger">{{ errors.name }}</div>
                    </div>

                    <div class="w-full md:w-1/2 px-3 mb-4">
                        <Label for="description" value="Descrição do Curso"/>
                        <Input id="description" v-model="form.description" type="text" class="mt-1 block w-full" required/>
                        <div v-if="errors.description" class="text-danger">{{ errors.description }}</div>
                    </div>

                    <div class="w-full flex flex-wrap px-3 mb-4">
                        <div class="w-1/2 pr-2">
                            <Label for="start_date" value="Data de Início"/>
                            <Input id="start_date" v-model="form.start_date" type="date" class="mt-1 block w-full" required/>
                            <div v-if="errors.start_date" class="text-danger">{{ errors.start_date }}</div>
                        </div>
                        <div class="w-1/2 pl-2">
                            <Label for="end_date" value="Data de Término"/>
                            <Input id="end_date" v-model="form.end_date" type="date" class="mt-1 block w-full" required/>
                            <div v-if="errors.end_date" class="text-danger">{{ errors.end_date }}</div>
                        </div>
                    </div>

                    <div class="w-full md:w-1/2 px-3 mb-4">
                        <Label for="price" value="Preço"/>
                        <Input id="price" v-model="form.price" type="number" class="mt-1 block w-full" required/>
                        <div v-if="errors.price" class="text-danger">{{ errors.price }}</div>
                    </div>

                    <div class="w-full flex flex-wrap px-3 mb-4">
                        <div class="w-1/2 pr-2">
                            <Label for="remaining_slots" value="Vagas Restantes"/>
                            <Input id="remaining_slots" v-model="form.remaining_slots" type="number" class="mt-1 block w-full" required/>
                            <div v-if="errors.remaining_slots" class="text-danger">{{ errors.remaining_slots }}</div>
                        </div>
                        <div class="mb-4">
                            <label for="is_active" class="block text-sm font-medium text-gray-700">Ativo</label>
                            <input ref="checkboxRef" type="checkbox" id="is_active" class="mt-1" v-model="form.is_active">
                        </div>
                    </div>

                    <div class="w-full px-3">
                        <Button type="submit">Salvar Alterações</Button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.container {
    max-width: 600px;
}
</style>
