<script setup>
import { ref, computed } from 'vue';
import { ChevronDown, ChevronRight, FileText, Check, Plus, Trash2, X } from 'lucide-vue-next';
import { FIELD_TEMPLATES, FIELD_TYPES, FIELD_CATEGORIES } from '@/Admin/Helpers/financingFieldTypes';
import { Button } from '@/Shared/Components/ui/button';
import ConfirmDialog from '@/Shared/Components/ConfirmDialog.vue';

const props = defineProps({
  module: { type: String, default: 'financing_product' },
});

const emit = defineEmits(['select']);

const isOpen = ref(false);
const customTemplates = ref([]);
const hasFetched = ref(false);
const loading = ref(false);
const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

// --- Confirm for applying a template ---
const showConfirm = ref(null);
const openConfirm = (template) => { showConfirm.value = template; };
const confirmTemplate = () => {
  if (showConfirm.value) {
    emit('select', showConfirm.value.fields, showConfirm.value.name);
  }
  showConfirm.value = null;
};
const cancelTemplate = () => { showConfirm.value = null; };

// --- Delete custom template ---
const deleteTarget = ref(null);
const deleteTemplate = async () => {
  if (!deleteTarget.value) return;
  const r = await fetch(`/admin/field-templates/${deleteTarget.value}`, {
    method: 'DELETE',
    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
  });
  const data = await r.json();
  if (data.ok) {
    customTemplates.value = customTemplates.value.filter(t => t.id !== deleteTarget.value);
  }
  deleteTarget.value = null;
};

// --- Inline template builder ---
const showBuilder = ref(false);
const builderName = ref('');
const builderDescription = ref('');
const builderFields = ref([]);
const fieldType = ref('short_text');
const fieldLabel = ref('');
const fieldRequired = ref(false);
const builderSubmitting = ref(false);
const builderError = ref('');

const fieldTypeOptions = computed(() =>
  FIELD_CATEGORIES.map(cat => ({
    label: cat.label,
    options: FIELD_TYPES.filter(t => t.category === cat.key).map(t => ({ value: t.value, label: t.label })),
  }))
);

const addField = () => {
  if (!fieldLabel.value.trim()) return;
  builderFields.value.push({
    type: fieldType.value,
    label: fieldLabel.value.trim(),
    is_required: fieldRequired.value,
  });
  fieldLabel.value = '';
  fieldRequired.value = false;
};

const removeField = (idx) => {
  builderFields.value.splice(idx, 1);
};

const saveTemplate = async () => {
  if (!builderName.value.trim() || builderFields.value.length === 0) {
    builderError.value = 'Sila isi nama templat dan sekurang-kurangnya satu medan.';
    return;
  }
  builderSubmitting.value = true;
  builderError.value = '';
  try {
    const r = await fetch('/admin/field-templates', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
      body: JSON.stringify({
        module: props.module,
        name: builderName.value.trim(),
        description: builderDescription.value.trim(),
        fields: builderFields.value,
      }),
    });
    const data = await r.json();
    if (data.ok && data.template) {
      customTemplates.value.push(data.template);
      customTemplates.value.sort((a, b) => a.name.localeCompare(b.name));
      showBuilder.value = false;
      builderName.value = '';
      builderDescription.value = '';
      builderFields.value = [];
    } else {
      builderError.value = data.message || 'Ralat menyimpan templat.';
    }
  } catch {
    builderError.value = 'Ralat rangkaian. Sila cuba lagi.';
  }
  builderSubmitting.value = false;
};

const cancelBuilder = () => {
  showBuilder.value = false;
  builderName.value = '';
  builderDescription.value = '';
  builderFields.value = [];
  builderError.value = '';
};

// --- Fetch custom templates ---
const fetchTemplates = async () => {
  loading.value = true;
  try {
    const r = await fetch(`/admin/field-templates?module=${props.module}`, {
      headers: { 'Accept': 'application/json' },
    });
    const data = await r.json();
    if (data.ok) customTemplates.value = data.templates || [];
  } catch { /* ignore */ }
  loading.value = false;
};

