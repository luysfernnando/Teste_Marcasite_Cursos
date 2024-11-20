<script setup>
import {ref, watch} from 'vue';
import {Head, useForm} from '@inertiajs/vue3';
import Button from '@/Components/PrimaryButton.vue';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

// Recebendo os alunos e o curso como props
const props = defineProps({
    course: Object,
    students: Array
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

// Preencher os dados automaticamente
const selectedStudent = ref(null);

watch(() => form.student_id, (newStudentId) => {
    selectedStudent.value = props.students.find(student => student.id === newStudentId);
    if (selectedStudent.value) {
        form.name = selectedStudent.value.name;
        form.cpf = selectedStudent.value.cpf;
        form.email = selectedStudent.value.email;
    } else {
        form.name = '';
        form.cpf = '';
        form.email = '';
    }
});

// Envio do formulário
const submit = () => {
    form.post(route('registration.store', {course: props.course.id}));
};
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
                                <label class="block text-sm font-medium text-gray-700" for="student_id">Selecione o
                                    Aluno</label>
                                <select id="student_id" v-model="form.student_id" class="mt-1 block w-full" required>
                                    <option value="">Selecione um aluno</option>
                                    <option v-for="student in students" :key="student.id" :value="student.id">
                                        {{ student.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Nome  -->
                            <div class="w-1/2 pr-2">
                                <label class="block text-sm font-medium text-gray-700" for="name">Nome</label>
                                <input id="name" v-model="form.name" class="mt-1 block w-full" readonly type="text"/>
                            </div>
                        </div>

                        <div class="mb-4 flex">
                            <!-- CPF -->
                            <div class="w-1/2 pr-2">
                                <label class="block text-sm font-medium text-gray-700" for="cpf">CPF</label>
                                <input id="cpf" v-model="form.cpf" class="mt-1 block w-full" required type="text"/>
                            </div>

                            <!-- E-mail -->
                            <div class="w-1/2 pr-2">
                                <label class="block text-sm font-medium text-gray-700" for="email">E-mail</label>
                                <input id="email" v-model="form.email" class="mt-1 block w-full" readonly type="email"/>
                            </div>
                        </div>

                        <div class="mb-4 flex">
                            <!-- Senha -->
                            <div class="w-1/2 pr-2">
                                <label class="block text-sm font-medium text-gray-700" for="password">Senha</label>
                                <input id="password" v-model="form.password" class="mt-1 block w-full" required
                                       type="password"/>
                            </div>

                            <!-- Confirmação de Senha -->
                            <div class="w-1/2 pr-2">
                                <label class="block text-sm font-medium text-gray-700" for="password_confirmation">Confirmar
                                    Senha</label>
                                <input id="password_confirmation" v-model="form.password_confirmation"
                                       class="mt-1 block w-full"
                                       required type="password"/>
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
