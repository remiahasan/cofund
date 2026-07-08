<script setup>
import Logo from "@/icon/Group 47602.svg";
import imageRegister from "@/images/gambarauth2.png"
import Input from "@/components/form.vue";

import { ref } from "vue";
import { useAuthStore } from "@/stores/authStore";
import { useRouter } from "vue-router";

const authStore = useAuthStore();
const router = useRouter();


const nama = ref("");
const email = ref("");
const password = ref("");
const confirm_password = ref("");

async function register() {
    try {
        await authStore.register(nama.value, email.value, password.value, confirm_password.value);
        console.log("Registrasi berhasil");
        router.push("/login");
    } catch (error) {
        console.log(error);
    }
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
                <form class="pt-7" @submit.prevent="register">
                    <div class="columns-2">
                        <Input v-model="nama" label="Nama Lengkap" name="nama" placeholder="Masukkan Nama Lengkap Anda" />
                        <Input v-model="email" label="Email" name="email" placeholder="Masukkan Email Anda" type="email" />
                    </div>
                    <Input v-model="password" class="pt-4" label="Password" name="password" placeholder="Masukkan Password Anda"
                        type="password" />
                    <Input v-model="confirm_password" class="pt-4" label="Konfirmasi Password" name="confirm_password"
                        placeholder="Masukkan Konfirmasi Password Anda" type="password" />
                    <div class="pt-6">
                        <button class="w-full bg-blue-700 text-white py-2 rounded-sm font-semibold"
                            type="submit">Daftar</button>
                    </div>
                </form>
                <div class="text-center pt-4">
                    <p>Sudah punya akun? <a class="text-blue-700 font-semibold" href="">Masuk</a></p>
                </div>
            </div>
            <div class="justify-items-end">
                <img class="rounded-2xl shadow-2xl" :src="imageRegister" alt="">
            </div>
        </div>
    </div>
</template>
