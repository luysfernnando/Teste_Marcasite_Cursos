<script setup>
import { ref, watch } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import Button from '@/Components/PrimaryButton.vue';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Input from '@/Components/TextInput.vue';
import Label from '@/Components/InputLabel.vue';
import {toast} from "vue3-toastify";

// Recebendo os alunos e o curso como props
const props = defineProps({
    course: Object,
    students: Object,
    flash: Object,
    errors: Object,
});

// Formulário para gerenciar a inscrição
const form = useForm({
    student_id: '',
    name: '',
    cpf: '',
    email: '',
    password: '',
    password_confirmation: ''
});

// Função que preenche os dados do aluno automaticamente
const fillStudentData = (studentId) => {
    const selectedStudent = props.students[studentId];
    if (selectedStudent) {
        form.name = selectedStudent.name;
        form.cpf = selectedStudent.cpf || '';
        form.email = selectedStudent.email;
    }
};

// Observa mudanças no campo student_id para preencher os dados
watch(() => form.student_id, (newStudentId) => {
    fillStudentData(newStudentId);
});

// Envio do formulário
const submit = () => {
    form.post(route('registration.store', { course: props.course.id }), {
        onSuccess: () => {
            toast.success('Aluno registrado com sucesso!');
            form.reset();
        },
    });
};

// Exibir notificações com base na propriedade flash
watch(() => props.errors, (newFlash) => {
    if (newFlash.error) {
        toast.error(newFlash.error, {
            autoClose: 5000,
        });
    }
}, {immediate: true});
</script>

<template>
    <Head title="Inscrição para o curso"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Inscrição para o curso: {{ course.name }}
            </h2>
        </template>

        <div class="mt-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">

                <form @submit.prevent="submit">
                    <div class="w-full pr-4">

                        <div class="mb-4 flex">
                            <!-- Select de Aluno -->
                            <div class="w-1/2 pr-2">
                                <Label class="block text-sm font-medium text-gray-700" for="student_id">Selecione o Aluno</Label>
                                <select id="student_id" v-model="form.student_id" class="mt-1 block w-full border-gray-300 rounded-md" required>
                                    <option value="">Selecione um aluno</option>
                                    <option v-for="(student, index) in students" :key="student.id" :value="index">
                                        {{ student.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Nome  -->
                            <div class="w-1/2 pr-2">
                                <Label class="block text-sm font-medium text-gray-700" for="name">Nome</Label>
                                <Input id="name" v-model="form.name" class="mt-1 block w-full" readonly type="text"/>
                            </div>
                        </div>

                        <div class="mb-4 flex">
                            <!-- CPF -->
                            <div class="w-1/2 pr-2">
                                <Label class="block text-sm font-medium text-gray-700" for="cpf">CPF</Label>
                                <Input id="cpf" v-model="form.cpf" class="mt-1 block w-full" required type="text"/>
                            </div>

                            <!-- E-mail -->
                            <div class="w-1/2 pr-2">
                                <Label class="block text-sm font-medium text-gray-700" for="email">E-mail</Label>
                                <Input id="email" v-model="form.email" class="mt-1 block w-full" readonly type="email"/>
                            </div>
                        </div>

                        <div class="mb-4 flex">
                            <!-- Senha -->
                            <div class="w-1/2 pr-2">
                                <Label class="block text-sm font-medium text-gray-700" for="password">Senha</Label>
                                <Input id="password" v-model="form.password" class="mt-1 block w-full" required type="password"/>
                            </div>

                            <!-- Confirmação de Senha -->
                            <div class="w-1/2 pr-2">
                                <Label class="block text-sm font-medium text-gray-700" for="password_confirmation">Confirmar Senha</Label>
                                <Input id="password_confirmation" v-model="form.password_confirmation" class="mt-1 block w-full" required type="password"/>
                            </div>
                        </div>

                        <!-- Botão de Registrar -->
                        <div class="flex justify-end">
                            <Button :disabled="form.processing" type="submit">Registrar Aluno</Button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

