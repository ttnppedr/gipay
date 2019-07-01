<style>
</style>

<template>
  <div v-if="token">
    <div>
      <ul>
        <li>
          ID,
          類型,
          金額,
          到,
          從
        </li>
        <li v-for="order in orders">
          {{ order.id }},
          <span v-if="order.type === 1">存款,</span>
          <span v-if="order.type === 2">取款,</span>
          <span v-if="order.type === 3">轉帳,</span>
          ${{ order.amount }},
          {{ order.to.name }},
          <span
            v-if="order.type === 3"
          >{{ order.from.name }}</span>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
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
    axios
      .get("https://gipay.xyz/api/admin/orders", {
        headers: {
          Authorization: `Bearer ${this.token}`,
          "Content-Type": "application/json",
          Accept: "application/json"
        }
      })
      .then(response => {
        this.orders = response.data.data;
      })
      .catch(function(error) {
        console.log(error);
      });
  },
  methods: {
    setBlock(userId, userBlocked, userName) {
      let url =
        userBlocked === 0
          ? `/api/admin/unblock/user/${userId}`
          : `/api/admin/block/user/${userId}`;
      console.log(url);
      axios
        .patch(
          url,
          {},
          {
            headers: {
              Authorization: `Bearer ${this.token}`,
              "Content-Type": "application/json",
              Accept: "application/json"
            }
          }
        )
        .then(response => {
          let msg = userName;
          msg += userBlocked === 0 ? " 已解除凍結" : " 已凍結";
          alert(msg);
        })
        .catch(function(error) {
          alert("操作失敗");
        });
    }
  }
};
</script>
