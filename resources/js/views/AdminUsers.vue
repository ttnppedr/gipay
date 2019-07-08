<template>
  <section v-if="token" class="gipay-container">
    <table class="table is-striped is-hoverable is-fullwidth">
      <thead>
        <tr data-id>
          <th>帳號</th>
          <th>餘額</th>
          <th>E-mail</th>
          <th>凍結</th>
          <th>錯誤次數</th>
          <th>Admin</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id">
          <td>{{ user.name }}</td>
          <td>$ {{ user.balance }}</td>
          <td>{{ user.email }}</td>
          <td>
            <input
              type="checkbox"
              v-model="user.blocked"
              v-bind:true-value="1"
              v-bind:false-value="0"
              @change="setBlock(user.id, user.blocked, user.name)"
            />
          </td>
          <td>{{ user.password_errors }}</td>
          <td>{{ user.admin }}</td>
        </tr>
      </tbody>
    </table>
  </section>
</template>

<script>
export default {
  data: function() {
    return {
      token: window.$cookies.get("token"),
      users: []
    };
  },
  created() {
    if (this.token === null) {
      window.location.replace("/admin-login");
    }
  },
  mounted() {
    axios
      .get("https://gipay.xyz/api/admin/users", {
        headers: {
          Authorization: `Bearer ${this.token}`,
          "Content-Type": "application/json",
          Accept: "application/json"
        }
      })
      .then(response => {
        this.users = response.data.data;
      })
      .catch(function(error) {
        console.log(error);
      });
  },
  methods: {
    setBlock(userId, userBlocked, userName) {
      let url =
        userBlocked === 0
          ? `https://gipay.xyz/api/admin/unblock/user/${userId}`
          : `https://gipay.xyz/api/admin/block/user/${userId}`;
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

<style lang="scss" scoped>
.gipay-container {
  max-width: 768px;
  margin: 0 auto;
  min-height: calc(100vh - 52px);
}

table {
  border-radius: 5px;
}
</style>
