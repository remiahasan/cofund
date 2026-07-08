<script setup>
import noImage from '@/images/no-image.jpg';

const props = defineProps({
    data: {
        type: Object,
        default: () => ({
            name: 'Nama Produk',
            price: 0,
            stock: 0
        })
    }
});
</script>

<template>
    <div
        class="bg-white cursor-pointer rounded-2xl p-5 flex flex-col gap-2 shadow-[0_4px_8px_rgba(0,0,0,0.09)] transition-transform duration-200 ease-in-out hover:-translate-y-1.5 hover:shadow-[0_12px_24px_rgba(0,0,0,0.13)]">

        <!-- Gambar -->
        <div class="campaign-image relative">
            <img :src="noImage" alt="Cofund" />
            <div v-if="data.stock <= 0"
                class="absolute top-2 right-2 bg-gray-500 text-white text-[10px] px-2 py-1 rounded-md font-bold uppercase tracking-wider">
                Habis
            </div>
        </div>

        <!-- Info -->
        <div class="flex-1">
            <p class="campaign-name text-gray-700 font-medium">{{ data.name }}</p>
            <p class="campaign-des ">deesscribsi</p>
            <p class="campaign-price text-blue-700">Rp. {{ data.price.toLocaleString('id-ID') }}</p>
        </div>

        <!-- Tombol -->
        <button
            :disabled="data.stock <= 0"
            class="btn-beli bg-blue-600 hover:bg-blue-700 text-white font-semibold border-none rounded-xl py-2 px-0 w-full transition-colors duration-200 flex-shrink-0 disabled:bg-gray-300 disabled:text-gray-500 disabled:cursor-not-allowed">
            {{ data.stock <= 0 ? 'Habis' : 'Beli Sekarang' }}
        </button>

    </div>
</template>

<style scoped>
.campaign-name {
    font-size: 11px;
    margin: 0 0 4px 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.campaign-des{
    font-size: 11px;
    margin: 0 0 4px 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.campaign-image img {
    width: 100%;
    aspect-ratio: 1 / 1;
    object-fit: contain;
    border-radius: 8px;
}

.campaign-price {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
}

.btn-beli {
    font-size: 14px;
}

@media (min-width: 768px) {
    .campaign-name {
        font-size: 13px;
    }

    .campaign-price {
        font-size: 20px;
    }

    .btn-beli {
        font-size: 15px;
        border-radius: 16px;
    }
}

@media (min-width: 1024px) {
    .campaign-name {
        font-size: 14px;
    }

    .campaign-price {
        font-size: 22px;
    }

    .btn-beli {
        font-size: 20px;
        border-radius: 18px;
    }
}
</style>