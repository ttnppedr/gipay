<style>
</style>

<template>
  <section v-if="token" class="gipay-container">
    <table class="table is-striped is-hoverable is-fullwidth">
      <thead>
        <tr data-id>
          <th align="right">餘額</th>
          <th align="center" class="table-align-center">類型</th>
          <th align="center" class="table-align-center">動作名稱</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="order in orders" :key="order.id">
          <th align="right">$ {{ order.amount }}</th>
          <td align="center">
            <span v-if="order.type === 1" class="tag is-success">存款</span>
            <span v-if="order.type === 2" class="tag is-info">提款</span>
            <span v-if="order.type === 3" class="tag is-warning">轉帳</span>
          </td>
          <td align>
            <span v-if="order.type == 3" class="table__flex">
              <span class="username">{{ order.from.name }}</span>
              <span>轉帳給</span>
              <span class="username">{{ order.to.name }}</span>
              。
            </span>
            <span v-else>
              <span class="username">{{ order.to.name }}</span>
              <span v-if="order.type === 1">存款</span>
              <span v-if="order.type === 2">提款</span>
              <span v-if="order.type === 3">轉帳</span>
              。
            </span>
          </td>
        </tr>
      </tbody>
    </table>
  </section>
</template>

<script>
import API from "../utilities/API.js";

export default {
  data: function() {
    return {
      token: window.$cookies.get("token"),
      orders: []
    };
  },
  created() {
    if (this.token === null) {
      window.location.replace("/admin-login");
    }
  },
  mounted() {
    API.orders
      .get(this.token)
      .then(response => {
        this.orders = response.data.data;
      })
      .catch(function(error) {
        console.log(error);
      });
  }
};
</script>

<style lang="scss" scoped>
.table__flex {
  display: flex;
  align-items: center;

  :nth-child(2) {
    margin: 0 7px;
  }
}
.username {
  color: #6eb586;
  font-weight: 700;
}
</style>

