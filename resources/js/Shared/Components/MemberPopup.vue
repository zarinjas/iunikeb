<script setup>
import { router } from '@inertiajs/vue3';
import { X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = defineProps({
    popup: { type: Object, required: true },
});

const open = ref(true);
const hasContent = computed(() => props.popup.title || props.popup.content);
const imageLink = computed(() => {
    if (props.popup.button_url && !props.popup.button_text) return props.popup.button_url;
    return null;
});

function dismiss() {
    open.value = false;
    router.post('/member/popup/dismiss', {}, {
        preserveState: true,
        preserveScroll: true,
        onError: () => {},
    });
}
</script>

<template>
    <Teleport to="body">
        <div v-if="open" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-950/50 backdrop-blur-sm" @click="dismiss"></div>

            <div
                class="relative z-10 w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl"
                :class="!hasContent && popup.image_url ? '!w-auto !max-w-3xl' : ''"
            >
                <button
                    type="button"
                    class="absolute right-3 top-3 z-20 rounded-lg bg-white/80 p-1.5 text-slate-600 shadow-sm backdrop-blur transition-colors hover:bg-white hover:text-slate-900"
                    @click="dismiss"
                >
                    <X class="h-4 w-4" />
                </button>

                <a
                    v-if="popup.image_url && imageLink"
                    :href="imageLink"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <img :src="popup.image_url" :alt="popup.title" class="w-full object-cover" />
                </a>
                <img
                    v-else-if="popup.image_url"
                    :src="popup.image_url"
                    :alt="popup.title"
                    class="w-full object-cover"
                />

                <div v-if="hasContent" class="space-y-3 px-6 pb-6 pt-4">
                    <h2 v-if="popup.title" class="text-lg font-bold text-slate-950">{{ popup.title }}</h2>
                    <p v-if="popup.content" class="text-sm leading-relaxed text-slate-600 whitespace-pre-line">{{ popup.content }}</p>

                    <a
                        v-if="popup.button_text && popup.button_url"
                        :href="popup.button_url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="mt-2 inline-flex items-center rounded-lg bg-teal-700 px-5 py-2.5 text-sm font-medium text-white transition-colors hover:bg-teal-800"
                    >
                        {{ popup.button_text }}
                    </a>
                </div>
            </div>
        </div>
    </Teleport>
</template>
