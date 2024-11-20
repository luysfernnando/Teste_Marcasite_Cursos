<script setup>
import {onMounted, ref, watch} from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { IconAccountEdit, IconTrash } from '@iconify-prerendered/vue-mdi';
import Button from "@/Components/PrimaryButton.vue";
import {toast} from "vue3-toastify";

// Recebendo os props
const props = defineProps({
    users: Array,
    flash: Object
});

// Função para apagar usuário
const deleteUser = (id) => {
    if (confirm('Tem certeza que deseja apagar este usuário?')) {
        router.delete(route('users.destroy', { id }), {
            onError: (errors) => {
                console.log(errors);
            }
        });
    }
};

// Exibir notificações com base na propriedade flash
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
                <Button @click="$inertia.get(route('users.create'))">Novo Usuário</Button>
            </div>
        </template>


        <div class="mt-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">Nome</th>
                            <th class="px-4 py-2 text-left">Email</th>
                            <th class="px-4 py-2 text-left">Tipo</th>
                            <th class="px-4 py-2 text-left">Ativo</th>
                            <th class="px-4 py-2 text-left"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="user in users" :key="user.id" class="border-b">
                            <td class="px-4 py-2">{{ user.name }}</td>
                            <td class="px-4 py-2">{{ user.email }}</td>
                            <td class="px-4 py-2">{{ user.type === 0 ? 'Admin' : 'Aluno' }}</td>
                            <td class="px-4 py-2">{{ user.is_active ? 'Sim' : 'Não' }}</td>
                            <td class="px-4 py-2 flex space-x-2">
                                <Link :href="route('users.edit', { id: user.id })" class="hover:text-blue-600">
                                    <IconAccountEdit />
                                </Link>
                                <button @click="deleteUser(user.id)" class="hover:text-red-600">
                                    <IconTrash />
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
