<template>
    <section>
        <div class="column">
            <div class="box">
                <h4 id="oui-par" class="title is-6 title-chat has-text-centered">Ils ont dit Oui</h4>
                <div class="box" v-for="p in participants" :key="p.id">
                    <span v:bind-value="getCommentAuthorInformation">
                        <i class="fa-solid fa-check mr-2"></i>
                        {{ p.username }} - {{ p.email }}
                    </span>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
export default {
    props: ["participants"],
    data() {
        return {
            // id_author: this.commentaire.idUser,
            // author: {
            //     username: "",
            //     email: "",
            // }
        }
    },
    mounted() {
        console.log(this.participants);
    },
    methods: {
        getCommentAuthorInformations() {
            //Récupèrer les infos des auteurs de chacuns des commentaires
            console.log("ici");
            // if (this.id_author) {
            //     this.$api
            //         .get(`users/${this.id_author}`)
            //         .then((response) => {
            //             this.author.username = response.data.result.username;
            //             this.author.email = response.data.result.email;
            //         })
            //         .catch((error) => {
            //             console.log(error);
            //         });
            // }
        }
    }
}
</script>

<style lang="scss">
.title-name-comment {
    color: black;
}
</style>