<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { ChevronDown, ClipboardPenLine, Copyright, IdCard, KeyRound, Landmark, LayoutGrid, ListTodo, Settings as SettingsIcon, Shield, SquareCheckBig, User } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

const page = usePage();
// Pastikan user tersedia, dan ambil permissions dari props yang sudah di-share
const user = page.props.auth.user;

// --- DEFINISI MENU ---

// menu utama
const mainNavItems: NavItem[] = [
    {
        title: 'DASHBOARD',
        href: '/dashboard',
        icon: LayoutGrid,
    },
];

// menu tugasku
const taskNavItems: NavItem[] = [
    {
        title: 'Daftar Tugasku',
        href: '/dashboard/tugasku',
        icon: SquareCheckBig,
        permissionKey: 'tugasku.view', // Kunci untuk pengecekan
    },
    {
        title: 'Kelola Tugas Rutin',
        href: '/dashboard/tugasku/routine',
        icon: ClipboardPenLine,
        permissionKey: 'tugasrutin.view', // Kunci untuk pengecekan
    },
    {
        title: 'Riwayat Tugas',
        href: '/dashboard/tugasku/history',
        icon: ListTodo,
        permissionKey: 'history.view', // Kunci untuk pengecekan
    },
];

// menu master dengan permissionKey
const masterNavItems: NavItem[] = [
    {
        title: 'Kantor',
        href: '/kantor',
        icon: Landmark,
        permissionKey: 'kantor.view', // Kunci untuk pengecekan
    },
    {
        title: 'Divisi',
        href: '/divisi',
        icon: IdCard,
        permissionKey: 'divisi.view', // Kunci untuk pengecekan
    },
    {
        title: 'Role',
        href: '/roles',
        icon: Shield,
        permissionKey: 'role.view', // Kunci untuk pengecekan
    },
    {
        title: 'Permission',
        href: '/permissions',
        icon: KeyRound,
        permissionKey: 'permission.view', // Kunci untuk pengecekan
    },
    {
        title: 'User',
        href: '/users',
        icon: User,
        permissionKey: 'user.view', // Kunci untuk pengecekan
    },
];

// menu footer
const footerNavItems: NavItem[] = [
    {
        title: 'SUGA@2025',
        href: 'https://bprku.com',
        icon: Copyright,
    },
];

// --- LOGIKA PERMISSION ---

// Computed property untuk daftar menu yang sudah difilter
const filteredMasterNavItems = computed(() => {
    // Pastikan user ada dan permissions tersedia
    if (!user || !user.permissions || !Array.isArray(user.permissions)) {
        return [];
    }

    // Filter masterNavItems: hanya yang permissionKey-nya ada di array user.permissions
    return masterNavItems.filter((item) => user.permissions.includes(item.permissionKey));
});

// Computed property untuk menentukan apakah header "SETTING" harus muncul
// Ini akan TRUE jika user memiliki setidaknya SATU item di filteredMasterNavItems
const canViewMasterMenu = computed(() => {
    return filteredMasterNavItems.value.length > 0;
});

const filteredTaskNavItems = computed(() => {
    if (!user || !user.permissions || !Array.isArray(user.permissions)) {
        return [];
    }

    return taskNavItems.filter((item) => user.permissions.includes(item.permissionKey));
});

const canViewTaskMenu = computed(() => {
    return filteredTaskNavItems.value.length > 0;
});
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />

            <Collapsible v-if="canViewTaskMenu" defaultOpen class="group/collapsible">
                <SidebarGroup>
                    <SidebarGroupLabel asChild>
                        <CollapsibleTrigger class="flex items-center gap-2">
                            <SquareCheckBig class="h-4 w-4" />
                            TUGASKU
                            <ChevronDown class="ml-auto transition-transform group-data-[state=open]/collapsible:rotate-180" />
                        </CollapsibleTrigger>
                    </SidebarGroupLabel>
                    <CollapsibleContent>
                        <NavMain :items="filteredTaskNavItems" />
                    </CollapsibleContent>
                </SidebarGroup>
            </Collapsible>

            <Collapsible v-if="canViewMasterMenu" defaultOpen class="group/collapsible">
                <SidebarGroup>
                    <SidebarGroupLabel asChild>
                        <CollapsibleTrigger class="flex items-center gap-2">
                            <SettingsIcon class="h-4 w-4" />
                            SETTING
                            <ChevronDown class="ml-auto transition-transform group-data-[state=open]/collapsible:rotate-180" />
                        </CollapsibleTrigger>
                    </SidebarGroupLabel>
                    <CollapsibleContent>
                        <NavMain :items="filteredMasterNavItems" />
                    </CollapsibleContent>
                </SidebarGroup>
            </Collapsible>
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>

    <slot />
</template>
