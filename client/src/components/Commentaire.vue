<template>
  <section>
    <div class="media">
      <div class="media-left">
        <figure class="image is-32x32 mr-2">
          <img :src="avatar(author)" alt="Placeholder image">
        </figure>
      </div>
      <div class="media-content">
        <p class="title-name-comment is-4"><b>{{author.username}}</b></p>
      </div>
    </div>

    <div class="content ml-6" v-html="commentaire.content">
  </div>
  </section>
</template>
<script>
export default {
    props: ["commentaire"],
  data() {
    return {
      id_author : this.commentaire.idUser,
      author : {
        username : "",
        email : "",
      }
    }
  },
  mounted(){
    this.getCommentAuthorInformations();
  },
  methods: {
    getCommentAuthorInformations() {
      //Récupèrer les infos des auteurs de chacuns des commentaires
      if (this.id_author) {
        this.$api
          .get(`users/${this.id_author}`)
          .then((response) => {
            this.author.username = response.data.result.username;
            this.author.email = response.data.result.email;
          })
          .catch((error) => {
            console.log(error);
          });
      }
    }
  }
}
</script>

<style lang="scss">
.title-name-comment {
  color : black;
}
</style>