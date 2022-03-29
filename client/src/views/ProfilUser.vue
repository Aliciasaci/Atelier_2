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
                    <figure class="image is-48x48">
                      <img
                        :src="avatar(this.$store.state.member)"
                      />
                    </figure>
                  </div>
                  <div class="media-content">
                    <p class="title is-4 principal">{{this.$store.state.member.username}}</p>
                    <p class="subtitle is-6">@{{this.$store.state.member.email}}</p>
                  </div>
                </div>

                <div class="content ml-4">
                  {{this.$store.state.member.description}}
                  <br />
                  <time datetime="2016-1-1"><b>a rejoins le : </b>{{this.$store.state.member.created_at.substr(0,10)}}<b> à </b>{{this.$store.state.member.created_at.substr(11,8)}}</time>
                </div>
              </div>
            </div>
           <div class="card second-card">
              <div class="card-content">
                <p class="title principal is-6">Mes informations</p>
                <div class="field is-horizontal">
                    <div class=" field-label is-normal">
                        <label class="label">Username</label>
                    </div>
                    <div class="field-body">
                      <div class="field">
                        <p class="control is-expanded has-icons-left">
                          <input class="input" type="text" placeholder="Username" v-bind:readonly="readonly">
                          <span class="icon is-small is-left">
                            <i class="fas fa-user"></i>
                          </span>
                        </p>
                      </div>
                    </div>
                </div>
                 <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label"> Email </label>
                    </div>
                    <div class="field-body">
                      <div class="field">
                        <p class="control is-expanded has-icons-left has-icons-right">
                          <input class="input" type="email" placeholder="Email" v-bind:readonly="readonly">
                          <span class="icon is-small is-left">
                            <i class="fas fa-envelope"></i>
                          </span>
                          <span class="icon is-small is-right">
                            <i class="fas fa-check"></i>
                          </span>
                        </p>
                      </div>
                    </div>
                 </div>
                 <div class="field is-horizontal">
                    <div class="field-label is-normal">
                      <label class="label">Sexe</label>
                    </div>
                    <div class="field-body">
                      <div class="field is-narrow">
                        <div class="control">
                          <label class="radio pr-2">
                            <input type="radio" name="member" v-bind:readonly="readonly">
                            Homme
                          </label>
                          <label class="radio">
                            <input type="radio" name="member" v-bind:readonly="readonly">
                            Femme
                          </label>
                           <label class="radio">
                            <input type="radio" name="member" v-bind:readonly="readonly">
                            Autre
                          </label>
                        </div>
                      </div>
                    </div>
                  </div>
                   <div class="field is-horizontal">
                    <div class=" field-label is-normal">
                        <label class="label">Date de naissance</label>
                    </div>
                    <div class="field-body">
                      <div class="field">
                        <p class="control is-expanded has-icons-left">
                          <input class="input" type="date" placeholder="date de naissance" v-model="input_date_naissance" v-bind:readonly="readonly"> 
                        </p>
                      </div>
                    </div>
                </div>
                <div class="field is-horizontal">
                   <div class="field-label is-normal">
                        <label class="label"> Telephone </label>
                    </div>
                  <div class="field-body">
                    <div class="field is-expanded">
                      <div class="field has-addons">
                        <p class="control is-expanded">
                          <input class="input" type="tel" placeholder="numéro de téléphone" v-bind:readonly="readonly">
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
                        <textarea class="textarea" placeholder="Ajouter une bio" v-bind:readonly="readonly"></textarea>
                      </div>
                    </div>
                  </div>
                </div>
                 <div class="field is-horizontal">
                  <div class="field-label">
                  </div>
                  <div class="field-body">
                    <div class="field">
                      <div class="control">
                        <button class="button is-primary" @click="ModifyInformations">Modifier </button>
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
  data(){
    return {
      input_unsername : "",
      input_email : "",
      input_date_naissance : "",
      input_sexe : "",
      input_description : "",
      input_date_naissance : "",
      readonly : true,
    }
  },
  methods : {
    ModifyInformations(){
      this.readonly = false;
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
.label{
  text-align: left;
}
</style>
