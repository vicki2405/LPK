<script setup>
import { ref, computed } from 'vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { Camera, Trash2, Upload, CheckCircle2, FileText, Sparkles, UserCheck } from 'lucide-vue-next';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const photoInput = ref(null);
const photoPreview = ref(null);
const hasImgError = ref(false);

const form = useForm({
    _method: 'patch',
    name: user.value.name,
    email: user.value.email,
    avatar: null,
    remove_avatar: 0,
});

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];
    if (!photo) return;
    form.avatar = photo;
    form.remove_avatar = 0;
    hasImgError.value = false;
    const reader = new FileReader();
    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };
    reader.readAsDataURL(photo);
};

const deletePhoto = () => {
    form.avatar = null;
    form.remove_avatar = 1;
    photoPreview.value = null;
    hasImgError.value = false;
    if (photoInput.value) {
        photoInput.value.value = null;
    }
};

const submitProfile = () => {
    form.post(route('profile.update'), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            photoPreview.value = null;
            form.avatar = null;
            form.remove_avatar = 0;
            hasImgError.value = false;
            if (photoInput.value) {
                photoInput.value.value = null;
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-japan-red/10 text-japan-red">
                    CV & Biodata
                </span>
                <h2 class="text-lg font-bold text-slate-900">
                    Informasi Akun & Pasfoto Formal CV
                </h2>
            </div>

            <p class="mt-1 text-xs sm:text-sm text-slate-500">
                Lengkapi pasfoto formal standar CV Jepang (Rirekisho), nama lengkap, dan alamat email Anda.
            </p>
        </header>

        <form @submit.prevent="submitProfile" class="mt-6 space-y-6">
            <!-- PASFOTO FORMAL MODEL CV (RASIO 3:4) -->
            <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-br from-slate-50 to-slate-100/70 border border-slate-200/90 shadow-sm">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 pb-3 mb-4 border-b border-slate-200/80">
                    <div>
                        <h3 class="text-xs sm:text-sm font-bold text-slate-800 flex items-center gap-1.5">
                            <UserCheck class="w-4 h-4 text-japan-red" />
                            <span>Pasfoto Formal Siswa (Standar CV / Rirekisho Jepang)</span>
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            Foto ini dicantumkan pada lembar CV lamaran kerja dan tampilan Dashboard Siswa.
                        </p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-white border border-slate-200 text-[10px] font-bold text-slate-600 tracking-wider uppercase">
                        Rasio 3 : 4 Portrait
                    </span>
                </div>
                
                <input
                    ref="photoInput"
                    type="file"
                    class="hidden"
                    accept="image/png, image/jpeg, image/jpg, image/webp"
                    @change="updatePhotoPreview"
                />

                <div class="flex flex-col md:flex-row items-center md:items-start gap-6 sm:gap-8">
                    <!-- PASFOTO FRAME 3:4 BESAR & PROPORSIONAL -->
                    <div class="relative group shrink-0">
                        <div class="w-44 h-58 sm:w-48 sm:h-64 rounded-2xl overflow-hidden border-2 border-slate-300 shadow-lg bg-white flex flex-col items-center justify-center relative transition-all group-hover:border-japan-red/70 group-hover:shadow-xl">
                            <!-- New Preview Image -->
                            <img
                                v-if="photoPreview"
                                :src="photoPreview"
                                alt="Pratinjau Pasfoto CV"
                                class="w-full h-full object-cover object-top"
                            />
                            <!-- Current Saved Photo -->
                            <img
                                v-else-if="user.avatar_url && !form.remove_avatar && !hasImgError"
                                :src="user.avatar_url"
                                alt="Pasfoto Formal CV Saat Ini"
                                class="w-full h-full object-cover object-top"
                                @error="hasImgError = true"
                            />
                            <!-- Initial / Formal Placeholder -->
                            <div
                                v-else
                                class="w-full h-full bg-slate-50 flex flex-col items-center justify-center p-4 text-center text-slate-400"
                            >
                                <div class="w-16 h-16 rounded-full bg-slate-200/80 flex items-center justify-center mb-2">
                                    <Camera class="w-8 h-8 text-slate-400" />
                                </div>
                                <span class="text-sm font-bold text-slate-700">Belum Ada Foto</span>
                                <span class="text-xs text-slate-400 mt-0.5">Pasfoto Formal 3:4</span>
                                <span class="text-[10px] text-japan-red font-semibold mt-1">Klik tombol di bawah</span>
                            </div>

                            <!-- Bottom Tag in Frame -->
                            <div class="absolute bottom-0 inset-x-0 bg-slate-950/80 backdrop-blur-xs text-white text-[11px] font-bold py-1.5 px-2 text-center tracking-wider border-t border-white/10">
                                {{ user.name.split(' ')[0] }} • Pasfoto CV
                            </div>
                        </div>

                        <!-- Camera Quick Action Button -->
                        <button
                            type="button"
                            @click="selectNewPhoto"
                            class="absolute -top-2 -right-2 p-2.5 rounded-full bg-japan-red text-white shadow-xl hover:bg-rose-700 hover:scale-105 active:scale-95 transition-all cursor-pointer ring-4 ring-white"
                            title="Unggah Pasfoto Baru"
                        >
                            <Camera class="w-4 h-4" />
                        </button>
                    </div>

                    <!-- Instructions & Action Buttons -->
                    <div class="flex-1 space-y-3.5 text-center md:text-left">
                        <div class="space-y-1.5 bg-white/80 p-3.5 rounded-xl border border-slate-200/80 text-xs text-slate-600">
                            <p class="font-bold text-slate-800 flex items-center justify-center md:justify-start gap-1.5 text-xs">
                                <Sparkles class="w-3.5 h-3.5 text-amber-500" />
                                <span>Ketentuan Pasfoto Standar Jepang:</span>
                            </p>
                            <ul class="text-[11px] text-slate-500 space-y-1 list-disc list-inside text-left leading-relaxed">
                                <li>Mengenakan pakaian formal rapi (kemeja putih / berjas & berdasi).</li>
                                <li>Posisi wajah lurus menghadap kamera dengan ekspresi formal ramah.</li>
                                <li>Latar belakang polos (putih, biru, atau abu-abu terang).</li>
                                <li>Format yang didukung: <strong>JPG, JPEG, PNG, atau WebP</strong> (Maksimal 4MB).</li>
                            </ul>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-center md:justify-start gap-2.5 flex-wrap pt-1">
                            <button
                                type="button"
                                @click="selectNewPhoto"
                                class="px-4 py-2.5 rounded-xl bg-japan-red hover:bg-rose-700 text-white font-bold text-xs flex items-center gap-2 shadow-sm transition-all cursor-pointer"
                            >
                                <Upload class="w-3.5 h-3.5" />
                                <span>{{ photoPreview || (user.avatar_url && !form.remove_avatar) ? 'Ganti Pasfoto CV' : 'Unggah Pasfoto CV' }}</span>
                            </button>

                            <button
                                v-if="photoPreview || (user.avatar_url && !form.remove_avatar)"
                                type="button"
                                @click="deletePhoto"
                                class="px-3.5 py-2.5 rounded-xl bg-white hover:bg-rose-50 text-rose-700 font-bold text-xs flex items-center gap-1.5 transition-colors cursor-pointer border border-rose-200"
                            >
                                <Trash2 class="w-3.5 h-3.5 text-rose-500" />
                                <span>Hapus Foto</span>
                            </button>
                        </div>
                    </div>
                </div>

                <InputError class="mt-2" :message="form.errors.avatar" />
                <InputError class="mt-2" :message="form.errors.remove_avatar" />
            </div>

            <!-- NAME -->
            <div>
                <InputLabel for="name" value="Nama Lengkap Siswa *" class="text-xs font-bold text-slate-700 mb-1" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-japan-red focus:outline-none"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <!-- EMAIL -->
            <div>
                <InputLabel for="email" value="Alamat Email Akun *" class="text-xs font-bold text-slate-700 mb-1" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-japan-red focus:outline-none"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-xs sm:text-sm text-slate-800">
                    Alamat email Anda belum diverifikasi.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-xs sm:text-sm text-japan-red underline hover:text-rose-700 focus:outline-none font-bold"
                    >
                        Klik di sini untuk mengirim ulang email verifikasi.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-xs font-bold text-emerald-600"
                >
                    Tautan verifikasi baru telah dikirimkan ke alamat email Anda.
                </div>
            </div>

            <div class="flex items-center gap-4 pt-2">
                <PrimaryButton 
                    :disabled="form.processing"
                    class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs sm:text-sm shadow-md transition-all cursor-pointer flex items-center gap-2"
                >
                    <span v-if="form.processing" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                    <span>Simpan Perubahan CV & Profil</span>
                </PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-xs font-bold text-emerald-600 flex items-center gap-1.5"
                    >
                        <CheckCircle2 class="w-4 h-4 text-emerald-500" />
                        <span>Profil & Pasfoto berhasil diperbarui.</span>
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>

