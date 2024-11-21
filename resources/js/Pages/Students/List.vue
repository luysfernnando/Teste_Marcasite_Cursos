<script setup>
import {ref, computed, watch} from 'vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Button from '@/Components/PrimaryButton.vue';
import {toast} from "vue3-toastify";
import {IconSquareEditOutline, IconTrash} from "@iconify-prerendered/vue-mdi";
import {jsPDF} from 'jspdf';
import * as XLSX from 'xlsx';

const props = defineProps({
    course: Object,
    students: Array,
    flash: Object
});

const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = 10;

const paymentStatus = {
    0: { text: 'Aguardando Pagamento', color: 'text-orange-500' },
    1: { text: 'Pago', color: 'text-green-500' },
    2: { text: 'Cancelado', color: 'text-red-500' }
};

const filteredStudents = computed(() => {
    return props.students.filter(student => {
        return student.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            student.email.toLowerCase().includes(searchQuery.value.toLowerCase());
    });
});

const paginatedStudents = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filteredStudents.value.slice(start, end);
});

const totalPages = computed(() => {
    return Math.ceil(filteredStudents.value.length / itemsPerPage);
});

function formatCurrency(value) {
    if (value == null) return '';
    return value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }).replace('R$', '');
}

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

const exportToPDF = () => {
    const doc = new jsPDF();
    doc.setFontSize(12);

    let y = 20;
    doc.text('Lista de Alunos - Curso: ' + props.course.name, 10, y);
    y += 10;

    // Adicionando as colunas
    doc.text('Nome', 10, y);
    doc.text('Data de Inscrição', 60, y);
    doc.text('E-mail', 110, y);
    doc.text('Status', 160, y);
    doc.text('Valor Pago', 210, y);
    y += 10;

    // Adicionando as linhas dos alunos
    paginatedStudents.value.forEach(student => {
        doc.text(student.name, 10, y);
        doc.text(new Date(student.pivot?.created_at).toLocaleDateString('pt-BR'), 60, y);
        doc.text(student.email, 110, y);
        doc.text(paymentStatus[student.pivot?.payment_status].text, 160, y);
        doc.text('R$ ' + formatCurrency(student.pivot?.paid_value), 210, y);
        y += 10;
    });

    doc.save('students_list.pdf');
};

const exportToExcel = () => {
    const ws = XLSX.utils.json_to_sheet(paginatedStudents.value.map(student => ({
        Nome: student.name,
        'Data de Inscrição': new Date(student.pivot?.created_at).toLocaleDateString('pt-BR'),
        'E-mail': student.email,
        Status: paymentStatus[student.pivot?.payment_status].text,
        'Valor Pago': 'R$ ' + formatCurrency(student.pivot?.paid_value)
    })));

    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Alunos');

    XLSX.writeFile(wb, 'students_list.xlsx');
};
</script>

<template>
    <Head title="Alunos do Curso" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Alunos do curso: {{ course.name }}</h2>
                <input v-model="searchQuery" type="text" placeholder="Buscar..."
                       class="ml-4 px-4 py-2 border rounded-lg">
                <Button @click="$inertia.get(route('registration.get', { course: course.id }))">Nova Inscrição</Button>
            </div>
        </template>

        <div class="mt-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex mb-4">
                    <button @click="exportToPDF"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg mr-4 hover:bg-blue-700">
                        Exportar para PDF
                    </button>
                    <button @click="exportToExcel"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Exportar para Excel
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">Nome</th>
                            <th class="px-4 py-2 text-left">Data de Inscrição</th>
                            <th class="px-4 py-2 text-left">E-mail</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Valor Pago</th>
                            <th class="px-4 py-2 text-left"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="student in paginatedStudents" :key="student.id" class="border-b">
                            <td class="px-4 py-2">{{ student.name }}</td>
                            <td class="px-4 py-2">{{ new Date(student.pivot?.created_at).toLocaleDateString('pt-BR') }}</td>
                            <td class="px-4 py-2">{{ student.email }}</td>
                            <td class="px-4 py-2" :class="`${paymentStatus[student.pivot?.payment_status].color}`">
                                {{ paymentStatus[student.pivot?.payment_status].text }}
                            </td>
                            <td class="px-4 py-2">R$ {{ formatCurrency(student.pivot?.paid_value) }}</td>
                            <td class="px-4 py-2 flex space-x-2">
                                <Link v-if="student.id" :href="route('students.edit', { id: student.pivot?.id })"
                                      class="px-2 py-2 text-black rounded-lg hover:bg-yellow-600">
                                    <IconSquareEditOutline class="w-6 h-6"/>
                                </Link>
                                <Link v-if="student.id" :href="route('students.destroy', { id: student.pivot?.id })"
                                      method="delete" class="px-2 py-2 text-black rounded-lg hover:bg-red-600">
                                    <IconTrash class="w-6 h-6"/>
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
