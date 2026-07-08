<script setup>
import Logo from "@/icon/Group 47602.svg";
import imageLogin from "@/images/gambarauth1.png";
import Input from "@/components/form.vue";
import { ref } from "vue";
import { useAuthStore } from "@/stores/authStore";
import { useRouter } from "vue-router";

const email = ref("");
const password = ref("");
const authStore = useAuthStore();
const router = useRouter();

async function login() {
    try {
        await authStore.login(email.value, password.value);
        console.log("Login berhasil");
        router.push("/dashboard");
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
                <h1 class="text-3xl font-bold">Login</h1>
                <p class="py-4 ">Silahkan Masuk Menggunakan Akun Anda</p>
                <form class="pt-7" @submit.prevent="login">
                        <Input v-model="email" label="Email" name="email" placeholder="Masukkan Email Terdaftar Anda" type="email"/>
                    <Input v-model="password" class="pt-4" label="Password" name="password" placeholder="Masukkan Password Anda" type="password"/>
                    <div class="pt-6">
                        <button class="w-full bg-blue-700 text-white py-2 rounded-sm font-semibold"
                            type="submit">Login</button>
                    </div>
                </form>
                <div class="text-center pt-4">
                    <p>Belum Punya akun? <a class="text-blue-700 font-semibold" href="">Daftar</a></p>
                </div>
            </div>
            <div class="justify-items-end">
                <img class="rounded-2xl shadow-2xl" :src="imageLogin" alt="">
            </div>
        </div>
    </div>
</template>