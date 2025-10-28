<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeaderCell, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { Edit, Plus, Trash } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const page = usePage() as unknown as {
    props: {
        routineTasks: {
            data: Array<{ id: number; nama_tugas: string; divisi: { id: number; nama_divisi: string } | null }>;
            current_page: number;
            last_page: number;
        };
        divisis: Array<{ id: number; nama_divisi: string }>;
        filters: {
            search: string;
            perPage: number;
            divisi: string;
        };
    };
};

const routineTasks = computed(() => page.props.routineTasks.data || []);
const divisis = computed(() => page.props.divisis || []);
const currentPage = computed(() => Number(page.props.routineTasks.current_page) || 1);
const lastPage = computed(() => Number(page.props.routineTasks.last_page) || 1);

const search = ref(page.props.filters.search || '');
const perPage = ref(page.props.filters.perPage || 5);
const divisi = ref(page.props.filters.divisi || 'all');

const showForm = ref(false);
const form = useForm({
    id: null,
    nama_tugas: '',
    divisi_id: null,
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Tugasku', href: '/dashboard/tugasku' },
    { title: 'Kelola Tugas Rutin', href: '/dashboard/tugasku/routine' },
];

const openForm = (routineTask: any = null) => {
    if (routineTask) {
        form.id = routineTask.id;
        form.nama_tugas = routineTask.nama_tugas;
        form.divisi_id = routineTask.divisi_id;
    } else {
        form.reset();
        form.divisi_id = null;
    }
    showForm.value = true;
};

const closeForm = () => {
    form.reset();
    form.nama_tugas = '';
    form.divisi_id = null;
    showForm.value = false;
};

const submitForm = () => {
    if (form.id) {
        form.put(route('routine.update', form.id), { onSuccess: closeForm });
    } else {
        form.post(route('routine.store'), { onSuccess: closeForm });
    }
};

const deleteTask = (id: number) => {
    if (confirm('Yakin hapus tugas rutin ini?')) {
        router.delete(route('routine.destroy', id));
    }
};

const goPage = (targetPage: number) => {
    router.get(
        route('routine.index'),
        {
            page: targetPage,
            search: search.value,
            perPage: perPage.value,
            divisi: divisi.value,
        },
        { preserveState: true, replace: true },
    );
};

watch([search, perPage, divisi], () => {
    goPage(1);
});

const pageNumbers = computed(() => {
    const pages = [];
    const start = Math.max(currentPage.value - 2, 1);
    const end = Math.min(start + 4, lastPage.value);
    for (let i = start; i <= end; i++) {
        pages.push(i);
    }
    return pages;
});
</script>

<template>
    <Head title="Kelola Tugas Rutin" />
    <AppLayout :breadcrumbs="breadcrumbs" active-menu="routine">
        <div class="mx-auto w-11/12 px-4 py-4">
            <div class="mb-4 flex items-center justify-between">
                <h1 class="flex items-center gap-2 text-xl font-bold">Kelola Tugas Rutin</h1>
                <Button @click="openForm"> <Plus class="mr-2" />Tambah Tugas Rutin</Button>
            </div>

            <Card>
                <CardHeader class="flex items-center justify-between">
                    <CardTitle>Tugas Rutin</CardTitle>
                    <div class="flex items-center gap-2">
                        <Input
                            v-model="search"
                            placeholder="Cari tugas rutin..."
                            class="w-48"
                            @keyup.enter="() => router.get(route('routine.index'), { search, perPage, divisi })"
                        />
                        <select
                            v-model="divisi"
                            @change="() => router.get(route('routine.index'), { search, perPage, divisi })"
                            class="rounded border px-2 py-1"
                        >
                            <option value="all">Semua Divisi</option>
                            <option v-for="divisi in divisis" :key="divisi.id" :value="divisi.id">{{ divisi.nama_divisi }}</option>
                        </select>
                        <select
                            v-model="perPage"
                            @change="() => router.get(route('routine.index'), { search, perPage, divisi })"
                            class="rounded border px-2 py-1"
                        >
                            <option :value="5">5</option>
                            <option :value="10">10</option>
                            <option :value="50">50</option>
                        </select>
                    </div>
                </CardHeader>

                <CardContent>
                    <div class="overflow-x-auto">
                        <Table class="min-w-full">
                            <TableHead>
                                <TableRow>
                                    <TableHeaderCell class="px-2 !py-1">Nama Tugas</TableHeaderCell>
                                    <TableHeaderCell class="px-2 !py-1">Divisi</TableHeaderCell>
                                    <TableHeaderCell class="flex gap-1 px-2 !py-1">Aksi</TableHeaderCell>
                                </TableRow>
                            </TableHead>
                            <TableBody v-if="routineTasks.length > 0">
                                <TableRow
                                    v-for="value in routineTasks"
                                    :key="value.id"
                                    class="['even:bg-gray-50', 'hover:bg-gray-100', 'transition-colors']"
                                >
                                    <TableCell class="px-2 !py-1">{{ value.nama_tugas }}</TableCell>
                                    <TableCell class="px-2 !py-1">{{ value.divisi?.nama_divisi }}</TableCell>
                                    <TableCell class="flex gap-1 px-2 !py-1">
                                        <Button size="sm" class="mr-2" @click="openForm(value)">
                                            <Edit class="h-4 w-4" />
                                        </Button>
                                        <Button size="sm" variant="destructive" @click="deleteTask(value.id)">
                                            <Trash class="h-4 w-4" />
                                        </Button>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                            <TableBody v-else>
                                <TableRow>
                                    <TableCell class="px-2 !py-20 font-black text-center" colspan="3">Tidak ada data tugas rutin.</TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                        <div class="mt-2 flex justify-end gap-1">
                            <Button :disabled="currentPage <= 1" @click="goPage(1)">First</Button>
                            <Button :disabled="currentPage <= 1" @click="goPage(Math.max(currentPage - 1, 1))">Prev</Button>

                            <Button v-for="p in pageNumbers" :key="p" :variant="p === currentPage ? 'default' : 'outline'" @click="goPage(p)">{{
                                p
                            }}</Button>

                            <Button :disabled="currentPage >= lastPage" @click="goPage(Math.min(currentPage + 1, lastPage))">Next</Button>
                            <Button :disabled="currentPage >= lastPage" @click="goPage(lastPage)">Last</Button>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- MODAL -->
            <div v-if="showForm" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30">
                <div class="w-96 rounded bg-white p-6 shadow">
                    <h2 class="mb-4 text-lg font-semibold">{{ form.id ? 'Edit' : 'Tambah' }} Tugas Rutin</h2>

                    <div class="mb-3">
                        <label class="mb-1 block">Nama Tugas</label>
                        <Input v-model="form.nama_tugas" placeholder="Masukkan nama tugas rutin" />
                    </div>

                    <div class="mb-3">
                        <label class="mb-1 block">Divisi</label>
                        <select v-model="form.divisi_id" class="w-full rounded border px-2 py-1">
                            <option :value="null">Pilih Divisi</option>
                            <option v-for="divisi in divisis" :key="divisi.id" :value="divisi.id">{{ divisi.nama_divisi }}</option>
                        </select>

                        <div class="mt-4 flex justify-end gap-2">
                            <Button variant="secondary" @click="closeForm">Batal</Button>
                            <Button @click="submitForm" :disabled="form.processing">
                                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                                Simpan
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
