<script setup>
import {ref, watch} from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { toast } from "vue3-toastify";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import CpfInput from "@/Components/CpfInput.vue";

// Recebendo os props (para edição de usuário)
const props = defineProps({
    user: Object,
    flash: Object
});

// Definir se estamos criando ou editando
const isEditing = ref(!!props.user);

// Formulário de usuário
const form = useForm({
    name: props.user?.name || '',
    type: props.user?.type ?? 1,
    is_active: props.user?.is_active ?? 1,
    email: props.user?.email || '',
    cpf: props.user?.cpf || '',
    password: '',
    password_confirmation: ''
});

// Função para enviar o formulário (criar ou editar)
const submitForm = () => {
    if (isEditing.value) {
        // Atualizar usuário
        router.put(route('users.update', { id: props.user.id }), form, {
            onError: (errors) => {
                toast.error('Não foi possível atualizar o usuário', {
                    autoClose: 1000,
                });
                console.log(errors);
            }
        });
    } else {
        // Criar novo usuário
        router.post(route('users.store'), form, {
            onError: (errors) => {
                console.log(errors);
            }
        });
    }
};

const uploadPhoto = (event) => {
    const file = event.target.files[0];
    if (file) {
        const formData = new FormData();
        formData.append('photo', file);

        // Verificar se estamos criando ou editando
        const url = isEditing.value
            ? route('users.uploadPhoto', { id: props.user.id })
            : route('users.uploadPhoto', { id: 'new' });

        router.post(url, formData, {
            onSuccess: () => {
                toast.success('Foto de perfil atualizada com sucesso!', {
                    autoClose: 1000,
                });
            },
            onError: (errors) => {
                toast.error('Não foi possível atualizar a foto de perfil', {
                    autoClose: 1000,
                });
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
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ isEditing ? 'Editar Usuário' : 'Criar Novo Usuário' }}
            </h2>
        </template>

        <div class="mt-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <form @submit.prevent="submitForm" class="flex">
                    <div class="w-2/3 pr-4">
                        <div class="mb-4 flex">
                            <div class="w-1/2 pr-2">
                                <InputLabel class="block text-gray-700" required>Nome*</InputLabel>
                                <TextInput v-model="form.name" type="text" class="mt-1 block w-full" label="Nome" required />
                            </div>

                            <div class="w-1/2 pr-2">
                                <InputLabel class="block text-gray-700" required>Tipo de usuário*</InputLabel>
                                <select v-model="form.type" class="mt-1 block w-full border-gray-300 rounded-md">
                                    <option value="0">Admin</option>
                                    <option value="1">Aluno</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <InputLabel class="block text-gray-700" required>Email*</InputLabel>
                            <TextInput v-model="form.email" type="email" class="mt-1 block w-full" label="Email" required />
                        </div>

                        <div class="mb-4 flex">
                            <div class="w-1/2 pr-2">
                                <InputLabel class="block text-gray-700" required>CPF*</InputLabel>
                                <CpfInput v-model="form.cpf" type="text" class=" w-full" label="CPF" required/>
                            </div>
                            <div class="w-1/2 pl-2">
                                <InputLabel class="block text-gray-700" required>Ativo*</InputLabel>
                                <select v-model="form.is_active" class="mt-1 block w-full border-gray-300 rounded-md">
                                    <option value="1">Sim</option>
                                    <option value="0">Não</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4 flex">
                            <div class="w-1/2 pr-2">
                                <InputLabel
                                    class="block text-gray-700" required>Senha</InputLabel>
                                <TextInput
                                    v-model="form.password"
                                    type="password"
                                    class="mt-1 block w-full"
                                    label="Senha"
                                    :required="!isEditing"
                                    placeholder="Digite a senha..."
                                />
                            </div>

                            <div class="w-1/2 pr-2">
                                <InputLabel class="block text-gray-700" required>Confirmar Senha</InputLabel>
                                <TextInput
                                    v-model="form.password_confirmation"
                                    type="password"
                                    class="mt-1 block w-full"
                                    label="Confirmar Senha"
                                    :required="!isEditing || form.password !== ''"
                                    placeholder="Repita a senha..."
                                />
                            </div>
                        </div>
                    </div>

                    <div class="w-1/3 pl-4 flex flex-col items-center">
                        <InputLabel class="block text-gray-700" required>Imagem do Usuário</InputLabel>
                        <img :src="props.user?.profile_photo_path ? '/storage/' + props.user.profile_photo_path : 'https://cdn0.iconfinder.com/data/icons/user-pictures/100/unknown2-512.png'"
                             alt="Foto de Perfil"
                             class="w-32 h-32 rounded-full mb-4"
                        >
                        <input type="file" @change="uploadPhoto" class="mb-4">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ isEditing ? 'Atualizar Usuário' : 'Criar Usuário' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