const toggleOpen = () => {
  isOpen.value = !isOpen.value;
  if (isOpen.value && !hasFetched.value) {
    fetchTemplates();
    hasFetched.value = true;
  }
};

// --- Combined templates ---
const allTemplates = computed(() => [
  ...FIELD_TEMPLATES.map(t => ({ ...t, source: 'preset' })),
  ...customTemplates.value.map(t => ({ ...t, source: 'custom' })),
]);
</script>

<template>
  <div class="rounded-lg border border-slate-200 bg-white">
    <button
      type="button"
      class="flex w-full items-center gap-2 px-3 py-2.5 text-left text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors"
      @click="toggleOpen"
    >
      <ChevronDown v-if="isOpen" class="h-4 w-4 shrink-0 text-slate-400" />
      <ChevronRight v-else class="h-4 w-4 shrink-0 text-slate-400" />
      <FileText class="h-4 w-4 shrink-0 text-slate-500" />
      <span class="flex-1">Templat Borang Pantas</span>
      <span class="text-xs text-slate-400">{{ allTemplates.length }} templat</span>
    </button>

    <div v-if="isOpen" class="border-t border-slate-100 px-3 pb-3 pt-2">
      <p class="mb-2 text-xs text-slate-500">
        Gunakan template untuk tambah field automatik.
      </p>

      <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
        <div
          v-for="template in allTemplates"
          :key="template.id || template.source + '-' + template.name"
          class="group relative rounded-lg border border-slate-200 bg-slate-50 p-3 transition-all hover:border-teal-200 hover:bg-white hover:shadow-sm"
        >
          <!-- Delete button for custom templates -->
          <button
            v-if="template.source === 'custom'"
            type="button"
            class="absolute right-1.5 top-1.5 z-10 rounded p-0.5 text-slate-300 opacity-0 transition-all hover:bg-red-50 hover:text-red-500 group-hover:opacity-100"
            @click="deleteTarget = template.id"
          >
            <Trash2 class="h-3.5 w-3.5" />
          </button>

          <div class="space-y-1">
            <h4 class="text-sm font-semibold text-slate-900 group-hover:text-teal-800">
              {{ template.name }}
            </h4>
            <p class="text-xs text-slate-500 leading-relaxed">{{ template.description }}</p>
            <p class="text-[11px] text-slate-400">
              {{ template.fieldCount || (template.fields?.length || 0) }} medan
              <span v-if="template.fields?.filter(f => f.is_required).length > 0">
                · {{ template.fields.filter(f => f.is_required).length }} wajib
              </span>
            </p>
          </div>
          <div class="mt-2">
            <Button type="button" variant="outline" size="sm" class="w-full text-xs" @click="openConfirm(template)">
              <Check class="mr-1 h-3 w-3" />
              Guna Templat
            </Button>
          </div>
        </div>
      </div>

      <!-- Inline Builder -->
      <div class="mt-3">
        <button
          v-if="!showBuilder"
          type="button"
          class="flex w-full items-center justify-center gap-1.5 rounded-lg border border-dashed border-slate-300 bg-white px-3 py-2 text-xs font-medium text-slate-600 transition-all hover:border-teal-300 hover:bg-teal-50 hover:text-teal-700"
          @click="showBuilder = true"
        >
          <Plus class="h-3.5 w-3.5" />
          Buat Templat Sendiri
        </button>

        <div v-else class="rounded-lg border border-teal-200 bg-white p-4 shadow-sm">
          <div class="mb-3 flex items-center justify-between">
            <h4 class="text-sm font-semibold text-teal-800">Bina Templat Baru</h4>
            <button type="button" class="rounded p-1 text-slate-400 hover:text-slate-600" @click="cancelBuilder">
              <X class="h-4 w-4" />
            </button>
          </div>

          <!-- Template name & description -->
          <div class="grid gap-3 sm:grid-cols-2">
            <input v-model="builderName" type="text" placeholder="Nama templat (cth: Maklumat Bank)"
              class="h-9 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-1 focus:ring-teal-700/20" />
            <input v-model="builderDescription" type="text" placeholder="Penerangan ringkas (pilihan)"
              class="h-9 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-1 focus:ring-teal-700/20" />
          </div>

          <!-- Add field row -->
          <div class="mt-3 flex flex-wrap items-center gap-2">
            <select v-model="fieldType"
              class="h-9 rounded-lg border border-slate-300 bg-white px-2 text-sm focus:border-teal-700 focus:outline-none focus:ring-1 focus:ring-teal-700/20">
              <optgroup v-for="group in fieldTypeOptions" :key="group.label" :label="group.label">
                <option v-for="opt in group.options" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
              </optgroup>
            </select>
            <input v-model="fieldLabel" type="text" placeholder="Label medan..."
              class="h-9 flex-1 min-w-[160px] rounded-lg border border-slate-300 bg-white px-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-1 focus:ring-teal-700/20"
              @keyup.enter="addField" />
            <label class="flex items-center gap-1.5 text-xs text-slate-600 cursor-pointer select-none whitespace-nowrap">
              <input v-model="fieldRequired" type="checkbox" class="h-3.5 w-3.5 rounded border-slate-300 text-teal-600 focus:ring-teal-500" />
              Wajib
            </label>
            <Button type="button" size="sm" :disabled="!fieldLabel.trim()" @click="addField">
              Tambah
            </Button>
          </div>

          <!-- Builder field list -->
          <div v-if="builderFields.length > 0" class="mt-3 space-y-1.5">
            <p class="text-xs font-medium text-slate-500">{{ builderFields.length }} medan:</p>
            <div
              v-for="(f, idx) in builderFields"
              :key="idx"
              class="flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5"
            >
              <span class="rounded bg-white px-1.5 py-0.5 text-[11px] font-medium text-slate-600 ring-1 ring-slate-200 shrink-0">
                {{ FIELD_TYPES.find(t => t.value === f.type)?.label || f.type }}
              </span>
              <span class="flex-1 truncate text-xs text-slate-700">{{ f.label }}</span>
              <span v-if="f.is_required" class="rounded bg-red-50 px-1.5 py-0.5 text-[11px] font-medium text-red-600">Wajib</span>
              <button type="button" class="shrink-0 rounded p-0.5 text-slate-300 hover:bg-red-50 hover:text-red-500" @click="removeField(idx)">
                <X class="h-3.5 w-3.5" />
              </button>
            </div>
          </div>

          <!-- Builder actions -->
          <div class="mt-4 flex items-center justify-between">
            <p v-if="builderError" class="text-xs text-red-600">{{ builderError }}</p>
            <div class="ml-auto flex gap-2">
              <Button type="button" variant="outline" size="sm" @click="cancelBuilder">Batal</Button>
              <Button type="button" size="sm" :disabled="!builderName.trim() || builderFields.length === 0 || builderSubmitting" @click="saveTemplate">
                Simpan Templat
              </Button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Confirm apply template -->
    <ConfirmDialog
      :open="Boolean(showConfirm)"
      :title="`Guna Templat: ${showConfirm?.name || ''}`"
      :description="`Templat ini akan menambah ${showConfirm?.fieldCount || showConfirm?.fields?.length || 0} medan ke dalam seksyen ini. Teruskan?`"
      confirm-label="Guna Templat"
      @cancel="cancelTemplate"
      @confirm="confirmTemplate"
    />

    <!-- Confirm delete template -->
    <ConfirmDialog
      :open="Boolean(deleteTarget)"
      title="Padam Templat"
      description="Templat ini akan dipadam kekal. Tindakan ini tidak boleh dikembalikan."
      confirm-label="Padam"
      @cancel="deleteTarget = null"
      @confirm="deleteTemplate"
    />
  </div>
</template>
