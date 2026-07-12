<script setup>
import { ref, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useForm, useField } from "vee-validate";
import * as yup from "yup";
import Logo from "@/icon/Group 47602.svg";
import Input from "@/components/Input.vue";
import InfoModal from "@/components/common/InfoModal.vue";
import { useAuthStore } from "@/stores/authStore";
import { useToast } from "vue-toastification";

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const toast = useToast();

const token = route.query.token || "";
const emailFromLink = route.query.email || "";
const showSuccessModal = ref(false);
const linkInvalid = ref(!token || !emailFromLink);

const schema = yup.object({
    password: yup.string().required("Password wajib diisi").min(8, "Password minimal 8 karakter"),
    confirm_password: yup.string()
        .required("Konfirmasi password wajib diisi")
        .oneOf([yup.ref("password")], "Konfirmasi password tidak cocok"),
});

const { handleSubmit } = useForm({ validationSchema: schema });
const { value: password, errorMessage: passwordError } = useField("password");
const { value: confirm_password, errorMessage: confirmPasswordError } = useField("confirm_password");

const submit = handleSubmit(async (values) => {
    try {
        await authStore.resetPassword({
            token,
            email: emailFromLink,
            password: values.password,
            password_confirmation: values.confirm_password,
        });
        showSuccessModal.value = true;
    } catch (error) {
        // Backend biasanya balas 422 kalau token invalid/expired (>60 menit)
        toast.error(error.response?.data?.message || "Link reset password tidak valid atau sudah kedaluwarsa. Silakan minta link baru.");
    }
});

function handleModalConfirm() {
    router.push("/login");
}
</script>

<template>
    <div class="m-14">
        <div class="flex items-center gap-4">
            <img :src="Logo" alt="">
            <h1 class="font-bold text-4xl">Cofund</h1>
        </div>

        <div class="mx-14 my-28">
            <div v-if="linkInvalid" class="max-w-md flex flex-col items-center text-center gap-3 py-10 mx-auto">
                <i class="pi pi-exclamation-triangle text-4xl text-red-500"></i>
                <h1 class="text-2xl font-bold">Link Tidak Valid</h1>
                <p class="text-gray-500">Link reset password tidak lengkap atau sudah kedaluwarsa (link hanya berlaku 60 menit). Silakan minta link baru.</p>
                <router-link to="/forgot-password" class="text-blue-700 font-semibold mt-2">Minta Link Baru</router-link>
            </div>

            <div v-else class="max-w-md">
                <h1 class="text-3xl font-bold">Reset Password</h1>
                <p class="py-4">Masukkan password baru untuk akun <span class="font-semibold">{{ emailFromLink }}</span></p>
                <form class="pt-4" @submit.prevent="submit" novalidate>
                    <Input v-model="password" label="Password Baru" name="password" placeholder="Masukkan Password Baru" type="password" :error="passwordError" />
                    <Input v-model="confirm_password" class="pt-4" label="Konfirmasi Password Baru" name="confirm_password" placeholder="Masukkan Konfirmasi Password Baru" type="password" :error="confirmPasswordError" />
                    <div class="pt-6">
                        <button class="w-full bg-blue-700 text-white py-2 rounded-sm font-semibold" type="submit">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>

        <InfoModal
            :visible="showSuccessModal"
            @update:visible="val => showSuccessModal = val"
            title="Password Berhasil Direset"
            message="Password Anda sudah berhasil diubah. Silakan login menggunakan password baru."
            button-text="Login Sekarang"
            @confirm="handleModalConfirm"
        />
    </div>
</template>