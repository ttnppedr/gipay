<template>
  <div class="gipay-container">
    <form class="admin-login__form" name="loginForm">
      <section class="hero">
        <div class="hero-body">
          <div class="container">
            <h1 class="title">登入</h1>
            <h2 class="subtitle">Enjoy Life!</h2>
            <p class="help is-danger" v-show="error.isError">{{ error.errMessage }}</p>
          </div>
        </div>
      </section>
      <div class="field">
        <label class="label" for="adminUsername">帳號</label>
        <div class="control">
          <input
            id="adminUsername"
            class="input"
            v-bind:class="errorClass"
            type="text"
            ref="email"
            placeholder="abc@gmail.com"
            autocomplete="off"
            readonly
            onfocus="this.removeAttribute('readonly');"
          />
        </div>
      </div>

      <div class="field">
        <label class="label" for="adminPassWord">密碼</label>
        <div class="control">
          <input
            id="adminPassWord"
            class="input"
            v-bind:class="errorClass"
            type="password"
            ref="password"
            autocomplete="off"
          />
        </div>
      </div>
      <div class="field is-grouped is-grouped-right">
        <div class="control">
          <button class="button is-primary" @click.prevent="login">Login</button>
        </div>
        <div class="control">
          <button class="button is-light" type="reset" @="resetLoginValue">Reset</button>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import API from '../utilities/API.js';

export default {
  data: function() {
    return {
      error: {
        isError: 0,
        errMessage: '',
      },
    };
  },
  computed: {
    errorClass: function() {
      return {
        'is-danger': this.error.isError === 1,
      };
    },
    token() {
      return window.$cookies.get('token');
    },
  },
  mounted() {
    if (this.token) window.location.href = '/home';
  },
  methods: {
    login: function(event) {
      API.login
        .post(this.$refs.email.value, this.$refs.password.value)
        .then(response => {
          console.log(response.data);
          if (response.data.message) {
            this.error.isError = 1;
            this.error.errMessage = response.data.message.toUpperCase();

            return;
          }
          if (!response.data.user.admin) {
            this.error.isError = 1;
            this.error.errMessage = '您並非是後台管理者，請聯絡工程師。';
            return;
          }
          window.$cookies.set('token', response.data.token);
          window.location.href = '/home';
        })
        .catch(error => {
          console.log(error);
        });
    },
  },
};
</script>

<style lang="scss" scoped>
  .admin-login {
    background: rgba(255, 255, 255, 0.5);
    min-height: calc(100vh - 52px);
    padding-top: 45px;

    &__form {
      max-width: 400px;
      margin: 0 auto;
      background: #fff;
      padding: 0px 30px 45px;
      border-radius: 10px;
      text-align: left;
      box-shadow: 0px 12px 15px -5px #b2d0cd;

      section {
        text-align: center;
      }
    }
  }
</style>
