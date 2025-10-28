<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeaderCell, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { CircleCheckBig, Download, LoaderCircle } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import * as XLSX from 'xlsx';

const page = usePage<{
    histories: Array<{
        id: number;
        nama_tugas: string;
        jenis_tugas: string;
        divisi: string;
        tanggal_dibuat: string;
        tanggal_selesai: string;
    }>;
}>();

const showForm = ref(false);
const isExporting = ref(false);
const form = useForm({
    format: 'xlsx',
    tanggal_mulai: '',
    tanggal_akhir: '',
});

const histories = page.props.histories;

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Tugasku', href: '/dashboard/tugasku' },
];

const filteredPreview = computed(() => {
    if (!form.tanggal_mulai && !form.tanggal_akhir) {
        return histories;
    }

    return histories.filter(item => {
        const tanggalSelesai = new Date(item.tanggal_selesai);
        const mulai = form.tanggal_mulai ? new Date(form.tanggal_mulai) : null;
        const akhir = form.tanggal_akhir ? new Date(form.tanggal_akhir) : null;

        if (mulai && akhir) {
            return tanggalSelesai >= mulai && tanggalSelesai <= akhir;
        } else if (mulai) {
            return tanggalSelesai >= mulai;
        } else if (akhir) {
            return tanggalSelesai <= akhir;
        }
        return true;
    });
});

const hasFilteredData = computed(() => {
    return filteredPreview.value.length > 0;
});

const closeForm = () => {
    form.reset();
    form.tanggal_mulai = '';
    form.tanggal_akhir = '';
    showForm.value = false;
};

const submitForm = () => {
    if (!hasFilteredData.value) {
        return;
    }

    isExporting.value = true;

    const exportData = filteredPreview.value.map(item => ({
        'Nama Tugas': item.nama_tugas,
        'Jenis Tugas': item.jenis_tugas,
        'Divisi': item.divisi,
        'Tanggal Dibuat': item.tanggal_dibuat,
        'Tanggal Selesai': item.tanggal_selesai,
    }));

    const worksheet = XLSX.utils.json_to_sheet(exportData);

    worksheet['!cols'] = [
        { wch: 30 },
        { wch: 20 },
        { wch: 20 },
        { wch: 15 },
        { wch: 15 },
    ];

    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Laporan Tugas');

    const timestamp = new Date().toISOString().split('T')[0];
    const filename = `laporan-tugas-${timestamp}.xlsx`;

    XLSX.writeFile(workbook, filename);

    isExporting.value = false;
    closeForm();
};

const goBack = () => {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        router.visit('/tasks');
    }
};
</script>

<template>
    <Head title="History" />
    <AppLayout :breadcrumbs="breadcrumbs" active-menu="history">
        <div class="mx-auto w-11/12 px-4 py-4">
            <div class="mb-4 flex items-center justify-between">
                <h1 class="flex items-center gap-2 text-xl font-bold">Histori Tugasku</h1>
                <div class="flex items-center gap-2">
                    <Button @click="showForm = true" class="bg-green-500 text-white hover:bg-green-600"><Download /> Export Laporan</Button>
                    <Button @click="goBack">Kembali</Button>
                </div>
            </div>

            <Card>
                <CardHeader class="flex items-center">
                    <CardTitle class="flex gap-2"><CircleCheckBig class="h-4 w-4 text-green-500" /> Daftar Tugas Selesai</CardTitle>
                </CardHeader>

                <CardContent>
                    <div class="overflow-x-auto">
                        <Table class="overflow-x-auto">
                            <TableHead>
                                <TableRow>
                                    <TableHeaderCell class="px-2 !py-1">Nama Tugas</TableHeaderCell>
                                    <TableHeaderCell class="px-2 !py-1">Jenis Tugas</TableHeaderCell>
                                    <TableHeaderCell class="px-2 !py-1">Divisi</TableHeaderCell>
                                    <TableHeaderCell class="px-2 !py-1">Tanggal Dibuat</TableHeaderCell>
                                    <TableHeaderCell class="px-2 !py-1">Tanggal Selesai</TableHeaderCell>
                                </TableRow>
                            </TableHead>
                            <TableBody v-if="histories.length > 0">
                                <TableRow v-for="value in histories" :key="value.id">
                                    <TableCell class="px-2 !py-1">{{ value.nama_tugas }}</TableCell>
                                    <TableCell class="px-2 !py-1">{{ value.jenis_tugas }}</TableCell>
                                    <TableCell class="px-2 !py-1">{{ value.divisi }}</TableCell>
                                    <TableCell class="px-2 !py-1">{{ value.tanggal_dibuat }}</TableCell>
                                    <TableCell class="px-2 !py-1">{{ value.tanggal_selesai }}</TableCell>
                                </TableRow>
                            </TableBody>
                            <TableBody v-else>
                                <TableRow>
                                    <TableCell class="px-2 !py-20 text-center font-black" colspan="5">Tidak ada data histori tugas.</TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </CardContent>
            </Card>

            <div v-if="showForm" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30">
                <div class="w-96 rounded bg-white p-6 shadow">
                    <h2 class="mb-4 text-lg font-semibold">Export Laporan</h2>

                    <div class="mb-4">
                        <label class="mb-1 block">Format File</label>
                        <select v-model="form.format" class="w-full rounded border border-gray-300 px-3 py-2">
                            <option value="xlsx">Excel (.xlsx)</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="mb-1 block">Tanggal Mulai</label>
                        <input type="date" v-model="form.tanggal_mulai" class="w-full rounded border border-gray-300 px-3 py-2" />
                    </div>

                    <div class="mb-4">
                        <label class="mb-1 block">Tanggal Akhir</label>
                        <input type="date" v-model="form.tanggal_akhir" class="w-full rounded border border-gray-300 px-3 py-2" />
                    </div>

                    <!-- Info box dinamis berdasarkan hasil filter -->
                    <div
                        v-if="hasFilteredData"
                        class="mb-4 bg-blue-50 p-3 text-sm text-blue-800 border border-blue-200 rounded"
                    >
                        <p>
                            <span v-if="!form.tanggal_mulai && !form.tanggal_akhir">
                                Kosongkan tanggal untuk export semua data.
                            </span>
                            <span v-else>
                                Ditemukan <strong>{{ filteredPreview.length }} data</strong> yang sesuai filter.
                            </span>
                        </p>
                    </div>

                    <!-- Error box kalau gak ada data -->
                    <div
                        v-else
                        class="mb-4 bg-red-50 p-3 text-sm text-red-800 border border-red-200 rounded"
                    >
                        <p class="font-semibold">⚠️ Data tidak ditemukan!</p>
                        <p class="mt-1">Tidak ada data tugas yang selesai pada rentang tanggal yang dipilih.</p>
                    </div>

                    <div class="mt-4 flex justify-end gap-2">
                        <Button variant="secondary" @click="closeForm">Batal</Button>
                        <Button
                            @click="submitForm"
                            :disabled="isExporting || !hasFilteredData"
                            class="bg-green-500 text-white hover:bg-green-600 disabled:bg-gray-400 disabled:cursor-not-allowed"
                        >
                            <LoaderCircle v-if="isExporting" class="h-4 w-4 animate-spin" />
                            Export
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
