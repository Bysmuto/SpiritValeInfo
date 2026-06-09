<template>
    <Head title="Equipment - SpiritVale Info"></Head>
    <BaseLayout>
        <h1 class="page-title">Equipment</h1>

        <div class="filter-row flex items-center gap-4">
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium">Type:</label>
                <div class="relative w-48">
                    <Input
                        placeholder="Search type..."
                        v-model="typeDD.search"
                        autocomplete="off"
                        spellcheck="false"
                        @focus="typeDD.focus()"
                        @input="typeDD.show = true"
                        @blur="typeDD.blur()"
                        @keydown.enter.prevent="typeDD.enter($event)"
                    ></Input>

                    <div
                        v-if="typeDD.show"
                        class="absolute top-full left-0 z-50 w-full mt-1 bg-[#1e293b] border border-gray-600 rounded-md shadow-lg max-h-60 overflow-y-auto"
                    >
                        <div
                            v-for="opt in typeDD.filtered"
                            :key="opt.value"
                            @mousedown.prevent="typeDD.select(opt)"
                            class="px-3 py-2 cursor-pointer hover:bg-gray-700 text-sm text-gray-100"
                        >
                            {{ opt.label }}
                        </div>
                        <div v-if="typeDD.filtered.length === 0" class="px-3 py-2 text-sm text-gray-400">
                            No matches found
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium">Sort by:</label>
                <div class="relative w-48">
                    <Input
                        placeholder="Search sort..."
                        v-model="sortDD.search"
                        autocomplete="off"
                        spellcheck="false"
                        @focus="sortDD.focus()"
                        @input="sortDD.show = true"
                        @blur="sortDD.blur()"
                        @keydown.enter.prevent="sortDD.enter($event)"
                    ></Input>

                    <div
                        v-if="sortDD.show"
                        class="absolute top-full left-0 z-50 w-full mt-1 bg-[#1e293b] border border-gray-600 rounded-md shadow-lg max-h-60 overflow-y-auto"
                    >
                        <div
                            v-for="opt in sortDD.filtered"
                            :key="opt.value"
                            @mousedown.prevent="sortDD.select(opt)"
                            class="px-3 py-2 cursor-pointer hover:bg-gray-700 text-sm text-gray-100"
                        >
                            {{ opt.label }}
                        </div>
                        <div v-if="sortDD.filtered.length === 0" class="px-3 py-2 text-sm text-gray-400">
                            No matches found
                        </div>
                    </div>
                </div>
            </div>
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
import { computed, ref, reactive, Ref } from 'vue';
import { Input } from '@/components/ui/input';
import MyBadge from '@/components/shared/MyBadge.vue';
import { Equipment } from '@/types';

const props = defineProps<{
    type: string;
    typeOptions: string[];
    search: string;
    equipmentList: Array<Equipment>;
}>();

const equipmentList = ref(props.equipmentList);
equipmentList.value = equipmentList.value.sort((e1, e2) => e1.DisplayName.localeCompare(e2.DisplayName));

const optionsType = [{ value: 'All', label: 'All' }];
props.typeOptions.forEach((type) => optionsType.push({ value: type, label: type }));

