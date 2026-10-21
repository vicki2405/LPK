import { ref, computed } from 'vue';

// Persistent language state across components
const currentLang = ref(localStorage.getItem('masayume_lang') || 'id');

export function useLang() {
    const setLang = (lang) => {
        currentLang.value = lang;
        localStorage.setItem('masayume_lang', lang);
    };

    const isJapanese = computed(() => currentLang.value === 'ja');
    const isIndonesian = computed(() => currentLang.value === 'id');

    return {
        currentLang,
        setLang,
        isJapanese,
        isIndonesian,
    };
}
