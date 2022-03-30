<template>
  <section class="hero is-fullheight">
    <div class="hero-body">
      <div class="container">
        <div class="columns is-centered">
          <div class="column is-6-tablet is-5-desktop is-4-widescreen">
            <h1 class="title is-2">Reunionou</h1>
            <div class="box">
              <h4 class="title is-4 has-text-centered se-connecter">
                <b>Se connecter</b>
              </h4>
              <form @submit.prevent="login">
                <div class="field">
                  <label class="label">Email</label>
                  <div>
                    <input
                      class="input is-small"
                      type="email"
                      v-model="email"
                      placeholder="Veuillez saisir votre @mail"
                    />
                  </div>
                  <p v-if="responseMessage" class="help is-danger">{{ responseMessage }}</p>
                </div>

                <div class="field">
                  <label class="label">Password</label>
                  <div>
                    <input
                      class="input is-small"
                      type="password"
                      v-model="password"
                      placeholder="Veuillez saisir votre mot de passe"
                    />
                  </div>
                  <p v-if="responseMessage" class="help is-danger">{{ responseMessage }}</p>
                </div>

                <div class="field is-grouped">
                  <div class="control">
                    <button class="button is-link is-normal">valider</button>
                  </div>
                </div>
              </form>
            </div>
            <div class="box">
              Pas encore inscrit ?
              <router-link class="is-text" to="/SingnIn">S'inscrire</router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
<script>
export default {
  mounted() { },
  data() {
    return {
      email: "",
      password: "",
      responseMessage: "",
    }
  },
  methods: {
    login() {
      this.$api
        .post("auth",
          {},
          {
            auth: {
              username: this.email,
              password: this.password,
            },
          }
        )
        .then((response) => {
          this.$store.commit("setToken", response.data.token);
          this.$store.commit("setMember", response.data.user);
          if(this.$store.state.member.role == 100){
          this.$router.push("Accueil");
          }else{
          this.$router.push("BackOffice");
          }
        })
        .catch((error) => {
          console.log(error);
          this.responseMessage = "Email ou mots de passe incorrecte";
        });
    },
  }

};
</script>
<style lang="scss">
.title {
  text-align: center;
  font-family: "Abril Fatface", sans-serif;
  color: white;
}
.se-connecter {
  color: rgb(49, 47, 47);
}
.invite-text {
  color: black;
}
@import url("https://fonts.googleapis.com/css2?family=Abril+Fatface&family=Alegreya+Sans:wght@300&family=Cormorant+Garamond:wght@300;600&family=Lora:wght@500&family=Old+Standard+TT&family=Pacifico&family=Poppins&family=Roboto+Mono:wght@500&family=Roboto:wght@300;400&family=Space+Mono&display=swap");
</style>
