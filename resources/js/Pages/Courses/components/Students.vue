<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Button from '@/Components/PrimaryButton.vue';
import {watch} from "vue";
import {toast} from "vue3-toastify";

// Recebendo os props
const props = defineProps({
    course: Object,
    students: Array,
    flash: Object
});

const paymentStatus = {
    0: { text: 'Aguardando Pagamento', color: 'text-orange-500' },
    1: { text: 'Pago', color: 'text-green-500' },
    2: { text: 'Cancelado', color: 'text-red-500' }
};

function formatCurrency(value) {
    if (value == null) return '';
    return value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }).replace('R$', '');
}

console.log(props);

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
    <Head title="Alunos do Curso" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Alunos do curso: {{ course.name }}</h2>
                <Button @click="$inertia.get(route('registration.get', { course: course.id }))">Nova Inscrição</Button>
            </div>
        </template>

        <div class="mt-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">Nome</th>
                            <th class="px-4 py-2 text-left">Data de Inscrição</th>
                            <th class="px-4 py-2 text-left">E-mail</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Valor Pago</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="student in students" :key="student.id" class="border-b">
                            <td class="px-4 py-2">{{ student.name }}</td>
                            <td class="px-4 py-2">{{ new Date(student.pivot?.created_at).toLocaleDateString('pt-BR') }}</td>
                            <td class="px-4 py-2">{{ student.email }}</td>
                            <td class="px-4 py-2" :class="`${paymentStatus[student.pivot?.payment_status].color}`">
                                {{ paymentStatus[student.pivot?.payment_status].text }}
                            </td>
                            <td class="px-4 py-2">R$ {{ formatCurrency(student.pivot?.paid_value) }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