const optionsSort = [
    { value: 'Name', label: 'Alphabetical' },
    { value: 'LowestLv', label: 'Monster Lvl' },
    { value: 'Agi', label: 'Agi' },
    { value: 'AllResist', label: 'All Resist' },
    { value: 'AllStats', label: 'All Stats' },
    { value: 'Atk', label: 'Atk' },
    { value: 'AtkMult', label: 'Atk Mult' },
    { value: 'AtkSpd', label: 'Atk Spd' },
    { value: 'AutocastAttack', label: 'Autocast Attack' },
    { value: 'AutocastChance', label: 'Autocast Chance' },
    { value: 'AutocastHit', label: 'Autocast Hit' },
    { value: 'Block', label: 'Block' },
    { value: 'CastSpd', label: 'Cast Spd' },
    { value: 'Chain', label: 'Chain' },
    { value: 'CooldownRecovery', label: 'Cooldown Recovery' },
    { value: 'Crit', label: 'Crit' },
    { value: 'CritDamage', label: 'Crit Damage' },
    { value: 'CritMult', label: 'Crit Mult' },
    { value: 'DamageCloseRange', label: 'Damage Close Range' },
    { value: 'DamageElement', label: 'Damage Element' },
    { value: 'DamageFromElement', label: 'Damage From Element' },
    { value: 'DamageFromMagic', label: 'Damage From Magic' },
    { value: 'DamageFromMelee', label: 'Damage From Melee' },
    { value: 'DamageFromRanged', label: 'Damage From Ranged' },
    { value: 'DamageMagic', label: 'Damage Magic' },
    { value: 'DamageMelee', label: 'Damage Melee' },
    { value: 'DamageRanged', label: 'Damage Ranged' },
    { value: 'DamageStatus', label: 'Damage Status' },
    { value: 'DamageToElement', label: 'Damage To Element' },
    { value: 'Def', label: 'Def' },
    { value: 'DefFlat', label: 'Def Flat' },
    { value: 'DefMult', label: 'Def Mult' },
    { value: 'Detector', label: 'Detector' },
    { value: 'Dex', label: 'Dex' },
    { value: 'DoubleAttack', label: 'Double Attack' },
    { value: 'ElementArmor', label: 'Element Armor' },
    { value: 'ElementResist', label: 'Element Resist' },
    { value: 'ElementWeapon', label: 'Element Weapon' },
    { value: 'Flee', label: 'Flee' },
    { value: 'FleeMult', label: 'Flee Mult' },
    { value: 'GrantSkill', label: 'Grant Skill' },
    { value: 'Healing', label: 'Healing' },
    { value: 'HealingReceived', label: 'Healing Received' },
    { value: 'Hit', label: 'Hit' },
    { value: 'HitMult', label: 'Hit Mult' },
    { value: 'Hp', label: 'Hp' },
    { value: 'HpMult', label: 'Hp Mult' },
    { value: 'HpRegen', label: 'Hp Regen' },
    { value: 'HpRegenMax', label: 'Hp Regen Max' },
    { value: 'HpRegenMult', label: 'Hp Regen Mult' },
    { value: 'Int', label: 'Int' },
    { value: 'Leech', label: 'Leech' },
    { value: 'LeechKill', label: 'Leech Kill' },
    { value: 'LeechKillMp', label: 'Leech Kill Mp' },
    { value: 'LeechMp', label: 'Leech Mp' },
    { value: 'Luk', label: 'Luk' },
    { value: 'Matk', label: 'Matk' },
    { value: 'MatkMult', label: 'Matk Mult' },
    { value: 'MatkPerStr', label: 'Matk Per Str' },
    { value: 'Mdef', label: 'Mdef' },
    { value: 'MdefFlat', label: 'Mdef Flat' },
    { value: 'MdefMult', label: 'Mdef Mult' },
    { value: 'MoveSpd', label: 'Move Spd' },
    { value: 'Mp', label: 'Mp' },
    { value: 'MpCost', label: 'Mp Cost' },
    { value: 'MpMult', label: 'Mp Mult' },
    { value: 'MpRegen', label: 'Mp Regen' },
    { value: 'MpRegenMax', label: 'Mp Regen Max' },
    { value: 'MpRegenMult', label: 'Mp Regen Mult' },
    { value: 'NoCastCancel', label: 'No Cast Cancel' },
    { value: 'NoFlinch', label: 'No Flinch' },
    { value: 'NoKnockback', label: 'No Knockback' },
    { value: 'NoReflect', label: 'No Reflect' },
    { value: 'PerfectCloak', label: 'Perfect Cloak' },
    { value: 'PerfectDodge', label: 'Perfect Dodge' },
    { value: 'Range', label: 'Range' },
    { value: 'ReflectDamage', label: 'Reflect Damage' },
    { value: 'ReflectSpell', label: 'Reflect Spell' },
    { value: 'SiphonHp', label: 'Siphon Hp' },
    { value: 'SiphonMp', label: 'Siphon Mp' },
    { value: 'SkillArea', label: 'Skill Area' },
    { value: 'SkillCastTime', label: 'Skill Cast Time' },
    { value: 'SkillChains', label: 'Skill Chains' },
    { value: 'SkillCharges', label: 'Skill Charges' },
    { value: 'SkillCooldown', label: 'Skill Cooldown' },
    { value: 'SkillCost', label: 'Skill Cost' },
    { value: 'SkillDamage', label: 'Skill Damage' },
    { value: 'SkillDuration', label: 'Skill Duration' },
    { value: 'SkillHits', label: 'Skill Hits' },
    { value: 'SkillLevel', label: 'Skill Level' },
    { value: 'SkillPiercing', label: 'Skill Piercing' },
    { value: 'SkillRemoveKnockback', label: 'Skill Remove Knockback' },
    { value: 'SkillRemoveStatus', label: 'Skill Remove Status' },
    { value: 'Splash', label: 'Splash' },
    { value: 'StatusDuration', label: 'Status Duration' },
    { value: 'StatusImmune', label: 'Status Immune' },
    { value: 'StatusMaxStacks', label: 'Status Max Stacks' },
    { value: 'Str', label: 'Str' },
    { value: 'SummonAllStats', label: 'Summon All Stats' },
    { value: 'SummonAtkMult', label: 'Summon Atk Mult' },
    { value: 'SummonAtkSpd', label: 'Summon Atk Spd' },
    { value: 'SummonHealing', label: 'Summon Healing' },
    { value: 'SummonHpMult', label: 'Summon Hp Mult' },
    { value: 'SummonMatkMult', label: 'Summon Matk Mult' },
    { value: 'SummonResist', label: 'Summon Resist' },
    { value: 'Vit', label: 'Vit' },
    { value: 'WeightLimit', label: 'Weight Limit' }
];

const urlParams = new URLSearchParams(window.location.search);
const filterText = ref(props.search);
const sortOption = ref(urlParams.get('sort') || 'Name');
const filterType = ref(props.type || 'All');

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

const createDropdownState = (options: {label: string, value: string}[], modelRef: Ref<string>) => {
    return reactive({
        show: false,
        search: options.find(opt => opt.value === modelRef.value)?.label || options[0].label,
        get filtered() {
            const query = this.search.toLowerCase().trim();
            if (!query) return options;
            return options.filter(opt =>
                opt.label.toLowerCase().includes(query) || opt.value.toLowerCase().includes(query)
            );
        },
        select(opt: {label: string, value: string}) {
            modelRef.value = opt.value;
            this.search = opt.label;
            this.show = false;
            updateUrl();
        },
        blur() {
            this.show = false;
            const valid = options.find(opt => opt.value === modelRef.value);
            if (valid) this.search = valid.label;
        },
        enter(e: Event) {
            if (this.filtered.length > 0) {
                this.select(this.filtered[0]);
                (e.target as HTMLInputElement).blur();
            }
        },
        focus() {
            this.show = true;
            this.search = '';
        }
    });
};

const typeDD = createDropdownState(optionsType, filterType);
const sortDD = createDropdownState(optionsSort, sortOption);

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
</script>

<style scoped></style>
