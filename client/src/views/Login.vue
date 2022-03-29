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
            <div>
              <div class="box">
                <p class="is-text invite-text" data-target="inviter" @click="showModalInvite()">
                  <u>Continuer en tant qu'invité</u>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal !-->
    <div id="inviter" class="modal" :class="{ 'is-active': showModalFlagI }">
      <div class="modal-background"></div>
      <div class="modal-card">
        <header class="modal-card-head">
          <p class="modal-card-title is-5">Accéder à votre évènement</p>
          <button class="delete" aria-label="close" @click="cancelModalInvite"></button>
        </header>
        <section class="modal-card-body ml-0.5">
          <p>
            <b>URL</b>
          </p>
          <input
            class="input is-link"
            type="url"
            placeholder="Veuillez saisir l'url de l'évènement"
            v-model="input_link_inviter"
            required
          />
        </section>
        <section class="modal-card-body ml-0.5">
          <p>
            <b>Pseudo</b>
          </p>
          <input
            class="input is-link"
            type="text"
            placeholder="Veuillez saisir votre username"
            v-model="username_visiter"
            required
          />
        </section>
        <footer class="modal-card-foot">
          <button class="button ml-2 is-link is-outlined" @click="createInstanceVisiter">Accéder</button>
          <button class="button ml-2 is-black is-outlined">Effacer</button>
        </footer>
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
      showModalFlagI: false,
      input_link_inviter: "",
      username_visiter: "",
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
          this.$router.push("Accueil");
        })
        .catch((error) => {
          console.log(error);
          this.responseMessage = "Email ou mots de passe incorrecte";
        });
    },
    /*Lorsqu'un visiteur insére son nom, une instance contenant un ID, username et level = 0 lui est crée en base de donnée,
    *il est ensuite redirigé vers l'URL en question
    */
    //!vérifie que l'url existe bel est bien (si t'as le temps)
    createInstanceVisiter() {
      if (this.username_visiter) {
        this.$api
          .post("visiteurs", {
            username: this.username_visiter,
          }).then(response => {
            console.log(response)
            this.$store.commit("setToken", response.data.visiteur.token);
            this.$store.commit("setMember", response.data.visiteur);
            console.log(this.$store.state.member);
            this.goToLink();
          })
      }
    },
    showModalInvite() {
      this.showModalFlagI = true;
    },
    cancelModalInvite() {
      this.showModalFlagI = false;
    },
    goToLink() {
      if (this.input_link_inviter) {
        let route = this.input_link_inviter.slice(21);       //!modifier ça avec la taille du serveur de docketu
        this.$router.push(route);
      }
    }
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
