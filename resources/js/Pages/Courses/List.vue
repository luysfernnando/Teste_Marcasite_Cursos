<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Input from '@/Components/TextInput.vue';
import Label from '@/Components/InputLabel.vue';
import Button from '@/Components/PrimaryButton.vue';
import { IconPeople, IconTrash, IconSquareEditOutline } from "@iconify-prerendered/vue-mdi";

const props = defineProps(['courses']);
const showForm = ref(false);
const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = 10;

const form = useForm({
    name: '',
    description: '',
    price: '',
    is_active: 1,
    start_date: '',
    end_date: '',
    remaining_slots: '',
    photo: null,
});

const errors = ref({});
const photoPreviewUrl = ref(null);

const filteredCourses = computed(() => {
    return props.courses.filter(course => {
        return course.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            course.description.toLowerCase().includes(searchQuery.value.toLowerCase());
    });
});

const paginatedCourses = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filteredCourses.value.slice(start, end);
});

const totalPages = computed(() => {
    return Math.ceil(filteredCourses.value.length / itemsPerPage);
});

const submit = () => {
    const formData = new FormData();
    formData.append('name', form.name);
    formData.append('description', form.description);
    formData.append('price', form.price);
    formData.append('is_active', form.is_active);
    formData.append('start_date', form.start_date);
    formData.append('end_date', form.end_date);
    formData.append('remaining_slots', form.remaining_slots);
    if (form.photo) {
        formData.append('photo', form.photo);
    }

    router.post(route('courses.store'), formData, {
        onSuccess: () => {
            showForm.value = false;
            form.reset();
            photoPreviewUrl.value = null;
        },
        onError: (err) => {
            errors.value = err;
            console.log(errors.value);
        }
    });
};

const handleFileChange = (event) => {
    const file = event.target.files[0];
    form.photo = file;

    if (file) {
        photoPreviewUrl.value = URL.createObjectURL(file);
    }
};

function formatCurrency(value) {
    if (value == null) return '';
    return value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }).replace('R$', '');
}
</script>


<template>
    <Head title="Cursos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Lista de Cursos</h2>
                <input v-if="!showForm" v-model="searchQuery" type="text" placeholder="Buscar..." class="ml-4 px-4 py-2 border rounded-lg">
                <Button @click="showForm = !showForm">{{ showForm ? 'Voltar' : 'Novo Curso' }}</Button>
            </div>
        </template>

        <div class="mt-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div v-if="showForm" class="bg-white shadow rounded-lg p-6 flex">
                <div class="w-2/3 pr-4">
                    <form @submit.prevent="submit">
                        <div class="mb-4 flex">
                            <div class="w-1/2 pr-2">
                                <Label for="name" class="block text-sm font-medium text-gray-700">Nome do Curso</Label>
                                <Input v-model="form.name" type="text" id="name" class="mt-1 block w-full" required/>
                                <div v-if="errors.name" class="text-danger">{{ errors.name }}</div>
                            </div>
                            <div class="w-1/2 pr-2">
                                <Label for="price" class="block text-sm font-medium text-gray-700">Preço</Label>
                                <Input v-model="form.price" type="number" id="price" class="mt-1 block w-full" required/>
                                <div v-if="errors.price" class="text-danger">{{ errors.price }}</div>
                            </div>
                        </div>

                        <div class="mb-4 flex">
                            <div class="w-1/2 pr-2">
                                <Label for="start_date" class="block text-sm font-medium text-gray-700">Data de Início</Label>
                                <Input v-model="form.start_date" type="date" id="start_date" class="mt-1 block w-full" required/>
                                <div v-if="errors.start_date" class="text-danger">{{ errors.start_date }}</div>
                            </div>
                            <div class="w-1/2 pr-2">
                                <Label for="end_date" class="block text-sm font-medium text-gray-700">Data de Término</Label>
                                <Input v-model="form.end_date" type="date" id="end_date" class="mt-1 block w-full" required/>
                                <div v-if="errors.end_date" class="text-danger">{{ errors.end_date }}</div>
                            </div>
                        </div>

                        <div class="mb-4 flex">
                            <div class="w-1/2 pr-2">
                                <Label for="remaining_slots" class="block text-sm font-medium text-gray-700">Vagas Restantes</Label>
                                <Input v-model="form.remaining_slots" type="number" id="remaining_slots" class="mt-1 block w-full" required/>
                                <div v-if="errors.remaining_slots" class="text-danger">{{ errors.remaining_slots }}</div>
                            </div>
                            <div class="w-1/2 pr-2">
                                <Label for="is_active" class="block text-sm font-medium text-gray-700">Ativo</Label>
                                <select v-model="form.is_active" class="mt-1 block w-full border-gray-300 rounded-md">
                                    <option value="1">Sim</option>
                                    <option value="0">Não</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <Label for="description" class="block text-sm font-medium text-gray-700">Descrição</Label>
                            <Input v-model="form.description" type="text" id="description" class="mt-1 block w-full" required/>
                            <div v-if="errors.description" class="text-danger">{{ errors.description }}</div>
                        </div>

                        <div class="w-full">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Criar curso
                            </button>
                        </div>
                    </form>
                </div>

                <div class="w-1/3 flex flex-col items-center">
                    <Label for="photo" class="block text-sm font-medium text-gray-700 mb-2">Imagem do Curso</Label>

                    <!-- Pré-visualização da imagem selecionada -->
                    <div v-if="photoPreviewUrl" class="w-full">
                        <img :src="photoPreviewUrl" alt="Prévia da Imagem" class="w-full h-auto object-cover rounded-lg max-h-60" />
                    </div>

                    <Input type="file" @change="handleFileChange" class="mb-4" />
                </div>
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
                        <tr v-for="course in paginatedCourses" :key="course.id" class="border-b">
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
                                        class="px-2 py-2 text-black rounded-lg hover:bg-blue-600"
                                    >
                                        <IconPeople class="w-6 h-6" />
                                    </Link>
                                    <Link
                                        :href="route('courses.edit', { id: course.id })"
                                        class="px-2 py-2 text-black rounded-lg hover:bg-yellow-600"
                                    >
                                        <IconSquareEditOutline class="w-6 h-6" />
                                    </Link>
                                    <Link
                                        :href="route('courses.delete', { id: course.id })"
                                        method="delete"
                                        as="button"
                                        class="px-2 py-2 text-black rounded-lg hover:bg-red-600"
                                    >
                                        <IconTrash class="w-6 h-6" />
                                    </Link>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-between mt-4">
                    <button @click="currentPage = Math.max(currentPage - 1, 1)" :disabled="currentPage === 1" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Anterior</button>
                    <span>Página {{ currentPage }} de {{ totalPages }}</span>
                    <button @click="currentPage = Math.min(currentPage + 1, totalPages)" :disabled="currentPage === totalPages" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Próxima</button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
