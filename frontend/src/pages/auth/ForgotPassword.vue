<script setup>
import { ref } from "vue";
import { useForm, useField } from "vee-validate";
import * as yup from "yup";
import Logo from "@/icon/Group 47602.svg";
import imageForgot from "@/images/gambarauth1.png";
import Input from "@/components/Input.vue";
import { useAuthStore } from "@/stores/authStore";
import { useToast } from "vue-toastification";

const authStore = useAuthStore();
const toast = useToast();
const isSent = ref(false);
const sentToEmail = ref("");

const schema = yup.object({
    email: yup.string().required("Email wajib diisi").email("Format email tidak valid"),
});

const { handleSubmit } = useForm({ validationSchema: schema });
const { value: email, errorMessage: emailError } = useField("email");

const submit = handleSubmit(async (values) => {
    try {
        await authStore.forgotPassword(values.email);
        sentToEmail.value = values.email;
        isSent.value = true;
    } catch (error) {
        toast.error(error.response?.data?.message || "Gagal mengirim link reset password");
    }
});
</script>

<template>
    <div class="m-14">
        <div class="flex items-center gap-4">
            <img :src="Logo" alt="">
            <h1 class="font-bold text-4xl">Cofund</h1>
        </div>
        <div class="mx-auto mt-28 items-center justify-center md:gap-2 md:columns-2">
            <div class="w-full">
                <template v-if="!isSent">
                    <h1 class="text-3xl font-bold">Lupa Password</h1>
                    <p class="py-4">Masukkan email Anda, kami akan mengirimkan link untuk reset password.</p>
                    <form class="pt-4" @submit.prevent="submit" novalidate>
                        <Input v-model="email" label="Email" name="email" placeholder="Masukkan Email Terdaftar Anda" type="email" :error="emailError" />
                        <div class="pt-6">
                            <button class="w-full bg-blue-700 text-white py-2 rounded-sm font-semibold" type="submit">Kirim Link Reset</button>
                        </div>
                    </form>
                    <div class="text-center pt-4">
                        <router-link class="text-blue-700 font-semibold" to="/login">Kembali ke Login</router-link>
                    </div>
                </template>

                <template v-else>
                    <div class="flex flex-col items-center text-center gap-3 py-10">
                        <i class="pi pi-envelope text-4xl text-blue-600"></i>
                        <h1 class="text-2xl font-bold">Cek Email Anda</h1>
                        <p class="text-gray-500">
                            Link reset password telah dikirim ke <span class="font-semibold">{{ sentToEmail }}</span>.
                            Link ini berlaku selama <span class="font-semibold">60 menit</span>, setelah itu Anda perlu meminta link baru.
                        </p>
                        <router-link to="/login" class="text-blue-700 font-semibold mt-2">Kembali ke Login</router-link>
                    </div>
                </template>
            </div>
            <div class="justify-items-end">
                <img class="rounded-2xl shadow-2xl" :src="imageForgot" alt="">
            </div>
        </div>
    </div>
</template>