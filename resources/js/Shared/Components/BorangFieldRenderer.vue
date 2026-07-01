<script setup>
import { computed } from 'vue';
import { getFieldTypeConfig } from '@/Admin/Helpers/formFieldTypes';
import FileUploader from '@/Shared/Components/FileUploader.vue';
import SignaturePad from '@/Shared/Components/SignaturePad.vue';
import TextInput from '@/Shared/Components/Form/TextInput.vue';
import TextareaInput from '@/Shared/Components/Form/TextareaInput.vue';

const props = defineProps({
    field: { type: Object, required: true },
    modelValue: { type: [String, Number, Array, Object, Boolean], default: null },
    fileValue: { type: [Object, File], default: null },
    error: { type: String, default: null },
    autofilled: { type: Boolean, default: false },
});

const emit = defineEmits(['update:modelValue', 'update:fileValue']);

const config = computed(() => getFieldTypeConfig(props.field.type));
const isMemberAutofill = computed(() => config.value?.isMemberAutofill);

const MY_STATES = [
    'Johor', 'Kedah', 'Kelantan', 'Melaka', 'Negeri Sembilan',
    'Pahang', 'Perak', 'Perlis', 'Pulau Pinang', 'Sabah',
    'Sarawak', 'Selangor', 'Terengganu',
    'W.P. Kuala Lumpur', 'W.P. Labuan', 'W.P. Putrajaya',
];

const inputTypeMap = {
    short_text: 'text',
    email: 'email',
    phone: 'text',
    identity_no: 'text',
    number: 'number',
    currency: 'number',
    date: 'date',
};

function getAddressValue() {
    const v = props.modelValue;
    if (v && typeof v === 'object') return v;
    return { line1: '', line2: '', postcode: '', city: '', state: '' };
}

function setAddressSubField(key, value) {
    emit('update:modelValue', { ...getAddressValue(), [key]: value });
}

function toggleCheckbox(option) {
    const current = Array.isArray(props.modelValue) ? [...props.modelValue] : [];
    if (current.includes(option)) {
        emit('update:modelValue', current.filter((item) => item !== option));
    } else {
        emit('update:modelValue', [...current, option]);
    }
}
</script>

