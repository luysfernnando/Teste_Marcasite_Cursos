<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";

const { props } = usePage();
const course = props.course || {};
const paymentStatus = {
    0: { text: 'Aguardando Pagamento', color: 'text-orange-500' },
    1: { text: 'Pago', color: 'text-green-500' },
    2: { text: 'Cancelado', color: 'text-red-500' }
};

course.paid_value = formatCurrency(course.paid_value);
course.enrolled_at = formatDate(course.enrolled_at);
course.payment_status = paymentStatus[course.payment_status]?.text;

function formatCurrency(value) {
    if (value == null) return '';
    return 'R$ ' + value.toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'}).replace('R$', '').trim();
}

function formatDate(value) {
    if (!value) return '';
    const date = new Date(value);
    return date.toLocaleDateString('pt-BR');
}
</script>

<template>
    <Head :title="`Informações do Curso: ${course.name}`"/>

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Informações do Curso</h2>
                <Link :href="route('user_courses.list')"
                      class="text-white bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-lg text-sm">
                    Voltar
                </Link>
            </div>
        </template>

        <div class="mt-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex">
                    <div class="w-2/3 pr-4">
                        <div class="mb-4 flex">
                            <div class="w-1/2 pr-2">
                                <InputLabel class="block text-gray-700" required>Nome do Curso</InputLabel>
                                <TextInput
                                    id="name"
                                    label="Nome do Curso"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="course.name"
                                    disabled
                                />
                            </div>

                            <div class="w-1/2 pr-2">
                                <InputLabel class="block text-gray-700" required>Valor Pago</InputLabel>
                                <TextInput
                                    id="price"
                                    label="Valor Pago"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="course.paid_value"
                                    disabled
                                />
                            </div>
                        </div>

                        <div class="mb-4 flex">
                            <div class="w-1/2 pr-2">
                                <InputLabel class="block text-gray-700" required>Data de Inscrição</InputLabel>
                                <TextInput
                                    id="enrolled_at"
                                    label="Data de Inscrição"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="course.enrolled_at"
                                    disabled
                                />
                            </div>

                            <div class="w-1/2 pr-2">
                                <InputLabel class="block text-gray-700" required>Status</InputLabel>
                                <TextInput
                                    id="status"
                                    label="Status"
                                    type="text"
                                    class="mt-1 block w-full"
                                    :class="paymentStatus[course.payment_status]?.color"
                                    v-model="course.payment_status"
                                    disabled
                                />
                            </div>
                        </div>

                        <div class="mb-4 flex">
                            <div class="w-full pr-2">
                                <InputLabel class="block text-gray-700" required>Descrição</InputLabel>
                                <TextInput
                                    id="description"
                                    label="Descrição"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="course.description"
                                    disabled
                                />
                            </div>
                        </div>
                    </div>

                    <div class="w-1/3 pl-4 flex flex-col items-center">
                        <InputLabel class="block text-gray-700" required>Imagem do Curso</InputLabel>
                        <img
                            :src="course.image_path ? '/storage/' + course.image_path : 'https://via.placeholder.com/150'"
                            alt="Imagem do Curso"
                            class="w-full h-50 mb-4 mt-1 rounded-md"
                        >
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
img {
    max-height: 13rem; /* Adjust this value as needed */
}
</style>
