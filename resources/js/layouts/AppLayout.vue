<script setup lang="ts">
import SidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { toast } from 'vue-sonner';
import { watch, onMounted } from 'vue'; 
import { usePage } from '@inertiajs/vue3';
import Sidebar from '@/components/ui/sidebar/Sidebar.vue';
const page = usePage();
// onMounted(() => {
//     // 🚨 Tes 1: Ini harus muncul segera setelah halaman dimuat.
//     toast.info('TEST A: Toast Manual Berfungsi.'); 
// });
interface Props {
    breadcrumbs?: BreadcrumbItemType[];
    activeMenu?: string;
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
    activeMenu: 'dashboard',
});

watch(() => page.props.flash, (flash) => {
    // Pastikan flash object bukan null dan memiliki pesan
    if (flash && flash.success) {
        toast.success(flash.success);
    }
    if (flash && flash.error) {
        toast.error(flash.error);
    }
    if (flash && flash.message) {
        toast(flash.message);
    }
}, { deep: true });
</script>

<template>
    
    <SidebarLayout :breadcrumbs="breadcrumbs" :active-menu="activeMenu">
        
        <slot />
    </SidebarLayout >
    <Toaster position="top-right" theme="light" richColors class="fixed top-4 right-4 z-50 space-y-4 w-full max-w-xs"/>
</template>
