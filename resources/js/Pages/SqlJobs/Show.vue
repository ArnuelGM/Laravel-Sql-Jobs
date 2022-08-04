<template>
  <AppLayout title="Sql Jobs">
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Sql Job Detail
        </h2>
        <Link href="/sqlJobs">
          <Button>Back</Button>
        </Link>
      </div>
    </template>
    <div class="py-12 pt-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">

          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-lg">{{ sqlJob.title }}</h3>
              <p class="text-sm text-gray-600">
                {{ sqlJob.description }}
              </p>
            </div>
            <SqlJobStatus :status="sqlJob.status" class="text-lg" />
          </div>

          <div class="mt-2">
            <span class="bg-indigo-100 text-indigo-500 inline-flex items-center px-2 py-1 rounded-lg font-bold">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              {{ getExecutionDate(sqlJob.execution_date) }}
            </span>
          </div>

          <div class="mt-4">
            <h3 class="text-lg">
              Connection
            </h3>
            <pre class="text-sm text-gray-600" v-text="`${ sqlJob.connection.name }, ${ sqlJob.connection.database }`" />
          </div>

          <div class="mt-4" v-if="sqlJob.error">
            <h3 class="text-lg text-red-600 font-bold">
              Error
            </h3>
            <code>
              <pre class="text-sm text-gray-600">{{ `${ sqlJob.error }` }}</pre>
            </code>
          </div>

          <div class="mt-4 flex gap-2 items-center">
            <Button type="button" v-if="! showScript" @click="showScript = true">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
              </svg>
              Show Script
            </Button>
            <Button type="button" v-else @click="showScript = false">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
              </svg>
              Hide Script
            </Button>
            <template v-if="sqlJob.status === 'PENDING'">
              <Button type="button" @click="confirmExecuteNow()">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd" />
                </svg>
                Execute Now
              </Button>
            </template>
          </div>

          <div v-if="showScript">
            <SshPre language="sql" label="SQL">{{ sqlJob.script }}</SshPre>
          </div>

        </div>
      </div>
    </div>

    <DialogModal :show="showModalExecuteNow">

      <template #title>Confirm</template>

      <template #content>Do you really wants to execute the script now?</template>

      <template #footer>
        <SecondaryButton class="btn btn-primary" @click="showModalExecuteNow = false">Cancel</SecondaryButton>
        <Button type="button" @click="executeNow()" :disabled="executing">Execute Now</Button>
      </template>

    </DialogModal>
  </AppLayout>
</template>

<script>

import SshPre from 'simple-syntax-highlighter'
import 'simple-syntax-highlighter/dist/sshpre.css'
import Label from '@/Jetstream/Label.vue'
import Button from '@/Jetstream/Button.vue'
import { Link } from '@inertiajs/inertia-vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import SectionTitle from '@/Jetstream/SectionTitle.vue'
import DialogModal from '@/Jetstream/DialogModal.vue'
import SecondaryButton from '@/Jetstream/SecondaryButton.vue'
import SqlJobStatus from '@/Components/SqlJobStatus.vue';

export default {

  props: {
    sqlJob: Object
  },

  components: {
    AppLayout,
    Link,
    Button,
    Label,
    SectionTitle,
    SqlJobStatus,
    SshPre,
    DialogModal,
    SecondaryButton
  },

  data() {
    return {
      showScript: false,
      showModalExecuteNow: false,
      executing: false
    }
  },

  methods: {

    getExecutionDate(date) {
      return new Date(date).toLocaleString();
    },

    confirmExecuteNow() {
      this.showModalExecuteNow = true;
    },

    executeNow() {

      this.executing = true;

      this.$inertia.visit(`/sqlJobs/${ this.sqlJob.id }/executeNow`, {

        method: 'post',

        onFinish: visit => {

          this.showModalExecuteNow = false;
          this.executing = true;

        }
      })

    }
  }
};
</script>
