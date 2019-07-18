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
          <th class="userName" @click="toggleLightbox">{{ user.name }}</th>
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
    <Lightbox v-if="modalStatus" @closeModal="toggleLightbox"></Lightbox>
  </section>
</template>

<script>
import API from "../utilities/API.js";
import Lightbox from "../components/Lightbox";

export default {
  components: {
    Lightbox
  },
  data: function() {
    return {
      token: window.$cookies.get("token"),
      users: [],
      modalStatus: false,
      userID: {}
    };
  },
  created() {
    if (this.token === null) {
      window.location.replace("/admin-login");
    }
  },
  mounted() {
    API.users
      .get(this.token)
      .then(response => {
        this.users = response.data.data;
      })
      .catch(function(error) {
        console.log(error);
      });
  },
  methods: {
    toggleLightbox() {
      this.modalStatus = !this.modalStatus;
    },
    setUserId(id) {
      this.userData.userID = id;
    },
    setBlock(userId, userBlocked, userName) {
      let url =
        userBlocked === 0
          ? `https://gipay.xyz/api/admin/unblock/user/${userId}`
          : `https://gipay.xyz/api/admin/block/user/${userId}`;

      API.users.patch(url, {}, this.token).catch(function(error) {
        alert("操作失敗");
      });
    }
  }
};
</script>

<style lang="scss" scoped>
.userName {
  &:hover {
    color: #209cee;
    cursor: pointer;
  }
}
</style>
