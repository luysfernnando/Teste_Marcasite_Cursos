<script setup>
import { ref, watch, defineEmits } from 'vue';
import TextInput from "@/Components/TextInput.vue";

const props = defineProps({
    modelValue: String,
    placeholder: {
        type: String,
        default: '000.000.000-00'
    }
});

const emit = defineEmits(['update:modelValue']);
const cpf = ref(props.modelValue || '');

// Função para mascarar o CPF
const formatCpf = (value) => {
    return value
        .replace(/\D/g, '') // Remove qualquer caractere que não seja dígito
        .replace(/(\d{3})(\d)/, '$1.$2') // Adiciona o primeiro ponto
        .replace(/(\d{3})(\d)/, '$1.$2') // Adiciona o segundo ponto
        .replace(/(\d{3})(\d{1,2})$/, '$1-$2') // Adiciona o hífen
        .slice(0, 14); // Limita a 14 caracteres no total (incluindo máscara)
};

// Função chamada ao digitar
const onInput = (value) => {
    const masked = formatCpf(value);
    cpf.value = masked;
    emit('update:modelValue', masked); // Emite o valor mascarado para o pai
};

// Atualiza o valor de `cpf` quando o prop `modelValue` mudar externamente
watch(() => props.modelValue, (newVal) => {
    cpf.value = formatCpf(newVal || '');
});
</script>

<template>
    <TextInput
        v-model="cpf"
        type="text"
        :placeholder="placeholder"
        class="form-input mt-1 block w-full"
        maxlength="14"
        @input="onInput($event.target.value)"
    />
</template>
