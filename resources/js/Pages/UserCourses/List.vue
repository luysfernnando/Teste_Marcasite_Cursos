<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
import {ref, onMounted, computed, watch} from 'vue';
import {usePage} from '@inertiajs/vue3';
import Input from "@/Components/TextInput.vue";
import Button from "@/Components/PrimaryButton.vue";
import {IconEye} from '@iconify-prerendered/vue-mdi';

const {props} = usePage();

defineProps({
    registrations: {
        type: Array,
        required: true,
    },
});

const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = 10;

const filteredRegistrations = computed(() => {
    return props.registrations.filter(registration => {
        return registration.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            registration.enrolled_at.toLowerCase().includes(searchQuery.value.toLowerCase());
    });
});

const paginatedRegistrations = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filteredRegistrations.value.slice(start, end);
});

const totalPages = computed(() => {
    return Math.ceil(filteredRegistrations.value.length / itemsPerPage);
});

const paymentStatus = {
    0: { text: 'Aguardando Pagamento', color: 'text-orange-500' },
    1: { text: 'Pago', color: 'text-green-500' },
    2: { text: 'Cancelado', color: 'text-red-500' }
};

onMounted(() => {
    if (props.flash?.success) {
        toast.success(props.flash.success);
    } else if (props.flash?.error) {
        toast.error(props.flash.error);
    }
});

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

function formatCurrency(value) {
    if (value == null) return '';
    return value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}

</script>

<template>
    <Head title="Meus Cursos"/>

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Meus Cursos</h2>
                <input v-model="searchQuery" type="text" placeholder="Buscar..." class="ml-4 px-4 py-2 border rounded-lg">
            </div>
        </template>

        <div class="mt-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left w-1/4">Nome</th>
                            <th class="px-4 py-2 text-left w-1/4">Data de Inscrição</th>
                            <th class="px-4 py-2 text-left w-1/4">Status</th>
                            <th class="px-4 py-2 text-left w-1/4">Valor Pago</th>
                            <th class="px-4 py-2 text-left w-full"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="registration in paginatedRegistrations" :key="registration.id" class="border-b">
                            <td class="px-4 py-2">{{ registration.name }}</td>
                            <td class="px-4 py-2">{{ new Date(registration.enrolled_at).toLocaleDateString('pt-BR') }}</td>
                            <td class="px-4 py-2" :class="`${paymentStatus[registration?.payment_status].color}`">
                                {{ paymentStatus[registration?.payment_status].text }}
                            </td>

                            <td class="px-4 py-2">R$ {{ formatCurrency(registration.paid_value) }}</td>
                            <td class="px-4 py-2 flex space-x-2">
                                <Link :href="route('user_courses.edit', registration.course_id)"
                                      class="px-2 py-2 text-black rounded-lg hover:bg-blue-600 hover:text-white">
                                    <IconEye class="w-6 h-6" />
                                </Link>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-between mt-4">
                    <button @click="currentPage = Math.max(currentPage - 1, 1)" :disabled="currentPage === 1"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Anterior
                    </button>
                    <span>Página {{ currentPage }} de {{ totalPages }}</span>
                    <button @click="currentPage = Math.min(currentPage + 1, totalPages)"
                            :disabled="currentPage === totalPages"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Próxima
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
