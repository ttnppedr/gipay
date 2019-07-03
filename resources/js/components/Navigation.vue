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
      <div></div>
      <div class="navbar-end">
        <div class="navbar-item">
          <div class="admin-info">
            <div class="admin-info__avatar">
              <img :src="adminInfo.avatar" />
            </div>
            <div class="admin-info__name">{{ adminInfo.name }}</div>
          </div>
          <div class="buttons" @click="logout">
            <a class="button is-light">{{ loginStatusMsg }}</a>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
  import logo from '../../images/logo.svg';
  export default {
    data: function() {
      return {
        token: window.$cookies.get('token'),
        logo: logo,
        adminInfo: ''
      };
    },
    created() {
      this.getAdminInfo();
    },
    computed: {
      loginStatusMsg() {
        return this.token ? '登出' : '登入';
      }
    },
    methods: {
      async getAdminInfo() {
        // 初始化，登入中才拿管理者資訊
        if (!this.token && window.location.pathname !== '/admin-login') {
          window.location.replace('/admin-login');
        }
        try {
          const adminInfo = await axios.get('https://gipay.xyz/api/info', {
            headers: {
              Authorization: `Bearer ${this.token}`,
              'Content-Type': 'application/json',
              Accept: 'application/json'
            }
          });

          if (adminInfo.data.message === 'token error') {
            this.logout();
            return;
          }

          this.adminInfo = adminInfo.data;
        } catch (e) {
          console.log('error', e);
        }
      },
      logout() {
        if (!this.token) return;
        window.$cookies.remove('token');
        window.location.replace('/admin-login');
      }
    }
  };
</script>

<style lang="scss" scoped>
  .admin-info {
    display: flex;
    justify-content: center;
    align-items: center;

    &__avatar {
      font-size: 0;
      img {
        border-radius: 50%;
      }
    }

    &__name {
      margin-left: 1em;
      margin-right: 1em;
    }
  }
</style>
