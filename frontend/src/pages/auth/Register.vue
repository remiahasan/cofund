<script setup>
import Logo from "@/icon/Group 47602.svg";
import imageRegister from "@/images/gambarauth2.png"
import Input from "@/components/Input.vue";
import { useForm, useField } from "vee-validate";
import * as yup from "yup";
import { useAuthStore } from "@/stores/authStore";
import { useRouter } from "vue-router";
import { useToast } from "vue-toastification";

const authStore = useAuthStore();
const router = useRouter();
const toast = useToast();

const schema = yup.object({
    nama: yup.string().required("Nama lengkap wajib diisi"),
    email: yup.string().required("Email wajib diisi").email("Format email tidak valid"),
    password: yup.string().required("Password wajib diisi").min(8, "Password minimal 8 karakter"),
    confirm_password: yup.string()
        .required("Konfirmasi password wajib diisi")
        .oneOf([yup.ref("password")], "Konfirmasi password tidak cocok"),
});

const { handleSubmit } = useForm({ validationSchema: schema });

const { value: nama, errorMessage: namaError } = useField("nama");
const { value: email, errorMessage: emailError } = useField("email");
const { value: password, errorMessage: passwordError } = useField("password");
const { value: confirm_password, errorMessage: confirmPasswordError } = useField("confirm_password");

const register = handleSubmit(async (values) => {
    try {
        await authStore.register(values.nama, values.email, values.password, values.confirm_password);
        toast.success("Registrasi berhasil, silakan login");
        router.push("/login");
    } catch (error) {
        toast.error(error.response?.data?.message || "Registrasi gagal, coba lagi");
    }
});

function goToLogin() {
    router.push("/login");
}
</script>

<template>
    <div class="m-14">
        <div class="flex items-center gap-4">
            <img :src="Logo" alt="">
            <h1 class="font-bold text-4xl">Cofund</h1>
        </div>
        <div class="mx-14 my-28 columns-2">
            <div class="w-full">
                <h1 class="text-3xl font-bold">Register</h1>
                <p class="py-4 ">Daftarkan akun Anda untuk memulai</p>
                <form class="pt-7" @submit.prevent="register" novalidate>
                    <div class="columns-2">
                        <Input v-model="nama" label="Nama Lengkap" name="nama" placeholder="Masukkan Nama Lengkap Anda" :error="namaError" />
                        <Input v-model="email" label="Email" name="email" placeholder="Masukkan Email Anda" type="email" :error="emailError" />
                    </div>
                    <Input v-model="password" class="pt-4" label="Password" name="password" placeholder="Masukkan Password Anda" type="password" :error="passwordError" />
                    <Input v-model="confirm_password" class="pt-4" label="Konfirmasi Password" name="confirm_password" placeholder="Masukkan Konfirmasi Password Anda" type="password" :error="confirmPasswordError" />
                    <div class="pt-6">
                        <button class="w-full bg-blue-700 text-white py-2 rounded-sm font-semibold" type="submit">Daftar</button>
                    </div>
                </form>
                <div class="text-center pt-4">
                    <p>Sudah punya akun? <a class="text-blue-700 font-semibold cursor-pointer" @click="goToLogin">Masuk</a></p>
                </div>
            </div>
            <div class="justify-items-end">
                <img class="rounded-2xl shadow-2xl" :src="imageRegister" alt="">
            </div>
        </div>
    </div>
</template>