<template>
    <div>
        <div class="flex items-center justify-between mb-3">
            <h1 class="text-3xl font-semibold">Products</h1>
            <button
                type="button"
                @click="showAddNewModal()"
                class="py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                Add new Product
            </button>
        </div>
        <products-table @clickEdit="editProduct"></products-table>
        <product-modal
            v-model="showProductModal"
            :product="productModel"
            @close="onModalClose"
        ></product-modal>
    </div>
</template>

<script setup>
import { computed, ref } from "vue";
import { useStore } from "vuex";

import ProductModal from "./ProductModal.vue";
import ProductsTable from "./ProductsTable.vue";

const store = useStore();

const DEFAULT_PRODUCT = {
    id: "",
    title: "",
    description: "",
    image: "",
    price: "",
};

const products = computed(() => store.state.products);

const productModel = ref({ ...DEFAULT_PRODUCT });
const showProductModal = ref(false);

function showAddNewModal() {
    showProductModal.value = true;
}

function editProduct(p) {
    store.dispatch("getProduct", p.id).then(({ data }) => {
        productModel.value = data;
        showAddNewModal();
    });
}

function onModalClose() {
    productModel.value = { ...DEFAULT_PRODUCT };
}
</script>
