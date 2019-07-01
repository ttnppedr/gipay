<template>
  <div v-if="token" class="home"></div>
</template>

<style lang="scss">
.home {
  position: relative;
  min-height: calc(100vh - 52px);
  &::before {
    content: "";
    position: absolute;
    height: 100%;
    width: 100%;
    background: url("../../images/digital_currency.svg") no-repeat center center;
    background-size: 50%;
    opacity: 0.5;
  }
}
</style>

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
