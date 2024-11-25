<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, usePage} from '@inertiajs/vue3';
import {ref, onMounted, computed, watch} from 'vue';
import {toast} from 'vue3-toastify';
import {loadStripe} from '@stripe/stripe-js';
import {IconImageOff} from '@iconify-prerendered/vue-mdi';

const {props} = usePage();

defineProps({
    courses: {
        type: Array,
        required: true,
    },
    registered_courses: {
        type: Array,
        required: true,
    },
});

const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = 9;

const filteredCourses = computed(() => {
    return props.courses.filter(course => {
        return course.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            course.description.toLowerCase().includes(searchQuery.value.toLowerCase());
    });
});

const paginatedCourses = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filteredCourses.value.slice(start, end);
});

const totalPages = computed(() => {
    return Math.ceil(filteredCourses.value.length / itemsPerPage);
});

onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const courseId = urlParams.get('course');
    const enrolled = urlParams.get('enrolled');

    if (courseId && enrolled) {
        axios.post(route('enroll_user', { course: courseId }))
            .then(() => toast.success('Curso Comprado com sucesso!'))
            .catch(() => toast.error('Erro ao comprar o curso.'));
    }
});

watch(() => props?.flash, (newFlash) => {
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

const purchaseCourse = async (course) => {
    const stripe = await loadStripe(import.meta.env.VITE_STRIPE_KEY);
    const response = await axios.post(route('purchase.course'), {
        course_id: course.id,
        course_name: course.name,
        price: course.price,
    });

    const sessionId = response.data.id;

    await stripe.redirectToCheckout({sessionId});
};

const isRegistered = (courseId) => {
    return props.registered_courses.some(course => course.id === courseId);
};

function formatCurrency(value) {
    if (value == null) return '';
    return value.toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'});
}

</script>

<template>
    <Head title="Vitrine de Cursos"/>

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Vitrine de Cursos</h2>
                <input v-model="searchQuery" type="text" placeholder="Buscar..."
                       class="ml-4 px-4 py-2 border rounded-lg">
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    <div v-for="course in paginatedCourses" :key="course.id"
                         class="bg-white shadow rounded-lg p-4 flex flex-col justify-between">
                        <div class="flex flex-col items-center">
                            <img
                                v-if="course.image_path"
                                :src="course.image_path ? '/storage/' + course.image_path : 'https://via.placeholder.com/150'"
                                alt="Miniatura do curso"
                                class="w-full h-40 object-cover mb-4"
                            />
                            <div v-else class="text-gray-500 mb-4">
                                <IconImageOff class="w-full h-40"/>
                            </div>
                        </div>
                        <div class="flex flex-col items-start">
                            <h3 class="text-2xl font-semibold text-gray-900">{{ course.name }}</h3>
                            <p class="text-gray-500">Período de Inscrição:
                                {{ new Date(course.start_date).toLocaleDateString('pt-BR') }} -
                                {{ new Date(course.end_date).toLocaleDateString('pt-BR') }}</p>
                            <p class="text-gray-500">Descrição: {{ course.description }}</p>
                            <p class="text-gray-500">Vagas restantes: {{ course.remaining_slots }}</p>
                        </div>
                        <div class="flex justify-between items-center mt-4">
                            <p class="text-lg text-blue-600">R$ {{ formatCurrency(course.price) }}</p>
                            <button
                                :class="{
                                    'bg-green-500 hover:bg-green-400': isRegistered(course.id),
                                    'bg-red-500 hover:bg-red-400': course.remaining_slots === 0 || isRegistered(course.id),
                                    'bg-blue-700 hover:bg-blue-600': course.remaining_slots > 0 && !isRegistered(course.id),
                                    'text-white px-4 py-2 rounded-lg text-sm w-1/2': true
                                }"
                                :disabled="isRegistered(course.id) || course.remaining_slots === 0"
                                @click="purchaseCourse(course)"
                            >
                                {{
                                    isRegistered(course.id) ? 'Já Matriculado' : (course.remaining_slots === 0 ? 'Esgotado!' : 'Comprar')
                                }}
                            </button>
                        </div>
                    </div>
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
