<template>
  <AppLayout title="Connections">
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Update Connection
        </h2>
        <Link href="/connections">
          <Button>Back</Button>
        </Link>
      </div>
    </template>
    <div class="py-12 pt-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

          <JetFormSection @submitted="save">

            <template #title>Update Connection</template>
            <template #description>Edit a saved connection.</template>

            <template #form>
              <div class="col-span-12">
                <div class="mb-4">
                  <Label class="text-md mb-2" value="Connection Name" />
                  <Input class="w-full" type="text" v-model="form.name" />
                  <Label class="text-xs mt-1 text-red-600" v-if="errors.name" :value="errors.name" />
                </div>

                <div class="mb-4">
                  <Label class="text-md mb-2" value="Connection type" />
                  <ConnectionTypes v-model="form.type" class="w-full" />
                  <Label class="text-xs mt-1 text-red-600" v-if="errors.type" :value="errors.type" />
                </div>

                <div class="mb-4">
                  <Label class="text-md mb-2" value="Host" />
                  <Input class="w-full" type="text" v-model="form.host" />
                  <Label class="text-xs mt-1 text-red-600" v-if="errors.host" :value="errors.host" />
                </div>

                <div class="mb-4">
                  <Label class="text-md mb-2" value="Port" />
                  <Input class="w-full" type="text" v-model="form.port" />
                  <Label class="text-xs mt-1 text-red-600" v-if="errors.port" :value="errors.port" />
                </div>

                <div class="mb-4">
                  <Label class="text-md mb-2" value="Database Name" />
                  <Input class="w-full" type="text" v-model="form.database" />
                  <Label class="text-xs mt-1 text-red-600" v-if="errors.database" :value="errors.database" />
                </div>

                <div class="mb-4">
                  <Label class="text-md mb-2" value="User" />
                  <Input class="w-full" type="text" v-model="form.user" />
                  <Label class="text-xs mt-1 text-red-600" v-if="errors.user" :value="errors.user" />
                </div>

                <div class="mb-4">
                  <Label class="text-md mb-2" value="Password" />
                  <Input class="w-full" type="password" v-model="form.password" />
                  <Label class="text-xs mt-1 text-red-600" v-if="errors.password" :value="errors.password" />
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

import Label from '@/Jetstream/Label.vue'
import Input from '@/Jetstream/Input.vue'
import Button from '@/Jetstream/Button.vue'
import { Link } from '@inertiajs/inertia-vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import JetFormSection from '@/Jetstream/FormSection.vue'
import ConnectionTypes from '@/Components/ConnectionTypes.vue'

export default {

  props: {
    connection: Object,
    errors: Object
  },

  components: {
    AppLayout,
    Link,
    Button,
    Label,
    Input,
    JetFormSection,
    ConnectionTypes
  },

  data() {
    return {
      form: this.$inertia.form({
        name:     this.connection.name,
        type:     this.connection.type,
        host:     this.connection.host,
        port:     this.connection.port,
        database: this.connection.database,
        user:     this.connection.user,
        password: this.connection.password
      })
    }
  },

  methods: {

    save() {

      this.form.put(route('connections.update', this.connection.id))

    }

  }
};
</script>
