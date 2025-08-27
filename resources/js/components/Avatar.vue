<script setup>
import {computed, ref} from "vue";

const props = defineProps({
    user: Object
})

const hasAvatarError = ref(false);

const hasAvatar = computed(() => {
    return !!props.user.avatar && !hasAvatarError.value
});

const avatarSrc = computed(() => {
    if (!hasAvatar.value) return null;

    return props.user.avatar.permalink || props.user.avatar;
})

const avatarClasses = computed(() => {
    const base = 'size-7 rounded-full [button:has(&)]:rounded-full';
    if (hasAvatar.value) {
        return base;
    } else {
        return base + ' text-white text-2xs font-medium flex flex-shrink-0 items-center justify-center bg-gradient-to-tr from-purple-500 to-red-600';
    }
})
</script>
<template>
    <template v-if="hasAvatar">
        <img :alt="user.name" :class="avatarClasses" :src="avatarSrc" @error="hasAvatarError = true"/>
    </template>
    <template v-else>
        <div :aria-label="user.name" :class="avatarClasses">
            {{ user.initials || '?' }}
        </div>
    </template>
</template>
