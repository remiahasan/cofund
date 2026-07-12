<script setup>
import { onMounted, ref } from "vue";
import Logo from "@/icon/Group 47602.svg";
import imageLogin from "@/images/gambarauth1.png";
import Input from "@/components/Input.vue";
import InfoModal from "@/components/common/InfoModal.vue";
import { useForm, useField } from "vee-validate";
import * as yup from "yup";
import { useAuthStore } from "@/stores/authStore";
import { useRoute, useRouter } from "vue-router";
import { useToast } from "vue-toastification";
import { dashboardRouteByRole } from "@/router";

const authStore = useAuthStore();
const route = useRoute();
const router = useRouter();
const toast = useToast();

const showVerifiedModal = ref(false);

const schema = yup.object({
    email: yup.string().required("Email wajib diisi").email("Format email tidak valid"),
    password: yup.string().required("Password wajib diisi").min(8, "Password minimal 8 karakter"),
});

const { handleSubmit } = useForm({ validationSchema: schema });

const { value: email, errorMessage: emailError } = useField("email");
const { value: password, errorMessage: passwordError } = useField("password");

const login = handleSubmit(async (values) => {
    try {
        await authStore.login(values.email, values.password);
        toast.success("Login berhasil");
        const role = authStore.user?.role;
        router.push({ name: dashboardRouteByRole[role] || "dashboard.backer" });
    } catch (error) {
        toast.error(error.response?.data?.message || "Email atau password salah");
    }
});

onMounted(() => {
    // Backend mengarahkan ke /login?verified=1 setelah link verifikasi email diklik
    if (route.query.verified === '1' || route.query.verified === 'true') {
        showVerifiedModal.value = true;
    }
});
</script>

<template>
    <div class="p-14">
        <div class="flex items-center gap-4">
            <img :src="Logo" alt="">
            <h1 class="font-bold text-xl md:text-4xl">Cofund</h1>
        </div>
        <div class="xl:mx-14 mt-28 items-center justify-center md:columns-2">
            <div class="w-full">
                <h1 class="text-3xl font-bold">Login</h1>
                <p class="py-4 ">Silahkan Masuk Menggunakan Akun Anda</p>
                <form class="pt-7" @submit.prevent="login" novalidate>
                    <Input v-model="email" label="Email" name="email" placeholder="Masukkan Email Terdaftar Anda" type="email" :error="emailError" />
                    <Input v-model="password" class="pt-4" label="Password" name="password" placeholder="Masukkan Password Anda" type="password" :error="passwordError" />
                    <div class="text-right pt-2">
                        <router-link to="/forgot-password" class="text-sm text-blue-700 font-medium">Lupa Password?</router-link>
                    </div>
                    <div class="pt-6">
                        <button class="w-full bg-blue-700 text-white py-2 rounded-sm font-semibold" type="submit">Login</button>
                    </div>
                </form>
                <div class="text-center pt-4">
                    <p>Belum Punya akun? <router-link class="text-blue-700 font-semibold" to="/register">Daftar</router-link></p>
                </div>
            </div>
            <div class="justify-items-end hidden md:block">
                <img class="rounded-2xl shadow-2xl" :src="imageLogin" alt="">
            </div>
        </div>

        <InfoModal
            :visible="showVerifiedModal"
            @update:visible="val => showVerifiedModal = val"
            title="Email Telah Diverifikasi"
            message="Akun Anda sudah terverifikasi. Silakan login untuk melanjutkan."
            button-text="Mengerti"
        />
    </div>
</template>