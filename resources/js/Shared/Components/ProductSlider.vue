<script setup>
import { ChevronLeft, ChevronRight, ShoppingBag } from 'lucide-vue-next';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    products: { type: Array, required: true },
});

const track = ref(null);
const activeIndex = ref(0);
let observer = null;

const total = computed(() => props.products.length);

// Scroll within the track only — never moves the page viewport
function scrollToIndex(index) {
    if (!track.value) return;
    const el = track.value.children[index];
    if (el) {
        track.value.scrollTo({ left: el.offsetLeft, behavior: 'smooth' });
    }
}

function prev() {
    scrollToIndex(Math.max(0, activeIndex.value - 1));
}

function next() {
    scrollToIndex(Math.min(total.value - 1, activeIndex.value + 1));
}

onMounted(() => {
    if (track.value && total.value > 1) {
        observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        const idx = Array.from(track.value.children).indexOf(entry.target);
                        if (idx !== -1) activeIndex.value = idx;
                    }
                });
            },
            { root: track.value, threshold: 0.5 },
        );
        Array.from(track.value.children).forEach((child) => observer.observe(child));
    }
});

onUnmounted(() => {
    if (observer) observer.disconnect();
});
</script>

<template>
    <div v-if="products.length" class="relative">
        <!-- Scroll-snap track -->
        <div
            ref="track"
            class="flex gap-3 overflow-x-auto scroll-smooth snap-x snap-mandatory scrollbar-none"
            style="-webkit-overflow-scrolling: touch;"
        >
            <Link
                v-for="product in products"
                :key="product.id"
                :href="product.url"
                class="group flex-none w-[75%] sm:w-[calc(33.333%-8px)] snap-start overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:border-teal-200 hover:shadow-md"
            >
                <div class="aspect-square overflow-hidden bg-slate-100">
                    <img
                        v-if="product.primary_image_url"
                        :src="product.primary_image_url"
                        :alt="product.name"
                        class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                    />
                    <div v-else class="flex h-full w-full items-center justify-center text-slate-400">
                        <ShoppingBag class="h-10 w-10" />
                    </div>
                </div>
                <div class="p-3">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">{{ product.category_name || 'Produk' }}</p>
                    <p class="mt-1 text-sm font-semibold text-slate-950 line-clamp-2">{{ product.name }}</p>
                    <p class="mt-1 text-xs text-slate-600">
                        <template v-if="product.min_price === product.max_price">
                            RM {{ Number(product.min_price).toFixed(2) }}
                        </template>
                        <template v-else>
                            RM {{ Number(product.min_price).toFixed(2) }} – RM {{ Number(product.max_price).toFixed(2) }}
                        </template>
                    </p>
                </div>
            </Link>
        </div>

        <!-- Nav buttons: hidden on mobile -->
        <button
            v-if="activeIndex > 0"
            class="absolute -left-3 top-1/2 z-10 hidden sm:flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full border border-slate-200 bg-white shadow-sm transition hover:bg-slate-50"
            @click="prev"
        >
            <ChevronLeft class="h-4 w-4 text-slate-600" />
        </button>

        <button
            v-if="activeIndex < total - 1"
            class="absolute -right-3 top-1/2 z-10 hidden sm:flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full border border-slate-200 bg-white shadow-sm transition hover:bg-slate-50"
            @click="next"
        >
            <ChevronRight class="h-4 w-4 text-slate-600" />
        </button>

        <!-- Pagination dots -->
        <div v-if="total > 1" class="mt-3 flex justify-center gap-1.5">
            <span
                v-for="(_, i) in products"
                :key="i"
                class="h-1.5 rounded-full transition-all"
                :class="i === activeIndex ? 'w-4 bg-teal-600' : 'w-1.5 bg-slate-300'"
            />
        </div>
    </div>

    <div v-else class="flex flex-col items-center gap-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 py-10 text-center">
        <ShoppingBag class="h-10 w-10 text-slate-400" />
        <div>
            <p class="text-sm font-medium text-slate-700">Tiada produk tersedia</p>
            <p class="mt-1 text-xs text-slate-500">Produk ansuran mudah akan dipaparkan di sini apabila tersedia.</p>
        </div>
    </div>
</template>