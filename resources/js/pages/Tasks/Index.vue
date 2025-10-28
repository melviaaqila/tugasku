<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeaderCell, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { CircleCheckBig, Edit, LoaderCircle, Plus, Trash } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const page = usePage<{
    tasks: {
        data: Array<{ id: number; nama_tugas: string; jenis_tugas: string; status: string }>;
        current_page: number;
        last_page: number;
    };
    routineTasks?: Array<{ id: number; nama_tugas: string }>;
    filters: {
        search: string;
        perPage: number;
        status: string;
    };
}>();

const tasks = computed(() => page.props.tasks.data || []);
const routineTasks = computed(() => page.props.routineTasks || []);
const currentPage = computed(() => Number(page.props.tasks.current_page) || 1);
const lastPage = computed(() => Number(page.props.tasks.last_page) || 1);

const search = ref(page.props.filters.search || '');
const perPage = ref(page.props.filters.perPage || 5);
const status = ref(page.props.filters.status || 'all');

const showForm = ref(false);
const form = useForm({
    id: null,
    nama_tugas: '',
    jenis_tugas: 'tugas rutin',
    status: 'belum',
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Tugasku', href: '/dashboard/tugasku' },
];

const openForm = (task: any = null) => {
    if (task) {
        form.id = task.id;
        form.nama_tugas = task.nama_tugas;
        form.jenis_tugas = task.jenis_tugas;
        form.status = task.status;
    } else {
        form.reset();
        form.jenis_tugas = 'tugas rutin';
        form.status = 'belum';
    }
    showForm.value = true;
};

const updateStatus = (task: any, newStatus: string) => {
    console.log('Task ID:', task.id, task);

    form.id = task.id;
    form.nama_tugas = task.nama_tugas;
    form.jenis_tugas = task.jenis_tugas;
    form.status = newStatus;
    submitForm();
};

const closeForm = () => {
    form.reset();
    form.jenis_tugas = 'tugas rutin';
    form.status = 'belum';
    showForm.value = false;
};

const submitForm = () => {
    if (form.id) {
        form.put(route('tugasku.update', form.id), { onSuccess: closeForm });
    } else {
        form.post(route('tugasku.store'), { onSuccess: closeForm });
    }
};

const deleteTask = (id: number) => {
    if (confirm('Yakin hapus tugas ini?')) {
        router.delete(route('tugasku.destroy', id));
    }
};

const goPage = (targetPage: number) => {
    router.get(
        route('tugasku.index'),
        {
            page: targetPage,
            search: search.value,
            perPage: perPage.value,
            status: status.value,
        },
        { preserveState: true, replace: true },
    );
};

watch([search, perPage, status], () => {
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
    <Head title="Tugasku" />
    <AppLayout :breadcrumbs="breadcrumbs" active-menu="tugasku">
        <div class="mx-auto w-11/12 px-4 py-4">
            <div class="mb-4 flex items-center justify-between">
                <h1 class="flex items-center gap-2 text-xl font-bold"><CircleCheckBig class="text-blue-500" />Tugasku</h1>
                <Button @click="openForm"> <Plus class="mr-2" />Tambah Tugas</Button>
            </div>

            <Card>
                <CardHeader class="flex items-center justify-between">
                    <CardTitle>Daftar Tugasku</CardTitle>
                    <div class="flex items-center gap-2">
                        <Input
                            v-model="search"
                            placeholder="Cari tugas..."
                            class="w-48"
                            @keyup.enter="() => router.get(route('tugasku.index'), { search, perPage, status })"
                        />
                        <select
                            v-model="status"
                            @change="() => router.get(route('tugasku.index'), { search, perPage, status })"
                            class="rounded border px-2 py-1"
                        >
                            <option value="all">Semua Status</option>
                            <option value="belum">Belum</option>
                            <option value="sedang berlangsung">Sedang Berlangsung</option>
                            <option value="selesai">Selesai</option>
                        </select>
                        <select
                            v-model="perPage"
                            @change="() => router.get(route('tugasku.index'), { search, perPage, status })"
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
                                    <TableHeaderCell class="!py-1 px-2">Nama Tugas</TableHeaderCell>
                                    <TableHeaderCell class="!py-1 px-2">Jenis Tugas</TableHeaderCell>
                                    <TableHeaderCell class="!py-1 px-2">Status</TableHeaderCell>
                                    <TableHeaderCell class="!py-1 px-2 flex gap-1">Aksi</TableHeaderCell>
                                </TableRow>
                            </TableHead>
                            <TableBody v-if="tasks.length > 0">
                                <TableRow v-for="task in tasks" :key="task.id" class="['even:bg-gray-50', 'hover:bg-gray-100', 'transition-colors']">
                                    <TableCell class="!py-1 px-2">{{ task.nama_tugas }}</TableCell>
                                    <TableCell class="!py-1 px-2">{{ task.jenis_tugas }}</TableCell>
                                    <TableCell class="!py-1 px-2">{{ task.status }}</TableCell>
                                    <TableCell class="!py-1 px-2 flex gap-1">
                                        <Button
                                            v-if="task.status === 'belum'"
                                            size="sm"
                                            class="mr-2 bg-blue-500 hover:bg-blue-600"
                                            @click="updateStatus(task, 'sedang berlangsung')"
                                        >
                                            <CircleCheckBig />
                                        </Button>
                                        <Button
                                            v-if="task.status === 'sedang berlangsung'"
                                            size="sm"
                                            class="mr-2 bg-green-500 hover:bg-green-600"
                                            @click="updateStatus(task, 'selesai')"
                                        >
                                            <CircleCheckBig />
                                        </Button>
                                        <Button v-if="task.status === 'selesai'" size="sm" disabled class="mr-2 bg-gray-500 hover:bg-gray-600">
                                            <CircleCheckBig />
                                        </Button>
                                        <Button size="sm" class="mr-2" @click="openForm(task)">
                                            <Edit />
                                        </Button>
                                        <Button size="sm" variant="destructive" @click="deleteTask(task.id)">
                                            <Trash />
                                        </Button>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                            <TableBody v-else>
                                <TableRow>
                                    <TableCell class="!py-20 font-black px-2 text-center" colspan="4">Tidak ada data tugas.</TableCell>
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
            <div v-if="showForm" class="fixed inset-0 z-50 flex items-start justify-center overflow-auto bg-black/30 pt-20">
                <div class="w-3/5 rounded bg-white p-6 shadow">
                    <h2 class="mb-4 text-lg font-semibold">{{ form.id ? 'Edit' : 'Tambah' }} Tugas</h2>

                    <div class="mb-3 flex items-center gap-4">
                        <label class="mb-1 block">Jenis Tugas</label>
                        <select v-model="form.jenis_tugas" name="jenis_tugas" class="rounded border px-2 py-1">
                            <option selected value="tugas rutin">Tugas Rutin</option>
                            <option value="tugas tambahan">Tugas Tambahan</option>
                        </select>
                    </div>

                    <div v-if="form.jenis_tugas" class="mb-3">
                        <label class="mb-1 block">Nama Tugas</label>
                        <div v-if="form.jenis_tugas === 'tugas rutin'" class="grid max-h-96 grid-cols-2 overflow-auto rounded border p-2">
                            <div v-for="p in routineTasks" :key="p.id" class="mb-1 flex items-center gap-2">
                                <input type="radio" name="nama_tugas" :value="p.nama_tugas" v-model="form.nama_tugas" />
                                {{ p.nama_tugas }}
                            </div>
                        </div>

                        <div v-else-if="form.jenis_tugas === 'tugas tambahan'" class="mb-3">
                            <Input v-model="form.nama_tugas" placeholder="Masukkan nama tugas tambahan" />
                        </div>
                    </div>

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
    </AppLayout>
</template>
