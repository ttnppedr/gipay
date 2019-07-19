<template>
  <div class="modal is-active">
    <div class="modal-background" @click="$emit('closeModal')"></div>
    <div class="modal-card">
      <header class="modal-card-head">
        <p class="modal-card-title">使用者基本資訊</p>
        <button class="delete" aria-label="close" @click="$emit('closeModal')"></button>
      </header>
      <section class="modal-card-body">使用者的詳細帳務資訊</section>
    </div>
  </div>
</template>

<script>
export default {
  name: "Lightbox",
  methods: {
    closeModal() {}
  }
};
</script>

<style lang="scss" scoped>
</style>
