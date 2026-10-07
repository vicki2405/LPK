<template>
    <div class="flex items-center select-none text-left">
        <!-- Logo Image (Tightly-Cropped Transparent Phoenix) -->
        <div class="relative flex items-center justify-center shrink-0">
            <img 
                src="/images/logo.png" 
                alt="Logo LPK Masayume" 
                class="h-10 sm:h-12 md:h-13 w-auto object-contain transition-transform duration-300 group-hover:scale-105"
            />
        </div>

        <!-- Logotype Typography (Bolder & Clean) -->
        <div v-if="!iconOnly" class="flex flex-col justify-center text-left ml-3 shrink-0">
            <span class="font-black tracking-tight text-lg sm:text-xl md:text-2xl leading-none text-slate-950 font-sans">
                MASAYUME
            </span>
            <span class="text-[9px] sm:text-[10px] md:text-[11px] font-black tracking-[0.22em] text-slate-500 uppercase font-sans mt-1">
                TRAINING CENTER
            </span>
        </div>
    </div>
</template>

<script setup>
defineProps({
    iconOnly: {
        type: Boolean,
        default: false,
    },
});
</script>
