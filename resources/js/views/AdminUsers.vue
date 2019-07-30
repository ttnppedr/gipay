<template>
  <section v-if="token" class="gipay-container">
    <section class="hero is-small">
      <div class="hero-body">
        <div class="container">
          <h1 class="title is-4">會員列表</h1>
          <h2 class="subtitle is-t">查看單一會員的資料，並可針對其新增存款、提款、轉帳。</h2>
        </div>
      </div>
    </section>
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
          <th class="userName" @click="getUserData(user.id)">{{ user.name }}</th>
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
    <Lightbox
      v-if="modalStatus"
      @closeModal="toggleLightbox"
      :userPersonalDetails="userPersonalDetails"
    ></Lightbox>
  </section>
</template>

<script>
import API from '../utilities/API.js';
import Lightbox from '../components/Lightbox';

export default {
  components: {
    Lightbox,
  },
  data: function() {
    return {
      token: window.$cookies.get('token'),
      users: [],
      modalStatus: false,
      userID: {},
      userPersonalDetails: '',
    };
  },
  created() {
    if (this.token === null) {
      window.location.replace('/admin-login');
    }
  },
  mounted() {
    API.users.list
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
      this.userPersonalDetail = '';
    },
    async getUserData(id) {
      // open lightbox
      this.toggleLightbox();
      try {
        // get an user's data
        await API.users.userData.get(this.token, id).then(response => {
          console.log(response.data);
          this.userPersonalDetails = response.data;
        });
      } catch (e) {
        console.error('error', e);
      }
    },
    setUserId(id) {
      this.userData.userID = id;
    },
    setBlock(userId, userBlocked, userName) {
      let url =
        userBlocked === 0
          ? `https://gipay.xyz/api/admin/unblock/user/${userId}`
          : `https://gipay.xyz/api/admin/block/user/${userId}`;

      API.users.list.patch(url, {}, this.token).catch(function(error) {
        alert('操作失敗');
      });
    },
  },
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
