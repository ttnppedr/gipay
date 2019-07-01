<style>
</style>

<template>
  <!-- <div v-if="token">
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
  </div>-->
  <nav class="navbar" role="navigation" aria-label="main navigation" v-if="token">
    <div class="navbar-brand">
      <a class="navbar-item" href="/admin-index">
        <img src="https://bulma.io/images/bulma-logo.png" width="112" height="28" />
      </a>

      <a
        role="button"
        class="navbar-burger burger"
        aria-label="menu"
        aria-expanded="false"
        data-target="navbarBasicExample"
      >
        <span aria-hidden="true"></span>
        <span aria-hidden="true"></span>
        <span aria-hidden="true"></span>
      </a>
    </div>

    <div class="navbar-menu">
      <div class="navbar-start">
        <a href="/admin-users" class="navbar-item">查看會員</a>
        <a href="/admin-orders" class="navbar-item">帳務紀錄</a>
      </div>

      <div class="navbar-end">
        <div class="navbar-item">
          <div class="buttons">
            <a class="button is-light">Log in</a>
          </div>
        </div>
      </div>
    </div>
  </nav>
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
