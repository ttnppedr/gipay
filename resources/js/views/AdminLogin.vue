<template>
  <div class="admin-login">
    <form class="admin-login__form">
      <section class="hero">
        <div class="hero-body">
          <div class="container">
            <h1 class="title">
              登入
            </h1>
            <h2 class="subtitle">
              Enjoy Life!
            </h2>
          </div>
        </div>
      </section>
      <div class="field">
        <label class="label" for="adminUsername">帳號</label>
        <div class="control">
          <input
            id="adminUsername"
            class="input"
            type="text"
            v-model="email"
            placeholder="abc@gmail.com"
          />
        </div>
      </div>

      <div class="field">
        <label class="label" for="adminPassWord">密碼</label>
        <div class="control">
          <input
            id="adminPassWord"
            class="input"
            type="password"
            v-model="password"
          />
        </div>
      </div>
      <div class="field is-grouped is-grouped-right">
        <div class="control">
          <button class="button is-primary" @click.prevent="login">
            Login
          </button>
        </div>
        <div class="control">
          <button class="button is-light" type="reset">Reset</button>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
  export default {
    data: function() {
      return {
        email: '',
        password: ''
      };
    },
    methods: {
      login: function(event) {
        axios
          .post('https://gipay.xyz/api/login', {
            email: this.email,
            password: this.password
          })
          .then(function(response) {
            window.$cookies.set('token', response.data.token);
            window.location.href = '/home';
          })
          .catch(function(error) {
            console.log(error);
          });
      }
    }
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
