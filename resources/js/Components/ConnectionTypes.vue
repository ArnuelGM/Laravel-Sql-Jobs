<template>
    <select
        class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
    >
      <option v-for="type in types" :key="type" :value="type">{{ type }}</option>
    </select>
</template>

<script>
export default {

  props: ['modelValue'],
  emits: ['update:modelValue'],

  data: () => ({
    types: [
      'mysql',
      'pgsql',
      'sqlsrv',
      'sqlite'
    ]
  })
}
</script>
