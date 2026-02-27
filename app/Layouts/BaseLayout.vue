<template>
    <nav class="fixed top-0 z-50 w-full border-b bg-gray-800 border-gray-700">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start rtl:justify-end">
                    <button
                        data-drawer-target="logo-sidebar"
                        data-drawer-toggle="logo-sidebar"
                        aria-controls="logo-sidebar"
                        type="button"
                        class="inline-flex items-center p-2 text-sm text-gray-400 rounded-lg sm:hidden hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600"
                    >
                        <span class="sr-only">Open sidebar</span>
                        <svg
                            class="w-6 h-6"
                            aria-hidden="true"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                clip-rule="evenodd"
                                fill-rule="evenodd"
                                d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"
                            ></path>
                        </svg>
                    </button>
                    <Link
                        href="/"
                        class="flex ms-2 md:me-24"
                    >
                        <img
                            src="https://spiritvale.info/content/fav.png"
                            class="h-8 me-3 rounded-sm"
                            alt="SpiritVale Logo"
                        />
                        <span class="inline self-center text-xl font-semibold sm:text-2xl whitespace-nowrap text-white"
                            >SpiritVale Info</span
                        >
                    </Link>
                    <a
                        href="https://spiritvale.info/wiki/Main_Page"
                        target="_blank"
                        class="text-white"
                    >
                        <span class="ms-3">Wiki</span>
                    </a>
                </div>
                <!--
                <div class="flex items-center">
                    <div class="flex items-center ms-3">
                        <div>
                            <button type="button" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600" aria-expanded="false" data-dropdown-toggle="dropdown-user">
                                <span class="sr-only">Open user menu</span>
                                <img class="w-8 h-8 rounded-full" src="https://flowbite.com/docs/images/people/profile-picture-5.jpg" alt="user photo">
                            </button>
                        </div>
                        <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-sm shadow-sm dark:bg-gray-700 dark:divide-gray-600" id="dropdown-user">
                            <div class="px-4 py-3" role="none">
                                <p class="text-sm text-gray-900 dark:text-white" role="none">
                                    Neil Sims
                                </p>
                                <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-300" role="none">
                                    neil.sims@flowbite.com
                                </p>
                            </div>
                            <ul class="py-1" role="none">
                                <li>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">Dashboard</a>
                                </li>
                                <li>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">Settings</a>
                                </li>
                                <li>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">Earnings</a>
                                </li>
                                <li>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">Sign out</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                -->
            </div>
        </div>
    </nav>

    <aside
        id="logo-sidebar"
        class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-gray-800 border-r border-gray-700 sm:translate-x-0"
        aria-label="Sidebar"
    >
        <div class="h-full px-3 pb-4 overflow-y-auto bg-gray-800">
            <ul class="space-y-2 font-medium">
                <li v-for="item in navigation">
                    <a
                        :href="item.href"
                        class="flex items-center p-2 text-white rounded-lg hover:bg-gray-700 group"
                    >
                        <!--
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 21">
                            <path d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z"/>
                            <path d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z"/>
                        </svg>
                        -->
                        <span class="ms-3">{{ item.name }}</span>
                    </a>
                </li>

                <li class="mt-4">
                    <a
                        href="/game/server-status"
                        class="flex items-center p-2 text-white rounded-lg hover:bg-gray-700 group"
                    >
                        <span class="ms-3">Server Status</span>
                    </a>
                    <a
                        href="/game/statistics"
                        class="flex items-center p-2 text-white rounded-lg hover:bg-gray-700 group"
                    >
                        <span class="ms-3">Game Statistics</span>
                    </a>
                    <a
                        href="/game/leaderboards"
                        class="flex items-center p-2 text-white rounded-lg hover:bg-gray-700 group"
                    >
                        <span class="ms-3">Leaderboards</span>
                    </a>
                    <a
                        href="/game/vending"
                        class="flex items-center p-2 text-white rounded-lg hover:bg-gray-700 group"
                    >
                        <span class="ms-3">Vending</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>

    <div class="p-4 sm:ml-64">
        <div class="p4 mt-14">
            <slot></slot>
        </div>
    </div>

    <div
        style="display: none"
        id="tooltip-container-lazy"
    >
        <TooltipCard
            :card="tooltipStore.data"
            v-if="tooltipStore.type === 'card'"
        ></TooltipCard>
        <TooltipGem
            :gem="tooltipStore.data"
            v-if="tooltipStore.type === 'gem'"
        ></TooltipGem>
        <TooltipConsumable
            :consumable="tooltipStore.data"
            v-if="tooltipStore.type === 'consumable'"
        ></TooltipConsumable>
        <TooltipEquipment
            :equipment="tooltipStore.data"
            v-if="tooltipStore.type === 'equipment'"
        ></TooltipEquipment>
        <TooltipMaterial
            :material="tooltipStore.data"
            v-if="tooltipStore.type === 'material'"
        ></TooltipMaterial>
        <TooltipMonster
            :monster="tooltipStore.data"
            v-if="tooltipStore.type === 'monster'"
        ></TooltipMonster>
        <TooltipSkill
            :skill="tooltipStore.data"
            v-if="tooltipStore.type === 'skill'"
        ></TooltipSkill>
    </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { initFlowbite } from 'flowbite';

import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useTooltipStore } from '@/stores/tooltip';
import TooltipCard from '@/MyComponents/TooltipCard.vue';
import TooltipMonster from '@/MyComponents/TooltipMonster.vue';
import TooltipConsumable from '@/MyComponents/TooltipConsumable.vue';
import TooltipEquipment from '@/MyComponents/TooltipEquipment.vue';
import TooltipMaterial from '@/MyComponents/TooltipMaterial.vue';
import TooltipSkill from '@/MyComponents/TooltipSkill.vue';
import TooltipGem from '@/MyComponents/TooltipGem.vue';

const navigation = ref([
    { name: 'Home', href: '/' },
    { name: 'Build Simulator', href: '/build-simulator' },
    { name: 'World Map', href: '/world-map' },
    { name: 'Maps', href: '/maps' },
    { name: 'Monsters', href: '/monsters' },
    { name: 'Cards', href: '/cards' },
    { name: 'Gems', href: '/gems' },
    { name: 'Equipment', href: '/equipment' },
    { name: 'Artifacts', href: '/artifacts' },
    { name: 'Videos', href: '/videos' },
    { name: 'Guides', href: '/guides' },
    { name: 'FAQ', href: '/faq' },
]);

const tooltipStore = useTooltipStore();

onMounted(() => {
    initFlowbite();
});
</script>

<style scoped></style>
