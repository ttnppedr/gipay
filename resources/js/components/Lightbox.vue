<template>
  <div class="modal is-active">
    <div class="modal-background" @click="$emit('closeModal')"></div>
    <div class="modal-card">
      <header class="modal-card-head">
        <!-- 使用者名稱 -->
        <h2 class="modal-card-title">
          <span class="tag is-info" v-if="userPersonalDetails.admin">Admin</span>
          <span>{{ userAccountName }}</span>
        </h2>
        <button class="delete" aria-label="close" @click="$emit('closeModal')"></button>
      </header>
      <section class="modal-card-body">
        <!-- 主要資訊區域 -->
        <section class="media">
          <!-- 使用者大頭照 -->
          <div class="media-left">
            <figure class="image is-128x128">
              <img class="is-rounded" :src="userPersonalDetails.avatar" />
            </figure>
          </div>
          <div class="media-content user">
            <!-- 使用者 ID 群組 -->
            <div class="user__group">
              <span class="user__inline-group">
                <span class="tag is-dark">ID</span>
                <span class="normal-data">{{ userPersonalDetails.id }}</span>
              </span>
              <span class="user__inline-group">
                <span class="tag is-dark">Slack ID</span>
                <span class="normal-data">{{ userPersonalDetails.slack_id }}</span>
              </span>
              <!-- 使用者註冊時間 -->
              <span class="user__inline-group">
                <span class="tag is-dark">註冊時間</span>
                <span class="normal-data">{{ userPersonalDetails.created_at }}</span>
              </span>
            </div>
            <!-- 使用者餘額及操作 -->
            <div class="tile is-ancestor">
              <div class="tile is-vertical is-12">
                <div class="tile">
                  <div class="tile is-parent is-vertical">
                    <article class="tile is-child notification is-light">
                      <div class="title is-4">
                        ${{ userPersonalDetails.balance }}
                        <button
                          class="button is-small is-primary is-rounded"
                        >存款</button>
                        <button class="button is-small is-primary is-rounded">轉帳</button>
                      </div>
                    </article>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        <!-- 使用者訂單資訊 -->
        <section></section>
      </section>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Lightbox',
  props: {
    userPersonalDetails: {
      type: Object | String,
      default: '',
    },
  },
  computed: {
    userAccountName() {
      console.log(
        `${this.userPersonalDetails.name} (${this.userPersonalDetails.email})`
      );
      return this.userPersonalDetails
        ? `${this.userPersonalDetails.name} (${this.userPersonalDetails.email})`
        : '';
    },
  },
  methods: {
    closeModal() {},
  },
};
</script>

<style lang="scss" scoped>
  .normal-data {
    font-size: 12px;
  }

  h2 {
    font-weight: 700;
    font-size: 18px;
    line-height: 1.5;
    align-items: center;
    display: flex;
  }

  .user {
    line-height: 1.75;
    &__group {
      margin-bottom: 1em;
    }
    &__inline-group + &__inline-group {
      margin-left: 10px;
    }
  }

  /* override Bulma */
  .tile.is-ancestor:not(:last-child) {
    margin-bottom: 0;
  }
  .title:not(:last-child) {
    margin-bottom: 0.5rem;
  }

  .modal-card-title {
    .tag {
      margin-right: 0.5rem;
    }
  }
</style>
