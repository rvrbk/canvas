<script setup>
defineProps({
    size: { type: Number, default: 28 },
});
</script>

<template>
    <svg :width="size" :height="size" viewBox="0 0 32 32" fill="none" aria-hidden="true">
        <circle cx="12" cy="12" r="9" fill="#ff3d3d" />
        <circle cx="20" cy="12" r="9" fill="#2b6bff" />
        <circle cx="16" cy="20" r="9" fill="#ffcc00" />
    </svg>
</template>
