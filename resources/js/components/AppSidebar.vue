<script setup lang="ts">
import { usePage, Link } from '@inertiajs/vue3'
import NavFooter from '@/components/NavFooter.vue'
import NavMain from '@/components/NavMain.vue'
import NavUser from '@/components/NavUser.vue'
import { Sidebar, SidebarContent, SidebarFooter, SidebarGroup, SidebarGroupLabel, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar'
import { Collapsible, CollapsibleTrigger, CollapsibleContent } from '@/components/ui/collapsible'
import { type NavItem } from '@/types'
import { Copyright, Folder, LayoutGrid, ChevronDown, Landmark, IdCard, Settings as SettingsIcon, Shield, User,KeyRound } from 'lucide-vue-next'
import AppLogo from './AppLogo.vue'
import { computed } from 'vue'

const page = usePage()
// Pastikan user tersedia, dan ambil permissions dari props yang sudah di-share
const user = page.props.auth.user

// --- DEFINISI MENU ---

// menu utama
const mainNavItems: NavItem[] = [
  {
    title: 'DASHBOARD',
    href: '/dashboard',
    icon: LayoutGrid,
  },
]

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
]

// menu footer
const footerNavItems: NavItem[] = [
  {
    title: 'SUGA@2025',
    href: 'https://bprku.com',
    icon: Copyright,
  },
]

// --- LOGIKA PERMISSION ---

// Computed property untuk daftar menu yang sudah difilter
const filteredMasterNavItems = computed(() => {
    // Pastikan user ada dan permissions tersedia
    if (!user || !user.permissions || !Array.isArray(user.permissions)) {
        return [];
    }
    
    // Filter masterNavItems: hanya yang permissionKey-nya ada di array user.permissions
    return masterNavItems.filter(item => 
        user.permissions.includes(item.permissionKey)
    );
});


// Computed property untuk menentukan apakah header "SETTING" harus muncul
// Ini akan TRUE jika user memiliki setidaknya SATU item di filteredMasterNavItems
const canViewMasterMenu = computed(() => {
    return filteredMasterNavItems.value.length > 0;
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

      <Collapsible
        v-if="canViewMasterMenu"
        defaultOpen
        class="group/collapsible"
      >
        <SidebarGroup>
          <SidebarGroupLabel asChild>
            <CollapsibleTrigger class="flex items-center gap-2">
              <SettingsIcon class="h-4 w-4" />
              SETTING
              <ChevronDown
                class="ml-auto transition-transform group-data-[state=open]/collapsible:rotate-180"
              />
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