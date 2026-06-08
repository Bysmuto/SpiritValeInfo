<template>
    <Head title="Equipment - SpiritVale Info"></Head>
    <BaseLayout>
        <h1 class="page-title">Equipment</h1>

        <div class="filter-row flex gap-2">
            <MySelect
                :options="optionsType"
                placeholder="Type"
                v-model="filterType"
                @change="updateUrl"
            ></MySelect>
            <MySelect
                :options="optionsSort"
                placeholder="Sort By"
                v-model="sortOption"
                @change="updateUrl"
            ></MySelect>
        </div>

        <div class="mb-3 w-full mt-3">
            <Input
                placeholder="Search..."
                v-model="filterText"
                class="border-gray-500/80 focus:border-blue-400 focus:ring-2 focus:ring-blue-400/30"
                @change="updateUrl"
            ></Input>
        </div>

        <div class="data-table">
            <table class="data-table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Equipment</th>
                        <th>Stats</th>
                        <th>Obtained</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="equipment in filteredEquipmentList"
                        :key="equipment.Slug"
                    >
                        <td style="width: 100px">
                            <img
                                loading="lazy"
                                style="width: 64px"
                                :src="'https://spiritvale.info/content/game/icons/item-' + equipment.icon + '.webp'"
                                :alt="equipment.DisplayName"
                            />
                        </td>
                        <td>
                            <div>
                                <Link
                                    :href="'/equipment/' + equipment.Slug"
                                    v-tippy="{ contentLazy: { type: 'equipment', slug: equipment.Slug } }"
                                    >{{ equipment.DisplayName }}</Link
                                >
                            </div>
                            <div class="my-2">
                                <MyBadge>{{ equipment.typeName }}</MyBadge>
                            </div>
                            <div class="my-2">
                                <MyBadge>{{ equipment.Slots }} Card Slots</MyBadge>
                            </div>
                        </td>
                        <td>
                            <div
                                v-for="stat in equipment.statsPrimary"
                                v-html="stat"
                            ></div>
                            <div v-if="equipment.statsPrimary.length > 0 && equipment.statsSecondary.length > 0">
                                ----------------
                            </div>
                            <div
                                v-for="stat in equipment.statsSecondary"
                                v-html="stat"
                            ></div>
                        </td>
                        <td>
                            <div v-if="equipment.drops.length > 0">
                                <div
                                    class="my-2"
                                    v-for="drop in equipment.drops"
                                    :key="drop.monster.name"
                                >
                                    Lv {{ drop.monster.level }}
                                    <Link :href="'/monsters/' + drop.monster.slug">{{ drop.monster.name }}</Link>
                                    <MyBadge
                                        color="yellow"
                                        class="ml-1"
                                        v-if="drop.monster.isBoss"
                                        >BOSS</MyBadge
                                    >
                                    <MyBadge class="ml-1">{{ drop.chance }}%</MyBadge>
                                </div>
                            </div>
                            <div v-if="equipment.crafting !== null">
                                <div v-if="equipment.crafting.map !== null">
                                    Crafted in
                                    <Link :href="'/maps/' + equipment.crafting.map.Slug || '-'">{{
                                        equipment.crafting.map.DisplayName
                                    }}</Link>
                                    (Lv {{ equipment.crafting.map.MonsterMinLevel }} -
                                    {{ equipment.crafting.map.MonsterMaxLevel }})
                                </div>
                                <div
                                    v-for="material in equipment.crafting.materials"
                                    :key="material.Slug"
                                >
                                    {{ material.count }} x
                                    <Link :href="'/materials/' + material.item.Slug">{{
                                        material.item.DisplayName
                                    }}</Link>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </BaseLayout>
</template>

<script setup lang="ts">
import BaseLayout from '@/layouts/BaseLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Input } from '@/components/ui/input';
import MyBadge from '@/components/shared/MyBadge.vue';
import MySelect from '@/components/shared/MySelect.vue';
import { Equipment } from '@/types';

const props = defineProps<{
    type: string;
    typeOptions: string[];
    search: string;
    equipmentList: Array<Equipment>;
}>();

