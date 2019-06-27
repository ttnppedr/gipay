<style>

</style>

<template>
    <div v-if="token">
        <div>
            <ul>
                <li>
                    ID,
                    帳號,
                    餘額,
                    凍結,
                    錯誤次數,
                    e-mail,
                    管理者
                </li>
                <li v-for="user in users">
                    {{ user.id }},
                    {{ user.name }},
                    ${{ user.balance }},
                    <input type="checkbox" v-model="user.blocked" v-bind:true-value="1" v-bind:false-value="0" @change="setBlock(user.id, user.blocked, user.name)">,
                    {{ user.password_errors }},
                    {{ user.email }},
                    {{ user.admin }},
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
export default {
    data: function () {
        return {
            token: window.$cookies.get('token'),
            users: []
        }
    },
    created() {
        if (this.token === null) {
            window.location.replace('/admin-login');
        }
    },
    mounted() {
        axios.get('https://gipay.xyz/api/admin/users', {
            headers: {
                    'Authorization': `Bearer ${this.token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then((response) => {
                this.users = response.data.data;
            })
            .catch(function (error) {
                console.log(error)
            });
    },
    methods: {
        setBlock(userId, userBlocked, userName) {
            let url = (userBlocked === 0) ? `https://gipay.xyz/api/admin/unblock/user/${userId}` : `https://gipay.xyz/api/admin/block/user/${userId}`;
            console.log(url);
            axios.patch(url, {}, {
                headers: {
                        'Authorization': `Bearer ${this.token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then((response) => {
                    let msg = userName;
                    msg += (userBlocked === 0) ? ' 已解除凍結' : ' 已凍結';
                    alert(msg);
                })
                .catch(function (error) {
                    alert('操作失敗');
                });
            }
    }
}
</script>
