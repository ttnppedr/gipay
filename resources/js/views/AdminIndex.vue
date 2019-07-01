<style>
</style>

<template>
  <div v-if="token">
    <div>
      <span>
        <a href="/admin-users">Users</a>
      </span>
    </div>
    <div>
      <span>
        <a href="/admin-orders">Orders</a>
      </span>
    </div>
  </div>
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
