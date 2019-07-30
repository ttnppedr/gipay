<template>
  <div>
    <div class="field">
      <p class="control">
        <label class="label" for="amount">輸入金額</label>
      </p>
    </div>
    <div class="field has-addons">
      <div class="control has-icons-left is-expanded">
        <input class="input" type="number" ref="amount" placeholder="ex: 100" min="1" max="1000000" />
        <span class="icon is-small is-left">
          <font-awesome-icon icon="dollar-sign" />
          <i class="fas fa-dollar-sign"></i>
        </span>
      </div>
      <p class="control">
        <button class="button is-primary" @click="withdrawMoney">送出</button>
      </p>
    </div>
  </div>
</template>

<script>
import API from '../utilities/API.js';

export default {
  name: 'TabWithdraw',
  props: {
    userId: {
      type: Number,
      default: 1,
    },
  },
  data() {
    return {
      token: window.$cookies.get('token'),
    };
  },
  methods: {
    withdrawMoney() {
      try {
        API.orders.post
          .withdraw(this.token, this.userId, this.$refs.amount.value)
          .then(response => {
            console.log(response);
          });
      } catch (e) {
        console.error(e);
      }
    },
  },
};
</script>
