<script setup>
import { ref, watch, onMounted } from 'vue';

const props = defineProps({
    width: { type: Number, required: true },
    height: { type: Number, required: true },
    backgroundColor: { type: String, default: '#ffffff' },
    contributions: { type: Array, default: () => [] },
});

const canvasEl = ref(null);

function render() {
    if (!canvasEl.value) return;
    const ctx = canvasEl.value.getContext('2d');
    ctx.fillStyle = props.backgroundColor;
    ctx.fillRect(0, 0, props.width, props.height);

    const ordered = [...props.contributions].sort((a, b) => a.layer_order - b.layer_order);
    for (const contribution of ordered) {
        for (const stroke of contribution.strokes) {
            if (stroke.points.length < 2) continue;
            ctx.strokeStyle = stroke.color;
            ctx.lineWidth = stroke.width;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            ctx.beginPath();
            ctx.moveTo(stroke.points[0].x, stroke.points[0].y);
            for (const point of stroke.points.slice(1)) {
                ctx.lineTo(point.x, point.y);
            }
            ctx.stroke();
        }
    }
}

watch(() => props.contributions, render, { deep: true });
onMounted(render);
</script>

<template>
    <canvas ref="canvasEl" :width="width" :height="height" class="max-w-full border-4 border-ink" style="height: auto" />
</template>
