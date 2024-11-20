<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Button from '@/Components/PrimaryButton.vue';

const props = defineProps(['courses']);
const showForm = ref(false);
const form = useForm({
    name: '',
    description: '',
    price: '',
    is_active: false,
    start_date: '',
    end_date: '',
    remaining_slots: '',
});

const submit = () => {
    form.post(route('courses.store'), {
        onSuccess: () => {
            showForm.value = false;
            form.reset();
        },
        onError: (errors) => {
            console.log(errors);
        }
    });
};

function formatCurrency(value) {
    if (value == null) return '';
    return value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }).replace('R$', '');
}

console.log(props, 'props');
</script>

<template>
    <Head title="Cursos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Lista de Cursos</h2>
                <Button @click="showForm = !showForm">Novo Curso</Button>
            </div>
        </template>

        <div class="mt-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div v-if="showForm" class="bg-white shadow rounded-lg p-6">
                <form @submit.prevent="submit">
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
                        <input v-model="form.name" type="text" id="name" class="mt-1 block w-full" required>
                    </div>
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">Descrição</label>
                        <input v-model="form.description" type="text" id="name" class="mt-1 block w-full" required>
                    </div>
                    <div class="mb-4">
                        <label for="price" class="block text-sm font-medium text-gray-700">Valor</label>
                        <input v-model="form.price" type="number" id="price" class="mt-1 block w-full" required>
                    </div>
                    <div class="mb-4">
                        <label for="is_active" class="block text-sm font-medium text-gray-700">Ativo</label>
                        <input v-model="form.is_active" type="checkbox" id="is_active" class="mt-1" :true-value="1" :false-value="0">
                    </div>
                    <div class="mb-4">
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Data de Início</label>
                        <input v-model="form.start_date" type="date" id="start_date" class="mt-1 block w-full" required>
                    </div>
                    <div class="mb-4">
                        <label for="end_date" class="block text-sm font-medium text-gray-700">Data de Término</label>
                        <input v-model="form.end_date" type="date" id="end_date" class="mt-1 block w-full" required>
                    </div>
                    <div class="mb-4">
                        <label for="remaining_slots" class="block text-sm font-medium text-gray-700">Vagas Restantes</label>
                        <input v-model="form.remaining_slots" type="number" id="remaining_slots" class="mt-1 block w-full" required>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Salvar</button>
                    </div>
                </form>
            </div>

            <div v-else class="bg-white shadow rounded-lg p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">Nome</th>
                            <th class="px-4 py-2 text-left">Descrição</th>
                            <th class="px-4 py-2 text-left">Valor</th>
                            <th class="px-4 py-2 text-left">Ativo</th>
                            <th class="px-4 py-2 text-left">Período de Inscrição</th>
                            <th class="px-4 py-2 text-left">Vagas Restantes</th>
                            <th class="px-4 py-2 text-left"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="course in courses" :key="course.id" class="border-b">
                            <td class="px-4 py-2">{{ course.name }}</td>
                            <td class="px-4 py-2">{{ course.description }}</td>
                            <td class="px-4 py-2">R$ {{ formatCurrency(course.price) }}</td>
                            <td class="px-4 py-2">{{ course.is_active ? 'Sim' : 'Não' }}</td>
                            <td class="px-4 py-2">{{ new Date(course.start_date).toLocaleDateString('pt-BR') }} até {{ new Date(course.end_date).toLocaleDateString('pt-BR') }}</td>
                            <td class="px-4 py-2">{{ course.remaining_slots }}</td>
                            <td class="px-4 py-2">
                                <div class="flex space-x-2">
                                    <Link
                                        :href="route('students.list', { id: course.id })"
                                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600"
                                    >
                                        Ver Alunos
                                    </Link>
                                    <Link
                                        :href="route('courses.edit', { id: course.id })"
                                        class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600"
                                    >
                                        Editar
                                    </Link>
                                    <Link
                                        :href="route('courses.delete', { id: course.id })"
                                        method="delete"
                                        as="button"
                                        class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600"
                                    >
                                        Apagar
                                    </Link>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
