<script setup>
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import PosterLightbox from '@/Shared/Components/PosterLightbox.vue';

const props = defineProps({
    posters: { type: Array, required: true },
});

const track = ref(null);
const activeIndex = ref(0);
const lightboxPoster = ref(null);
const isPaused = ref(false);
let autoPlayTimer = null;
let observer = null;

const total = computed(() => props.posters.length);

// Scroll within the track only — never moves the page viewport
function scrollToIndex(index) {
    if (!track.value) return;
    const el = track.value.children[index];
    if (el) {
        track.value.scrollTo({ left: el.offsetLeft, behavior: 'smooth' });
    }
}

function prev() {
    const next = Math.max(0, activeIndex.value - 1);
    scrollToIndex(next);
    resetAutoPlay();
}

function next() {
    const next = activeIndex.value < total.value - 1 ? activeIndex.value + 1 : 0;
    scrollToIndex(next);
    resetAutoPlay();
}

function openLightbox(poster) {
    lightboxPoster.value = poster;
}

function startAutoPlay() {
    if (total.value <= 1) return;
    stopAutoPlay();
    autoPlayTimer = setInterval(() => {
        if (!isPaused.value) {
            const nextIdx = activeIndex.value < total.value - 1 ? activeIndex.value + 1 : 0;
            scrollToIndex(nextIdx);
        }
    }, 4000);
}

function stopAutoPlay() {
    if (autoPlayTimer) {
        clearInterval(autoPlayTimer);
        autoPlayTimer = null;
    }
}

function resetAutoPlay() {
    stopAutoPlay();
    startAutoPlay();
}

onMounted(() => {
    startAutoPlay();

    // IntersectionObserver to track active slide for pagination dots
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
    stopAutoPlay();
    if (observer) observer.disconnect();
});
</script>

<template>
    <div
        v-if="posters.length"
        class="relative"
        @mouseenter="isPaused = true"
        @mouseleave="isPaused = false"
    >
        <!-- Scroll-snap track: native momentum + snap, no JS swipe needed -->
        <div
            ref="track"
            class="flex gap-3 overflow-x-auto scroll-smooth snap-x snap-mandatory scrollbar-none"
            style="-webkit-overflow-scrolling: touch;"
        >
            <button
                v-for="poster in posters"
                :key="poster.id"
                class="group flex-none w-full sm:w-[calc(25%-9px)] snap-start overflow-hidden rounded-xl bg-slate-50 shadow-sm ring-1 ring-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500"
                @click="openLightbox(poster)"
            >
                <!-- 3:4 mobile, 4:3 sm+ -->
                <div class="aspect-[3/4] sm:aspect-[4/3] overflow-hidden">
                    <img
                        :src="poster.image_url"
                        :alt="poster.alt_text || poster.title"
                        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                    />
                </div>
            </button>
        </div>

        <!-- Nav buttons: hidden on mobile, visible on sm+ -->
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
                v-for="(_, i) in posters"
                :key="i"
                class="h-1.5 rounded-full transition-all"
                :class="i === activeIndex ? 'w-4 bg-teal-600' : 'w-1.5 bg-slate-300'"
            />
        </div>

        <PosterLightbox
            v-if="lightboxPoster"
            :poster="lightboxPoster"
            @close="lightboxPoster = null"
        />
    </div>
</template>