<template>
  <section class="hero is-fullheight">
    <div class="hero-body">
      <div class="container">
        <div class="columns is-centered">
          <div class="column is-6-tablet is-5-desktop is-4-widescreen">
            <h1 class="title is-2">Reunionou</h1>
            <div class="box">
              <h4 class="title is-4 has-text-centered inscrire">
                <b>S'inscrire</b>
              </h4>
              <h6 class="subtitle has-text-centered is-6 reponse" v-if="responseMessage">{{ responseMessage }}</h6>
              <h6 class="subtitle has-text-centered is-6 errorMessage" v-if="errorMessage">{{ errorMessage }}</h6>
              <div class="field">
                <label class="label">Pseudo</label>
                <div>
                  <input
                    v-model="uname"
                    class="input is-small"
                    type="text"
                    placeholder="Veuillez saisir votre psuedo"
                    required
                  />
                </div>
              </div>

              <div class="field">
                <label class="label">Email</label>
                <div>
                  <input
                    v-model="mail"
                    class="input is-small"
                    type="email"
                    placeholder="Veuillez saisir votre @mail"
                    required
                  />
                </div>
              </div>

              <div class="field">
                <label class="label">Password</label>
                <div>
                  <input
                    v-model="pwd"
                    class="input is-small"
                    type="password"
                    placeholder="Veuillez saisir votre mot de passe"
                    required
                  />
                </div>
              </div>

              <div class="field">
                <label class="label">Confirm password</label>
                <div>
                  <input
                    v-model="pwd_confirm"
                    class="input is-small"
                    type="password"
                    placeholder="Veuillez saisir votre mot de passe"
                    required
                  />
                </div>
              </div>

              <div class="field is-grouped">
                <div class="control">
                  <button @click="signIn" class="button is-link">valider</button>
                </div>
              </div>
            </div>
            <div class="box">
              Vous avez déjà un compte ?
              <router-link class="is-text" to="/">Se connecter</router-link>
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
      uname: "",
      mail: "",
      pwd: "",
      responseMessage: "",
      errorMessage : "",
      pwd_confirm: "",
    }
  },
  methods: {
    signIn() {
      let hasUpperCase = (string) => /[A-Z]/.test(string);
      let hasLowerCase = (string) => /[a-z]/.test(string);
      let hasNumber = (string) => /[0-9]/.test(string);
      let isValid = (string) => /^(\d|\w)*$/.test(string);
      //vérifier que tous les champs sont remplie et vérifier que les deux mots de passe match et qu'il est complexe
      if (this.pwd_confirm != '' && this.uname != '' && this.mail != '' && this.pwd != "") {
        if(isValid(this.uname))
        {
          if (this.pwd == this.pwd_confirm) {
            if (hasUpperCase(this.pwd) && hasLowerCase(this.pwd) && hasNumber(this.pwd)) {
              this.$api
                .post("signin", {
                  uname: this.uname,
                  mail: this.mail,
                  pwd: this.pwd,
                }).then(response => {
                  if (response.status == 200) {
                    this.responseMessage = "Votre compte a été crée avec succès.";
                    this.errorMessage = "";
                  } else {
                    this.responseMessage = "une erreur est parvenue lors de la création de votre compte.";
                  }
                })
            } else {
              console.log('Le mot de passe doit contenir au moin une majuscule une minuscule et un chiffre');
              this.errorMessage = 'Le mot de passe doit contenir au moin une majuscule, une minuscule et un chiffre';
            }
          }
          else {
            console.log('Les mots de passes ne correspondent pas');
            this.errorMessage = 'Les mots de passes ne correspondent pas';
          }
        }
        else{
          console.log('Le pseudo doit être formé uniquement de lettres et de chiffres');
          this.errorMessage = 'Le pseudo doit être formé uniquement de lettres et de chiffres';
        }
        
      }
      else
      {
        console.log('Tous les champs doivent être rempli');
          this.errorMessage = 'Tous les champs doivent être rempli';
      }
    }

  }
}
</script>
<style lang="scss">
.title {
  text-align: center;
  font-family: "Abril Fatface", sans-serif;
  color: white;
}
.inscrire {
  color: rgb(49, 47, 47);
}
.reponse {
  color: rgb(86, 201, 101);
}
.errorMessage{
  color : red;
}
@import url("https://fonts.googleapis.com/css2?family=Abril+Fatface&family=Alegreya+Sans:wght@300&family=Cormorant+Garamond:wght@300;600&family=Lora:wght@500&family=Old+Standard+TT&family=Pacifico&family=Poppins&family=Roboto+Mono:wght@500&family=Roboto:wght@300;400&family=Space+Mono&display=swap");
</style>
