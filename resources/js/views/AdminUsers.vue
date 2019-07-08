<template>
  <section v-if="token" class="gipay-container">
    <table class="table is-striped is-hoverable is-fullwidth">
      <thead>
        <tr data-id>
          <th align="center" class="table-align-center">權限</th>
          <th>帳號</th>
          <th align="right">餘額</th>
          <th align="center" class="table-align-center">錯誤次數</th>
          <th align="center" class="table-align-center">動作</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id">
          <td align="center">
            <span class="tag is-info" v-if="user.admin">Admin</span>
          </td>
          <td>{{ user.name }}</td>
          <th align="right">$ {{ user.balance }}</th>
          <td align="center">{{ user.password_errors }}</td>
          <td align="center">
            <input
              type="checkbox"
              v-model="user.blocked"
              :id="user.id"
              :true-value="1"
              :false-value="0"
              @change="setBlock(user.id, user.blocked, user.name)"
            />
            <label
              class="button is-small is-danger is-rounded"
              :class="{ 'is-outlined': !user.blocked }"
              :for="user.id"
            >{{ user.blocked === 0 ? "凍結" : "解除" }}</label>
          </td>
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
        .catch(function(error) {
          alert("操作失敗");
        });
    }
  }
};
</script>
