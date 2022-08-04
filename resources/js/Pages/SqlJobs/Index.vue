<template>
  <AppLayout title="Connections">
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Sql Jobs
        </h2>
        <Link href="/sqlJobs/create">
          <Button>New Sql Job</Button>
        </Link>
      </div>
    </template>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <table class="w-full">
            <thead>
              <tr>
                <th class="uppercase font-thin text-slate-400 text-xs px-4 py-2 bg-slate-50 border-b text-left w-16">#</th>
                <th class="uppercase font-thin text-slate-400 text-xs px-4 py-2 bg-slate-50 border-b text-left">Title</th>
                <th class="uppercase font-thin text-slate-400 text-xs px-4 py-2 bg-slate-50 border-b text-left">Connection</th>
                <th class="uppercase font-thin text-slate-400 text-xs px-4 py-2 bg-slate-50 border-b text-left">Date</th>
                <th class="uppercase font-thin text-slate-400 text-xs px-4 py-2 bg-slate-50 border-b text-left">Status</th>
                <th class="uppercase font-thin text-slate-400 text-xs px-4 py-2 bg-slate-50 border-b text-right w-60">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(job, index) in sqlJobs.data" :key="job.id">
                <td class="border-b px-4 py-2"> {{ index +1 }} </td>
                <td class="border-b px-4 py-2 text-slate-800"> {{ job.title }} </td>
                <td class="border-b px-4 py-2 text-slate-500"> {{ job.connection && job.connection.name }} </td>
                <td class="border-b px-4 py-2 text-slate-500"> {{ new Date(job.execution_date).toLocaleString() }} </td>
                <td class="border-b px-4 py-2 text-slate-500">
                  <SqlJobStatus :status="job.status"/>
                </td>
                <td class="border-b px-4 py-2 text-right">
                  <Link :href="`/sqlJobs/${job.id}/edit`" class="text-indigo-500 font-semibold" v-if="job.status === 'PENDING'">Editar</Link>
                  &nbsp;
                  <Link :href="`/sqlJobs/${job.id}`" class="text-orange-500 font-semibold">Detail</Link>
                  &nbsp;
                  <a @click="promptDelete(job)" class="text-red-600 font-semibold cursor-pointer">Eliminar</a>
                </td>
              </tr>
              <tr v-if="! sqlJobs.data.length">
                <td colspan="6" v-if=" ! sqlJobs.legnth" class="text-center py-4">
                  <span class="inline-block mb-4 text-slate-500 text-lg tracking-wide font-extralight">No sql jobs created yet.</span>
                  <br>
                  <Link href="/sqlJobs/create">
                    <Button>New Sql Job</Button>
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="mt-2 flex justify-end" v-if="sqlJobs.links && sqlJobs.links.length">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <ul class="inline-flex items-center divide-x">
              <template v-for="link in sqlJobs.links" :key="link.label">
                <li class="inline-flex items-center">
                  <template v-if="link.active || ! link.url">
                    <span class="px-4 py-2 font-bold inline-block" v-html="link.label"/>
                  </template>
                  <Link class="px-4 py-2 font-bold inline-block" v-else
                    v-html="link.label"
                    :href="link.url"
                    :class="{ 'text-blue-500': ! link.active }"
                  />
                </li>
              </template>
            </ul>
          </div>
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
        Do you really want to delete this Sql Job?
      </template>

      <template #footer>
        <SecondaryButton @click="confirmDeleteModal = false">Cancel</SecondaryButton>
        <DangerButton @click="deleteSqlJob()">Delete Sql Job</DangerButton>
      </template>
    </DialogModal>
  </AppLayout>
</template>

<script>
import Button from '@/Jetstream/Button.vue'
import { Link } from '@inertiajs/inertia-vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import DialogModal from '@/Jetstream/DialogModal.vue'
import DangerButton from '@/Jetstream/DangerButton.vue'
import SqlJobStatus from '@/Components/SqlJobStatus.vue'
import SecondaryButton from '@/Jetstream/SecondaryButton.vue'

export default {
  components: {
    AppLayout,
    Link,
    Button,
    DialogModal,
    DangerButton,
    SecondaryButton,
    SqlJobStatus
  },

  props: {
    sqlJobs: Object
  },

  data: () => ({
    sqlJob: null,
    confirmDeleteModal: false
  }),

  methods: {

    promptDelete(sqlJob) {
      this.confirmDeleteModal = true
      this.sqlJob = sqlJob
    },

    deleteSqlJob() {
      this.$inertia.delete(`/sqlJobs/${ this.sqlJob.id }`)
      this.sqlJob = null
      this.confirmDeleteModal = false
    },
  }
};
</script>
