<script setup>
import {watch} from 'vue';
import {toast} from 'vue3-toastify';

// Recebendo os props
const props = defineProps({
    flash: Object
});

// Exibir notificações com base na propriedade flash
watch(() => props.flash, (newFlash) => {
    if (newFlash?.success) {
        toast.success(newFlash.success, {
            autoClose: 1000,
        });
    }
    if (newFlash?.error) {
        toast.error(newFlash.error, {
            autoClose: 1000,
        });
    }
}, {immediate: true});
</script>
