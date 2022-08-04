<template>
    <select
        class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        :disabled=" !connections.length "
    >
      <option v-for="connection in connections" :key="connection.id" :value="connection.id">
        {{ connections.name }}
      </option>
    </select>
</template>

<script>
export default {

  props: ['modelValue', 'connections'],
  emits: ['update:modelValue'],

  data: () => ({
    connections: []
  }),

}
</script>
