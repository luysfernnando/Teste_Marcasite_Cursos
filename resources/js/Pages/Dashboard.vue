<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    totalCourses: Object,
    totalStudents: Object,
    totalAdmins: Object,
    approvedPurchases: Object,
    pendingPurchases: Object,
    canceledPurchases: Object,
    flash: Object
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold">Quantidade de Cursos</h3>
                        <p class="text-2xl">{{ totalCourses }}</p>
                    </div>
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold">Quantidade de Alunos no Total</h3>
                        <p class="text-2xl">{{ totalStudents }}</p>
                    </div>
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold">Quantidade de Admins</h3>
                        <p class="text-2xl">{{ totalAdmins }}</p>
                    </div>
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold">Compras Aprovadas</h3>
                        <p class="text-2xl">{{ approvedPurchases }}</p>
                    </div>
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold">Compras Pendentes</h3>
                        <p class="text-2xl">{{ pendingPurchases }}</p>
                    </div>
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold">Compras Canceladas</h3>
                        <p class="text-2xl">{{ canceledPurchases }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
