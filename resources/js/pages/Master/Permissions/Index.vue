<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { LoaderCircle, Plus, Edit, Trash } from 'lucide-vue-next';

const page = usePage<{
  permissions: {
    data: Array<{ id: number; name: string; guard_name: string }>;
    links: any;
  },
  search?: string,
  perPage?: number
}>();

const permissions = computed(() => page.props.permissions.data);

const search = ref(page.props.search || '');
const perPage = ref(page.props.perPage || 5);

const showForm = ref(false);
const form = useForm({
  id: null,
  name: '',
  guard_name: 'web',
});

const breadcrumbs = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Permission Management', href: '/permissions' }
];

const openForm = (perm: any = null) => {
  if (perm) {
    form.id = perm.id;
    form.name = perm.name;
    form.guard_name = perm.guard_name;
  } else {
    form.reset();
    form.guard_name = 'web';
  }
  showForm.value = true;
};

const closeForm = () => {
  form.reset();
  form.guard_name = 'web';
  showForm.value = false;
};

const submitForm = () => {
  if (form.id) {
    form.put(route('permissions.update', form.id), { onSuccess: closeForm });
  } else {
    form.post(route('permissions.store'), { onSuccess: closeForm });
  }
};

const deletePermission = (id: number) => {
  if (confirm('Yakin hapus permission ini?')) {
    router.delete(route('permissions.destroy', id));
  }
};
</script>

<template>
  <Head title="Permission Management" />
  <AppLayout :breadcrumbs="breadcrumbs" active-menu="permissions">
    <div class="w-11/12 mx-auto px-4 py-4">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-bold">Permission Management</h1>
        <Button @click="openForm"><Plus class="mr-2" /> Tambah Permission</Button>
      </div>

      <Card>
        <CardHeader class="flex justify-between items-center">
          <CardTitle>Daftar Permission</CardTitle>
          <div class="flex gap-2 items-center">
            <Input
              v-model="search"
              placeholder="Cari permission..."
              class="w-48"
              @keyup.enter="() => router.get(route('permissions.index'), { search, perPage })"
            />
            <select
              v-model="perPage"
              @change="() => router.get(route('permissions.index'), { search, perPage })"
              class="border rounded px-2 py-1"
            >
              <option :value="5">5</option>
              <option :value="10">10</option>
              <option :value="50">50</option>
            </select>
          </div>
        </CardHeader>

        <CardContent>
          <div class="overflow-x-auto">
            <table class="min-w-full border-collapse">
              <thead>
                <tr class="bg-gray-100">
                  <th class="border px-4 py-2 w-16">ID</th>
                  <th class="border px-4 py-2 w-64">Nama Permission</th>
                  <th class="border px-4 py-2 w-48">Guard</th>
                  <th class="border px-4 py-2 w-32">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="perm in permissions" :key="perm.id">
                  <td class="border px-4 py-2">{{ perm.id }}</td>
                  <td class="border px-4 py-2">{{ perm.name }}</td>
                  <td class="border px-4 py-2">{{ perm.guard_name }}</td>
                  <td class="border px-4 py-2 text-center whitespace-nowrap">
                    <Button size="sm" class="mr-2" @click="openForm(perm)">
                      <Edit />
                    </Button>
                    <Button size="sm" variant="destructive" @click="deletePermission(perm.id)">
                      <Trash />
                    </Button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </CardContent>
      </Card>

      <!-- MODAL -->
      <div
        v-if="showForm"
        class="fixed inset-0 bg-black/30 flex justify-center items-start pt-20 z-50 overflow-auto"
      >
        <div class="bg-white p-6 rounded shadow w-96">
          <h2 class="text-lg font-semibold mb-4">{{ form.id ? 'Edit' : 'Tambah' }} Permission</h2>

          <div class="mb-3">
            <label class="block mb-1">Nama Permission</label>
            <Input v-model="form.name" placeholder="Nama Permission" />
          </div>

          <div class="mb-3">
            <label class="block mb-1">Guard Name</label>
            <Input v-model="form.guard_name" placeholder="Guard Name" />
          </div>

          <div class="flex justify-end gap-2 mt-4">
            <Button variant="secondary" @click="closeForm">Batal</Button>
            <Button @click="submitForm" :disabled="form.processing">
              <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
              Simpan
            </Button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
