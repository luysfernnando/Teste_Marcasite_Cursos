<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";

const { props } = usePage();
const course = props.course || {};

course.paid_value = formatCurrency(course.paid_value);

function formatCurrency(value) {
    if (value == null) return '';
    return value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
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

                    <div>
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

                    <div>
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

                    <div>
                        <InputLabel class="block text-gray-700" required>Status</InputLabel>
                        <TextInput
                            id="status"
                            label="Status"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="course.payment_status"
                            disabled
                        />
                    </div>

                    <div class="md:col-span-2">
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
        </div>
    </AuthenticatedLayout>
</template>