const equipmentList = ref(props.equipmentList);
equipmentList.value = equipmentList.value.sort((e1, e2) => e1.DisplayName.localeCompare(e2.DisplayName));

const optionsType = [{ value: 'All', label: 'All (Type)' }];
props.typeOptions.forEach((type) => optionsType.push({ value: type, label: type }));

const optionsSort = [
    { value: 'Name', label: 'Alphabetical (sort)' },
    { value: 'Atk', label: 'Atk' },
    { value: 'Crit', label: 'Crit' },
    { value: 'CritDamage', label: 'Crit Damage' },
    { value: 'AtkSpd', label: 'Atk Spd' },
    { value: 'Matk', label: 'Matk' },
    { value: 'Mp', label: 'Mp' },
    { value: 'MpRegen', label: 'Mp Regen' },
    { value: 'CastSpd', label: 'Cast Spd' },
    { value: 'Hp', label: 'Hp' },
    { value: 'HpRegen', label: 'Hp Regen' },
    { value: 'Def', label: 'Def' },
    { value: 'Mdef', label: 'Mdef' },
    { value: 'Str', label: 'Str' },
    { value: 'Vit', label: 'Vit' },
    { value: 'Int', label: 'Int' },
    { value: 'Agi', label: 'Agi' },
    { value: 'Dex', label: 'Dex' },
    { value: 'Luk', label: 'Luk' },
    { value: 'MoveSpd', label: 'Move Spd' },
    { value: 'LowestLv', label: 'Monster Lvl' },
];

const urlParams = new URLSearchParams(window.location.search);
const filterType = ref(props.type);
const filterText = ref(props.search);
const sortOption = ref(urlParams.get('sort') || 'Name');



const getStatValue = (equipment: Equipment, statPrefix: string) => {
    const primary = equipment.PrimaryStats || equipment.statsPrimary || [];
    const secondary = equipment.SecondaryStats || equipment.statsSecondary || [];
    const allStats = [...primary, ...secondary];
    
    const foundStat = allStats.find((s: any) => s.Name && s.Name.startsWith(`${statPrefix}_`));
    
    return foundStat && (foundStat as any).Value ? (foundStat as any).Value.Value : 0;
};

const getLowestDropLevel = (equipment: Equipment) => {
    if (!equipment.drops || equipment.drops.length === 0) {
        return 9999;
    }
    return Math.min(...equipment.drops.map((d) => d.monster.level));
};

const filteredEquipmentList = computed(() => {
    let result = equipmentList.value;

    if (filterType.value !== 'All') {
        result = result.filter((e) => e.typeName === filterType.value);
    }

    if (filterText.value !== '') {
        const query = filterText.value.toLowerCase();
        result = result.filter((e) => {
            if (e.DisplayName.toLowerCase().includes(query)) return true;
            if (e.typeName.toLowerCase().includes(query)) return true;
            for (const key in e.statsPrimary) {
                if (e.statsPrimary[key].toLowerCase().includes(query)) return true;
            }
            for (const key in e.statsSecondary) {
                if (e.statsSecondary[key].toLowerCase().includes(query)) return true;
            }
            return false;
        });
    }

    return [...result].sort((a, b) => {
        if (sortOption.value === 'Name') {
            return a.DisplayName.localeCompare(b.DisplayName);
        }
        if (sortOption.value === 'LowestLv') {
            return getLowestDropLevel(a) - getLowestDropLevel(b);
        }
        
        return getStatValue(b, sortOption.value) - getStatValue(a, sortOption.value);
    });
});

const updateUrl = () => {
    const searchParams = new URLSearchParams();
    if (filterType.value !== 'All') searchParams.set('type', filterType.value);
    if (sortOption.value !== 'Name') searchParams.set('sort', sortOption.value);
    if (filterText.value.trim() !== '') searchParams.set('search', filterText.value.trim());
    const query = searchParams.toString();

    if (query.length > 0) {
        window.history.replaceState(null, '', '/equipment?' + query);
    } else {
        window.history.replaceState(null, '', '/equipment');
    }
};
</script>

<style scoped></style>
