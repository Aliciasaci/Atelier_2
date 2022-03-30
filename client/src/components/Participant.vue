<template>
    <section>
         <p>{{ username }} - {{ email }}</p>     
    </section>
</template>
<script>
export default {
    props: ["participant"],
    data() {
        return {
            username: "",
            email : "",
        }
    },
    mounted() {
        if (this.participant) {
                this.$api
                    .get(`users/${this.participant}`)
                    .then((response) => {
                        console.log(response);
                        this.username = response.data.result.username;
                        this.email = response.data.result.email;
                    })
                    .catch((error) => {
                        console.log(error);
                    });
            }
    },
}
</script>

<style lang="scss">
.title-name-comment {
    color: black;
}
</style>