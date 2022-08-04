<template>
  <span
    class="inline-block px-2 py-1 rounded uppercase font-bold text-xs"
    :class="{
      'bg-indigo-200 text-indigo-700':  status === 'IN_PROGRESS',
      'bg-green-200 text-green-700': status === 'DONE',
      'bg-slate-200 text-slate-700': status === 'PENDING',
      'bg-gray-200 text-gray-700': status === 'CANCELLED',
      'bg-red-200 text-red-700': status === 'ERROR'
    }"
  >
    {{ status }}
  </span>
</template>

<script>
export default {
  props: ['status']
}
</script>
