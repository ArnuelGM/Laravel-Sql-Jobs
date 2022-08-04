<template>
  <AppLayout title="Connections">
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Connections List
        </h2>
        <Link href="/connections/create">
          <Button>New Connection</Button>
        </Link>
      </div>
    </template>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <table class="w-full">
            <thead>
              <tr>
                <th class="uppercase font-thin text-slate-400 text-xs px-4 py-2 bg-slate-50 border-b text-left w-20">#</th>
                <th class="uppercase font-thin text-slate-400 text-xs px-4 py-2 bg-slate-50 border-b text-left">Name</th>
                <th class="uppercase font-thin text-slate-400 text-xs px-4 py-2 bg-slate-50 border-b text-left">Type</th>
                <th class="uppercase font-thin text-slate-400 text-xs px-4 py-2 bg-slate-50 border-b text-right w-80">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(connection, index) in connections" :key="connection.id">
                <td class="border-b px-4 py-2"> {{ index +1 }} </td>
                <td class="border-b px-4 py-2 text-slate-800"> {{ connection.name }} </td>
                <td class="border-b px-4 py-2 text-slate-500"> {{ connection.type }} </td>
                <td class="border-b px-4 py-2 text-right">
                  <button @click="testConnection(connection)" class="text-orange-500 font-semibold">Test</button>
                  <!-- <Link :href="`/connections/${connection.id}`" class="text-orange-500 font-semibold">Test</Link> -->
                  &nbsp;
                  &nbsp;
                  <Link :href="`/connections/${connection.id}/edit`" class="text-indigo-500 font-semibold">Editar</Link>
                  &nbsp;
                  &nbsp;
                  <a @click="promptDelete(connection)" class="text-red-600 font-semibold cursor-pointer">Eliminar</a>
                </td>
              </tr>
              <tr v-if="! connections.length">
                <td colspan="4" v-if=" ! connections.legnth" class="text-center py-4">
                  <span class="inline-block mb-4 text-slate-500 text-lg tracking-wide font-extralight"> No connections found. </span>
                  <br>
                  <Link href="/connections/create">
                    <Button>New Connection</Button>
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <DialogModal :show="confirmDeleteModal">
      <template #title>
        <h2 class="text-red-700 font-bold">
          Warning
        </h2>
      </template>

      <template #content>
        Do you really want to delete this connection?
      </template>

      <template #footer>
        <SecondaryButton @click="confirmDeleteModal = false">Cancel</SecondaryButton>
        <DangerButton @click="deleteConnection()">Delete Connection</DangerButton>
      </template>
    </DialogModal>
  </AppLayout>
</template>

<script>
import DialogModal from '@/Jetstream/DialogModal.vue'
import Button from '@/Jetstream/Button.vue'
import DangerButton from '@/Jetstream/DangerButton.vue'
import SecondaryButton from '@/Jetstream/SecondaryButton.vue'
import { Link } from '@inertiajs/inertia-vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

export default {
  components: {
    AppLayout,
    Link,
    Button,
    DialogModal,
    DangerButton,
    SecondaryButton
  },
  props: {
    connections: {
      type: Array
    }
  },

  data: () => ({
    connection: null,
    confirmDeleteModal: false
  }),

  methods: {

    promptDelete(connection) {
      this.confirmDeleteModal = true
      this.connection = connection
    },

    deleteConnection() {
      this.$inertia.delete(`/connections/${ this.connection.id }`)
      this.connection = null
      this.confirmDeleteModal = false
    },

    testConnection(connection) {
      console.log(
        this.$inertia.get(`/connections/${ connection.id }`, {
          preserveState: true,
          preserveScroll: true,
          onError: errors => {
            console.log(errors)
          }
        })
      )
    }
  }
};
</script>
