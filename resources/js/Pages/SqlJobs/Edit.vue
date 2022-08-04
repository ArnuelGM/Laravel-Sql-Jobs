<template>
  <AppLayout title="Sql Jobs">
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Update Sql Job
        </h2>
        <Link href="/sqlJobs">
          <Button>Back</Button>
        </Link>
      </div>
    </template>
    <div class="py-12 pt-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

          <JetFormSection @submitted="save">

            <template #title>Update Sql Job</template>
            <template #description>Edit a Sql Job created previusly.</template>

            <template #form>
              <div class="col-span-12">
                <div class="mb-4">
                  <Label class="text-md mb-2" value="Title" />
                  <Input class="w-full" type="text" v-model="form.title" />
                  <Label class="text-xs mt-1 text-red-600" v-if="errors.title" :value="errors.title" />
                </div>

                <div class="mb-4">
                  <Label class="text-md mb-2" value="Connection" />
                  <Input class="w-full" type="text" v-model="form.connection_id" />
                  <Label class="text-xs mt-1 text-red-600" v-if="errors.connection_id" :value="errors.connection_id" />
                </div>

                <div class="mb-4">
                  <Label class="text-md mb-2" value="Description" />
                  <Input class="w-full" type="text" v-model="form.description" />
                  <Label class="text-xs mt-1 text-red-600" v-if="errors.description" :value="errors.description" />
                </div>

                <div class="mb-4">
                  <Label class="text-md mb-2" value="Execution Date" />
                  <div class="flex gap-2">
                    <Input class="w-full" type="date" v-model="form.date"/>
                    <Input class="w-full" type="time" v-model="form.time"/>
                  </div>
                  <Label class="text-xs mt-1 text-red-600"
                    v-if="errors.execution_date"
                    :value="errors.execution_date"
                  />
                </div>

                <div class="mb-4">
                  <Label class="text-md mb-2" value="Script" />
                  <textarea rows="6" v-model="form.script" class="w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"></textarea>
                  <Label class="text-xs mt-1 text-red-600" v-if="errors.script" :value="errors.script" />
                </div>
              </div>
            </template>

            <template #actions>
              <Button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Update</Button>
            </template>
          </JetFormSection>

      </div>
    </div>
  </AppLayout>
</template>

<script>

import '@vuepic/vue-datepicker/dist/main.css'
import Datepicker from '@vuepic/vue-datepicker';
import Label from '@/Jetstream/Label.vue'
import Input from '@/Jetstream/Input.vue'
import Button from '@/Jetstream/Button.vue'
import { Link } from '@inertiajs/inertia-vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import JetFormSection from '@/Jetstream/FormSection.vue'
import ConnectionTypes from '@/Components/ConnectionTypes.vue';

export default {

  props: {
    sqlJob: Object,
    errors: Object
  },

  components: {
    AppLayout,
    Link,
    Button,
    Label,
    Input,
    JetFormSection,
    ConnectionTypes,
    Datepicker
  },

  data() {
    return {
      form: this.$inertia.form({
        title: this.sqlJob.title,
        description: this.sqlJob.description,
        connection_id: this.sqlJob.connection_id,
        script: this.sqlJob.script,
        execution_date: this.sqlJob.execution_date,
        date: this.sqlJob.execution_date.substring(0, 10),
        time: this.sqlJob.execution_date.substring(11),
        status: this.sqlJob.status,
      })
    }
  },

  methods: {

    save() {
      this.form.execution_date = `${ this.form.date } ${ this.form.time }`
      this.form.put(route('sqlJobs.update', this.sqlJob.id))
    },

  }
};
</script>
