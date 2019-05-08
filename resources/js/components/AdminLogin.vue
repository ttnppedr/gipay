<style>

</style>

<template>
    <div>
        <div>
            <div>
                <span>帳號：</span>
                <input v-model="email">
            </div>
            <div>
                <span>密碼：</span>
                <input v-model="password" type="password">
            </div>
        </div>
        <button v-on:click="login">Login</button>
    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                email: '',
                password: ''
            }
        },
        methods: {
            login: function (event) {
                axios.post('/api/login', {
                    email: this.email,
                    password: this.password
                })
                    .then(function (response) {
                        window.$cookies.set('token', response.data.token);
                        window.location.href = '/admin-index';
                    })
                    .catch(function (error) {
                        console.log(error)
                    });
            }
        }
    }
</script>
