import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useTooltipStore = defineStore('tooltip', () => {
    const type = ref('');
    const data = ref({});

    return { type, data };
});