<template>
    <!-- Note -->
    <div v-if="field.type === 'note'" class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4 text-sm leading-7 text-slate-700">
        <p class="font-semibold text-slate-900">{{ field.label }}</p>
        <p v-if="field.help_text" class="mt-2">{{ field.help_text }}</p>
    </div>

    <!-- Instruction Text -->
    <div v-else-if="field.type === 'instruction_text'" class="rounded-[1.5rem] border border-blue-200 bg-blue-50 p-4 text-sm leading-6 text-blue-900">
        <p class="font-semibold">{{ field.label }}</p>
        <p class="mt-2">{{ field.help_text }}</p>
    </div>

    <!-- Office Use Box -->
    <div v-else-if="field.type === 'office_use_box'" class="rounded-[1.5rem] border border-dashed border-slate-300 bg-slate-50 p-4">
        <p class="text-sm font-semibold text-slate-900">{{ field.label }}</p>
        <p class="mt-2 text-sm leading-6 text-slate-500">{{ field.help_text || 'Ruangan ini disediakan untuk kegunaan pejabat.' }}</p>
    </div>

    <!-- Address: manual fill -->
    <div v-else-if="field.type === 'address_my'" class="space-y-3">
        <p class="text-sm font-medium text-slate-800">
            {{ field.label }}<span v-if="field.is_required" class="text-red-500">*</span>
        </p>
        <div class="space-y-2">
            <input :value="getAddressValue().line1" type="text" placeholder="Nombor & Nama Jalan / Taman"
                class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-950 shadow-sm"
                @input="setAddressSubField('line1', $event.target.value)" />
            <input :value="getAddressValue().line2" type="text" placeholder="Kawasan / Pekan (pilihan)"
                class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-950 shadow-sm"
                @input="setAddressSubField('line2', $event.target.value)" />
            <div class="grid grid-cols-2 gap-2">
                <input :value="getAddressValue().postcode" type="text" placeholder="Poskod" maxlength="5"
                    class="h-11 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-950 shadow-sm"
                    @input="setAddressSubField('postcode', $event.target.value)" />
                <input :value="getAddressValue().city" type="text" placeholder="Bandar"
                    class="h-11 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-950 shadow-sm"
                    @input="setAddressSubField('city', $event.target.value)" />
            </div>
            <select :value="getAddressValue().state"
                class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-950 shadow-sm"
                @change="setAddressSubField('state', $event.target.value)">
                <option value="" disabled>-- Pilih Negeri --</option>
                <option v-for="st in MY_STATES" :key="st" :value="st">{{ st }}</option>
            </select>
        </div>
        <p v-if="error" class="text-sm text-red-700">{{ error }}</p>
    </div>

    <!-- Member address autofill: disabled -->
    <div v-else-if="field.type === 'member_address'" class="space-y-3">
        <p class="text-sm font-medium text-slate-800">
            {{ field.label }}<span v-if="field.is_required" class="text-red-500">*</span>
        </p>
        <div class="relative space-y-2">
            <input :value="getAddressValue().line1" type="text" placeholder="Nombor & Nama Jalan / Taman" disabled
                class="h-11 w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-3 text-sm text-slate-600" />
            <input :value="getAddressValue().line2" type="text" placeholder="Kawasan / Pekan (pilihan)" disabled
                class="h-11 w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-3 text-sm text-slate-600" />
            <div class="grid grid-cols-2 gap-2">
                <input :value="getAddressValue().postcode" type="text" placeholder="Poskod" disabled
                    class="h-11 cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-3 text-sm text-slate-600" />
                <input :value="getAddressValue().city" type="text" placeholder="Bandar" disabled
                    class="h-11 cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-3 text-sm text-slate-600" />
            </div>
            <input :value="getAddressValue().state" type="text" placeholder="Negeri" disabled
                class="h-11 w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-3 text-sm text-slate-600" />
            <span class="absolute right-3 top-1 rounded bg-purple-50 px-1.5 py-0.5 text-[10px] font-medium text-purple-600">Auto</span>
        </div>
        <p v-if="error" class="text-sm text-red-700">{{ error }}</p>
    </div>

    <!-- Member autofill: disabled readonly -->
    <div v-else-if="isMemberAutofill" class="space-y-1">
        <label :for="field.field_key" class="text-sm font-medium text-slate-800">
            {{ field.label }}<span v-if="field.is_required" class="text-red-500">*</span>
        </label>
        <div class="relative">
            <input :id="field.field_key" :value="modelValue" disabled
                class="h-11 w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-3 text-sm text-slate-600" />
            <span class="absolute right-3 top-1/2 -translate-y-1/2 rounded bg-purple-50 px-1.5 py-0.5 text-[10px] font-medium text-purple-600">Auto</span>
        </div>
        <p v-if="error" class="text-sm text-red-700">{{ error }}</p>
    </div>

    <!-- Standard inputs -->
    <div v-else-if="['short_text', 'email', 'phone', 'identity_no', 'number', 'currency', 'date'].includes(field.type)" class="space-y-1">
        <TextInput :id="field.field_key" :model-value="modelValue"
            @update:model-value="emit('update:modelValue', $event)"
            :label="`${field.label}${field.is_required ? ' *' : ''}`"
            :type="inputTypeMap[field.type] || 'text'"
            :error="error" />
        <p v-if="autofilled" class="text-xs font-medium text-blue-600">Auto-isi</p>
    </div>

    <!-- Long text -->
    <div v-else-if="field.type === 'long_text'" class="space-y-1">
        <TextareaInput :id="field.field_key" :model-value="modelValue"
            @update:model-value="emit('update:modelValue', $event)"
            :label="`${field.label}${field.is_required ? ' *' : ''}`"
            :help="field.help_text"
            :error="error" />
        <p v-if="autofilled" class="text-xs font-medium text-blue-600">Auto-isi</p>
    </div>

    <!-- Select dropdown -->
    <div v-else-if="field.type === 'select'" class="space-y-2">
        <label :for="field.field_key" class="text-sm font-medium text-slate-800">{{ field.label }}<span v-if="field.is_required"> *</span></label>
        <select :id="field.field_key" :value="modelValue"
            @change="emit('update:modelValue', $event.target.value)"
            class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-950 shadow-sm">
            <option value="">Pilih pilihan</option>
            <option v-for="option in field.options" :key="option" :value="option">{{ option }}</option>
        </select>
        <p v-if="autofilled" class="text-xs font-medium text-blue-600">Auto-isi</p>
        <p v-if="error" class="text-sm text-red-700">{{ error }}</p>
    </div>

    <!-- Radio / Yes-No -->
    <div v-else-if="['radio', 'yes_no'].includes(field.type)" class="space-y-3">
        <p class="text-sm font-medium text-slate-800">{{ field.label }}<span v-if="field.is_required"> *</span></p>
        <div class="flex flex-wrap gap-3">
            <label v-for="option in field.type === 'yes_no' ? ['yes', 'no'] : field.options"
                :key="option"
                class="flex items-center gap-2 rounded-full border border-slate-300 bg-white px-4 py-2 text-sm text-slate-700"
                :class="modelValue === option ? 'border-teal-500 bg-teal-50 text-teal-800' : ''">
                <input :value="option" :checked="modelValue === option" type="radio"
                    @change="emit('update:modelValue', option)" />
                {{ field.type === 'yes_no' ? (option === 'yes' ? 'Ya' : 'Tidak') : option }}
            </label>
        </div>
        <p v-if="autofilled" class="text-xs font-medium text-blue-600">Auto-isi</p>
        <p v-if="error" class="text-sm text-red-700">{{ error }}</p>
    </div>

    <!-- Checkbox -->
    <div v-else-if="field.type === 'checkbox'" class="space-y-3">
        <p class="text-sm font-medium text-slate-800">{{ field.label }}<span v-if="field.is_required"> *</span></p>
        <div class="flex flex-col gap-2">
            <label v-for="option in field.options" :key="option"
                class="flex items-center gap-3 rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700"
                :class="Array.isArray(modelValue) && modelValue.includes(option) ? 'border-teal-500 bg-teal-50' : ''">
                <input type="checkbox" :checked="Array.isArray(modelValue) && modelValue.includes(option)"
                    @change="toggleCheckbox(option)" />
                {{ option }}
            </label>
        </div>
        <p v-if="error" class="text-sm text-red-700">{{ error }}</p>
    </div>

    <!-- File upload -->
    <div v-else-if="field.type === 'file'" class="space-y-1">
        <FileUploader :id="field.field_key" :model-value="fileValue"
            @update:model-value="emit('update:fileValue', $event)"
            :label="`${field.label}${field.is_required ? ' *' : ''}`"
            accept=".pdf,.jpg,.jpeg,.png,.webp"
            helper-text="Format disokong: PDF, JPG, JPEG, PNG, WEBP. Saiz maksimum 5MB."
            :error="error" />
    </div>

    <!-- Signature -->
    <div v-else-if="field.type === 'signature'" class="space-y-2">
        <label class="text-sm font-medium text-slate-800">{{ field.label }}<span v-if="field.is_required"> *</span></label>
        <SignaturePad :model-value="modelValue" @update:model-value="emit('update:modelValue', $event)" :error="error" />
    </div>

    <!-- Agreement checkbox -->
    <div v-else-if="field.type === 'agreement_checkbox'" class="rounded-[1.5rem] border border-slate-200 bg-white p-4">
        <label class="flex items-start gap-3">
            <input type="checkbox" :checked="modelValue" @change="emit('update:modelValue', $event.target.checked)" class="mt-1" />
            <div class="space-y-2">
                <p class="text-sm font-semibold text-slate-950">{{ field.label }}<span v-if="field.is_required"> *</span></p>
                <p class="text-sm leading-6 text-slate-600">{{ field.help_text }}</p>
            </div>
        </label>
        <p v-if="error" class="mt-2 text-sm text-red-700">{{ error }}</p>
    </div>
</template>
