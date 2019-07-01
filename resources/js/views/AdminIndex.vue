<style>
</style>

<template>
  <div v-if="token"></div>
</template>

<script>
export default {
  data: function () {
    return {
      token: window.$cookies.get("token")
    };
  },
  created () {
    if (this.token === null) {
      window.location.replace("/admin-login");
    }
  }
};
</script>
