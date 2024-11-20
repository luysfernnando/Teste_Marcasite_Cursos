<script setup>
import {Head, useForm} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {ref, watch} from 'vue';
import {toast} from 'vue3-toastify';

// Recebendo os props
const props = defineProps({
    registration: Object,
    course: Object,
    student: Object,
    flash: Object,
});

console.log(props.registration.id);

const paymentStatusOptions = ref([
    {value: '0', label: 'Aguardando Pagamento'},
    {value: '1', label: 'Pago'},
    {value: '2', label: 'Cancelado'},
]);

// Usando o Inertia useForm para lidar com o formulário
const form = useForm({
    name: props.student?.name,
    email: props.student?.email,
    payment_status: props.registration?.payment_status,
    paid_value: props.registration?.paid_value,
});

function submit() {
    form.put(route('students.update', {id: props.registration.id}), {
        onSuccess: () => toast.success('Inscrição atualizada com sucesso!'),
        onError: () => toast.error('Ocorreu um erro ao atualizar a inscrição.'),
    });
}

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
}, {immediate: true});
</script>

<template>
    <Head title="Editar Inscrição"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Editar inscrição do Curso: {{
                    course?.name
                }}</h2>
        </template>

        <div class="mt-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <form @submit.prevent="submit">
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nome 🔒</label>
                        <input v-model="form.name" type="text" id="name"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" disabled/>
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">E-mail 🔒</label>
                        <input v-model="form.email" type="email" id="email"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" disabled/>
                    </div>

                    <div class="mb-4">
                        <label for="payment_status" class="block text-sm font-medium text-gray-700">Status de
                            Pagamento</label>
                        <select v-model="form.payment_status" id="payment_status"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option v-for="status in paymentStatusOptions" :key="status.value" :value="status.value">
                                {{ status.label }}
                            </option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="paid_value" class="block text-sm font-medium text-gray-700">Valor Pago</label>
                        <input v-model="form.paid_value" type="number" step="0.01" id="paid_value"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
