<style>
</style>

<template>
  <nav class="navbar" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
      <a class="navbar-item" href="/home">
        <img :src="logo" />
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

    <div class="navbar-menu" v-if="token">
      <div class="navbar-start">
        <a href="/admin-users" class="navbar-item">查看會員</a>
        <a href="/admin-orders" class="navbar-item">帳務紀錄</a>
      </div>

      <div class="navbar-end">
        <div class="navbar-item">
          <div class="buttons" @click="logout">
            <a class="button is-light">{{ loginStatusMsg }}</a>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
import logo from "../../images/logo.svg"
export default {
  data: function () {
    return {
      token: window.$cookies.get("token"),
      logo: logo
    };
  },
  computed: {
    loginStatusMsg () {
      return this.token ? "登出" : "登入";
    }
  },
  methods: {
    logout () {
      if (!this.token) return;
      window.$cookies.remove("token");
      window.location.replace("/admin-login");
    }
  }
};
</script>
