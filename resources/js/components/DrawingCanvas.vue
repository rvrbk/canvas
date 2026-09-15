<script setup>
import { ref, computed, onMounted } from 'vue';

const props = defineProps({
    width: { type: Number, required: true },
    height: { type: Number, required: true },
    backgroundColor: { type: String, default: '#ffffff' },
});

const emit = defineEmits(['change']);

const canvasEl = ref(null);
const color = ref('#1f2937');
const brushWidth = ref(6);
const strokes = ref([]);
const currentStroke = ref(null);
const isDrawing = ref(false);

const palette = ['#1f2937', '#ef4444', '#f59e0b', '#22c55e', '#3b82f6', '#a855f7', '#ec4899'];

const hasStrokes = computed(() => strokes.value.length > 0);

function context() {
    return canvasEl.value.getContext('2d');
}

function redraw() {
    const ctx = context();
    ctx.fillStyle = props.backgroundColor;
    ctx.fillRect(0, 0, props.width, props.height);
    for (const stroke of strokes.value) {
        drawStroke(ctx, stroke);
    }
}

function drawStroke(ctx, stroke) {
    if (stroke.points.length < 2) return;
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

function pointerPosition(event) {
    const rect = canvasEl.value.getBoundingClientRect();
    const scaleX = props.width / rect.width;
    const scaleY = props.height / rect.height;
    return {
        x: Math.round((event.clientX - rect.left) * scaleX),
        y: Math.round((event.clientY - rect.top) * scaleY),
    };
}

function onPointerDown(event) {
    isDrawing.value = true;
    currentStroke.value = {
        color: color.value,
        width: brushWidth.value,
        points: [pointerPosition(event)],
    };
    canvasEl.value.setPointerCapture(event.pointerId);
}

function onPointerMove(event) {
    if (!isDrawing.value) return;
    currentStroke.value.points.push(pointerPosition(event));
    drawStroke(context(), currentStroke.value);
}

function onPointerUp() {
    if (!isDrawing.value) return;
    isDrawing.value = false;
    if (currentStroke.value.points.length >= 2) {
        strokes.value.push(currentStroke.value);
        emit('change', strokes.value);
    }
    currentStroke.value = null;
}

function undo() {
    strokes.value.pop();
    redraw();
    emit('change', strokes.value);
}

function clear() {
    strokes.value = [];
    redraw();
    emit('change', strokes.value);
}

defineExpose({ strokes, hasStrokes, clear });

onMounted(redraw);
</script>

<template>
    <div class="flex flex-col items-center gap-3">
        <div class="flex flex-wrap items-center justify-center gap-4">
            <div class="flex items-center gap-1.5">
                <button
                    v-for="swatch in palette"
                    :key="swatch"
                    type="button"
                    class="h-6 w-6 border-2 transition hover:scale-125"
                    :class="swatch === color ? 'scale-110 border-ink' : 'border-ink/30'"
                    :style="{ backgroundColor: swatch }"
                    @click="color = swatch"
                />
                <input v-model="color" type="color" class="h-7 w-7 cursor-pointer border-0 bg-transparent p-0" title="Custom color" />
            </div>
            <label class="flex items-center gap-2 text-xs font-bold tracking-wide uppercase">
                Brush
                <input v-model.number="brushWidth" type="range" min="2" max="30" />
            </label>
            <div class="flex gap-2">
                <button
                    type="button"
                    :disabled="!hasStrokes"
                    class="border-2 border-ink px-3 py-1 text-xs font-bold tracking-wide uppercase transition hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-40 disabled:hover:translate-y-0"
                    @click="undo"
                >
                    Undo
                </button>
                <button
                    type="button"
                    :disabled="!hasStrokes"
                    class="border-2 border-ink px-3 py-1 text-xs font-bold tracking-wide uppercase transition hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-40 disabled:hover:translate-y-0"
                    @click="clear"
                >
                    Clear
                </button>
            </div>
        </div>
        <canvas
            ref="canvasEl"
            :width="width"
            :height="height"
            class="max-w-full touch-none border-4 border-ink [cursor:crosshair]"
            style="height: auto"
            @pointerdown="onPointerDown"
            @pointermove="onPointerMove"
            @pointerup="onPointerUp"
            @pointerleave="onPointerUp"
        />
    </div>
</template>
