<template>
  <section class="hero is-fullheight">
    <Header />
    <div class="hero-body">
      <div class="container">
        <div class="columns is-centered">
          <div class="column is-6-tablet is-5-desktop is-10-widescreen">
            <h4 class="title is-5 has-text-centered mb-20">Mon profil</h4>
            <div class="card mb-6 first-card">
              <div class="card-content">
                <div class="media">
                  <div class="media-left mr-4">
                    <figure class="image is-64x64">
                      <img :src="avatar(this.$store.state.member)" />
                    </figure>
                  </div>
                  <div class="media-content">
                    <p class="title is-4 principal">{{input_unsername}}</p>
                    <p class="subtitle is-6">{{input_email}}</p>
                  </div>
                </div>

                <div class="content ml-4">
                  {{input_description}}
                  <br />
                  <time datetime="2016-1-1">
                    <b>a rejoins le :</b>
                    {{ this.$store.state.member.created_at.substr(0, 10) }}
                    <b>à</b>
                    {{ this.$store.state.member.created_at.substr(11, 8) }}
                  </time>
                </div>
              </div>
            </div>
            <div class="card second-card">
              <div class="card-content">
                <p class="title principal is-6">Mes informations</p>
                <div class="field is-horizontal">
                  <div class="field-label is-normal">
                    <label class="label">Username</label>
                  </div>
                  <div class="field-body">
                    <div class="field">
                      <p class="control is-expanded">
                        <input
                          class="input"
                          type="text"
                          placeholder="Username"
                          v-bind:readonly="readonly"
                          required
                          v-model="input_unsername"
                        />
                      </p>
                    </div>
                  </div>
                </div>
                <div class="field is-horizontal">
                  <div class="field-label is-normal">
                    <label class="label">Email</label>
                  </div>
                  <div class="field-body">
                    <div class="field">
                      <p class="control is-expanded">
                        <input
                          class="input"
                          type="email"
                          placeholder="Email"
                          v-bind:readonly="readonly"
                          required
                          v-model="input_email"
                        />
                      </p>
                    </div>
                  </div>
                </div>
                <div class="field is-horizontal">
                  <div class="field-label is-normal">
                    <label class="label">Sexe</label>
                  </div>
                  <div class="field-body">
                    <div class="field">
                      <p class="control is-expanded">
                        <input
                          class="input"
                          type="text"
                          placeholder="sexe"
                          v-bind:readonly="readonly"
                          v-model="input_sexe"
                          required
                        />
                      </p>
                    </div>
                  </div>
                </div>
                <div class="field is-horizontal">
                  <div class="field-label is-normal">
                    <label class="label">Date de naissance</label>
                  </div>
                  <div class="field-body">
                    <div class="field">
                      <p class="control is-expanded">
                        <input
                          class="input"
                          type="text"
                          placeholder="date de naissance"
                          v-model="input_date_naissance"
                          v-bind:readonly="readonly"
                        />
                      </p>
                    </div>
                  </div>
                </div>
                <div class="field is-horizontal">
                  <div class="field-label is-normal">
                    <label class="label">Telephone</label>
                  </div>
                  <div class="field-body">
                    <div class="field is-expanded">
                      <div class="field has-addons">
                        <p class="control is-expanded">
                          <input
                            class="input"
                            type="text"
                            placeholder="numéro de téléphone"
                            v-bind:readonly="readonly"
                            v-model="input_tel"
                          />
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="field is-horizontal">
                  <div class="field-label is-normal">
                    <label class="label">Bio</label>
                  </div>
                  <div class="field-body">
                    <div class="field">
                      <div class="control">
                        <textarea
                          class="textarea"
                          placeholder="Ajouter une bio"
                          v-bind:readonly="readonly"
                          v-model="input_description"
                        ></textarea>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="field is-horizontal">
                  <div class="field-label"></div>
                  <div class="field-body">
                    <div class="field">
                      <div class="control">
                        <button
                          class="button is-link is-outlined mr-2"
                          @click="enableModification"
                        >Modifier</button>
                        <button
                          class="button is-primary is-outlined"
                          @click="ModifyInformations"
                        >Sauvegarder</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  data() {
    return {
      readonly: true,
      input_unsername: "",
      input_email: "",
      input_sexe: "",
      input_description: "",
      input_date_naissance: "",
      input_tel: "",
      user_informations: "",
    }
  },
  mounted(){
    this.loadUserInformations();
  },
  methods: {
    enableModification() {
      this.readonly = false;
    },
    loadUserInformations() {
      this.$api
        .get(`users/${this.$store.state.member.id}`)
        .then((response) => {
          console.log(response.data);
          this.user_informations = response.data;

          //Remettre les nouvelles valeurs dans les champs : 
          this.input_unsername = response.data.result.username;
          this.input_email = response.data.result.email;
          this.input_sexe = response.data.result.sexe;
          this.input_description = response.data.result.description;
          this.input_date_naissance = response.data.result.dn;
          this.input_tel = response.data.result.tel;

          console.log(this.input_unsername, this.input_email, this.input_sexe);
        })
        .catch((error) => {
          console.log(error);
        });
    },
    ModifyInformations() {
      this.$api
        .put(`users/${this.$store.state.member.id}/informations`, {
          username: this.input_unsername,
          email: this.input_email,
          description: this.input_description,
          sexe: this.input_sexe,
          tel: this.input_tel,
          dn: this.input_date_naissance
        })
        .then((response) => {
          console.log(response.data.user);
          //Recharger les informations du user après modification
          this.loadUserInformations();
        })
        .catch((error) => {
          console.log(error);
        });
    }
  }
};
</script>

<style lang="scss">
.title {
  color: white;
  font-family: "Poppins", sans-serif;
}
.principal {
  color: rgb(56, 54, 54);
}
.first-card {
  height: 200px;
}
.second-card {
  height: 550px;
}
.label {
  text-align: left;
}
</style>
