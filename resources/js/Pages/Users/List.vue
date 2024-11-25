<script setup>
import { ref, computed, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { IconSquareEditOutline, IconTrash} from '@iconify-prerendered/vue-mdi';
import Button from "@/Components/PrimaryButton.vue";
import { toast } from "vue3-toastify";

const props = defineProps({
    users: Array,
    flash: Object
});

const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = 10;

const filteredUsers = computed(() => {
    return props.users.filter(user => {
        return user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            user.email.toLowerCase().includes(searchQuery.value.toLowerCase());
    });
});

const paginatedUsers = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filteredUsers.value.slice(start, end);
});

const totalPages = computed(() => {
    return Math.ceil(filteredUsers.value.length / itemsPerPage);
});

const deleteUser = (id) => {
    if (confirm('Tem certeza que deseja apagar este usuário?')) {
        router.delete(route('users.destroy', { id }), {
            onError: (errors) => {
                console.log(errors);
            }
        });
    }
};

watch(() => props.flash, (newFlash) => {
    if (newFlash.success) {
        toast.success(newFlash.success, {
            autoClose: 5000,
        });
    }
    if (newFlash.error) {
        toast.error(newFlash.error, {
            autoClose: 5000,
        });
    }
}, { immediate: true });
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Lista de Usuários</h2>
                <input v-model="searchQuery" type="text" placeholder="Buscar..." class="ml-4 px-4 py-2 border rounded-lg">
                <Button @click="$inertia.get(route('users.create'))">Novo Usuário</Button>
            </div>
        </template>

        <div class="mt-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left w-1/4">Nome</th>
                            <th class="px-4 py-2 text-left w-1/4">Email</th>
                            <th class="px-4 py-2 text-left w-1/4">Tipo</th>
                            <th class="px-4 py-2 text-left w-1/4">Ativo</th>
                            <th class="px-4 py-2 flex space-x-2 w-full"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="user in paginatedUsers" :key="user.id" class="border-b">
                            <td class="px-4 py-2">{{ user.name }}</td>
                            <td class="px-4 py-2">{{ user.email }}</td>
                            <td class="px-4 py-2">{{ user.type === 0 ? 'Admin' : 'Aluno' }}</td>
                            <td class="px-4 py-2">{{ user.is_active ? 'Sim' : 'Não' }}</td>
                            <td class="px-4 py-2 flex space-x-2">
                                <Link :href="route('users.edit', { id: user.id })" class="px-2 py-2 text-black rounded-lg hover:bg-yellow-600">
                                    <IconSquareEditOutline class="w-6 h-6" />
                                </Link>
                                <button @click="deleteUser(user.id)" class="px-2 py-2 text-black rounded-lg hover:bg-red-600">
                                    <IconTrash class="w-6 h-6" />
                                </button>
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
