<script setup>
import { ref, computed } from 'vue'
import { useStore } from 'vuex';

const store = useStore();

const test = computed(() => store.state.test);

defineProps({
  msg: String,
})

const count = ref(0)
</script>

<template>
    <div>

        <h1 class="bg-purple-500">{{ msg }}</h1>

        {{  test }}


    </div>
</template>

<style scoped>
.read-the-docs {
  color: #888;
}
</style>
