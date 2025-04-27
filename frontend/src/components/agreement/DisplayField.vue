<script setup>

defineProps({
  label: String,
  value: String
});

</script>

<template>
  <div class="mb-4">
    <label class="block text-sm font-medium mb-1">
      {{ label }}
    </label>
    <div class="bg-gray-700 rounded-md p-3">
      {{ value || '—' }}
    </div>
  </div>
</template>