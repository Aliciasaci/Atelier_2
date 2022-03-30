<template>
  <section class="hero is-fullheight">
    <Header />
    <div class="hero-body">
      <div class="container">
        <div class="columns is-centered">
          <div class="column is-6-tablet is-5-desktop is-6-widescreen">
            <h4 class="title is-5 has-text-centered mb-20">Mes évèments</h4>
            <div class="columns is-centered">
              <div class="box button-wrap">
                <div class="buttons">
                  <router-link
                    class="button button is-info is-hovered"
                    to="/FormEvent"
                  >Créer un évènement</router-link>
                </div>
              </div>
            </div>
            <div class="card mb-5" v-for="event in events" :key="event.id">
              <div class="card-content">
                <router-link :to="{ name: 'DetailEvent', params: { id: event.id } }">
                  <p class="title event is-5">{{ event.titre }}</p>
                </router-link>

                <p class="content">{{ event.description }}</p>
                <p class="subtitle event is-6">
                  <b>Organisateur :</b>
                  {{ $store.state.member.username }}
                </p>
                <p class="subtitle event is-6">
                  <b>Adresse :</b>
                  {{ event.lieu }}
                  <b>Le :</b>
                  <time datetime="2016-1-1">{{ event.dateEvent }}</time>
                </p>
              </div>
              <div class="card">
                <footer class="card-footer">
                  <button class="button is-warning is-light btn-footer">Modifier</button>
                  <button
                    class="button is-danger is-light btn-footer js-modal-trigger"
                    data-target="supprimer"
                    @click="deleteEvent(event.id)"
                  >Supprimer</button>
                  <button
                    class="button is-info is-light btn-footer js-modal-trigger"
                    data-target="inviter"
                    @click="showModalInvite(event.id)"
                  >Inviter</button>
                  <button
                    class="button is-black is-light btn-footer js-modal-trigger"
                    data-target="lien"
                    @click="showModalLien(event.id)"
                  >lien</button>
                </footer>
              </div>
              <!-- Modals -->
              <div id="inviter" class="modal" :class="{ 'is-active': showModalFlagI }">
                <div class="modal-background"></div>
                <div class="modal-card">
                  <header class="modal-card-head">
                    <p class="modal-card-title">Inviter</p>
                    <button class="delete" aria-label="close" @click="cancelModalInvite"></button>
                  </header>
                  <section class="modal-card-body ml-0.5">
                    <input
                      class="input is-link"
                      type="text"
                      placeholder="Entrer le username ou l'email"
                      v-model="input_search"
                    />
                    <div v-if="search_results" class="box search-result mt-2">
                      <b>{{ search_results.username }}</b>
                      <p class="subtitle is-6">{{ search_results.email }}</p>
                      <button
                        class="button is-success is-light"
                        @click="createInvitation()"
                      >Envoyer</button>
                    </div>
                    <div v-if="search_message" class="box search-result mt-2">
                      <b>{{ search_message }}</b>
                    </div>
                  </section>
                  <footer class="modal-card-foot">
                    <button class="button ml-2 is-link is-outlined" @click="searchUser()">
                      <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                    <button
                      class="button ml-2 is-danger is-outlined"
                      @click="effacerSearch()"
                    >Effacer</button>
                    <button
                      class="button ml-2 is-dark is-outlined"
                      @click="cancelModalInvite"
                    >Annuler</button>
                  </footer>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div id="lien" class="modal" :class="{ 'is-active': showModalFlagL }">
      <div class="modal-background"></div>
      <div class="modal-card">
        <header class="modal-card-head">
          <p class="modal-card-title">Lien</p>
          <button class="delete" aria-label="close" @click="cancelModalLien"></button>
        </header>

        <section class="modal-card-body">
          <input
            class="input is-link"
            type="text"
            placeholder="Entrer le username ou l'email"
            v-model="eventLink"
          />
        </section>
        <footer class="modal-card-foot">
          <button class="button" @click="cancelModalLien">Cancel</button>
        </footer>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  data() {
    return {
      events: [],
      showModalFlagI: false,
      showModalFlag: false,
      showModalFlagL: false,
      input_search: "",
      eventLink: "http://localhost:8080/DetailEvent/", //!changer ça pour le server de docketu
      search_results: null,
      search_message: "",
      invitation:null,
    };
  },
  mounted() {
    this.$api
      .get(`events/creators/${this.$store.state.member.id}`)
      .then((response) => {
        console.log(response.data.event);
        this.events = response.data.event;
      })
      .catch((error) => {
        console.log(error);
      });
  },
  methods: {
    deleteEvent(id) {
      if (id) {
        if (confirm("êtes-vous sûre de vouloir supprimer cet évènement ?")) {
          this.$api.delete(`events/${id}`)
            .then((response) => {
              console.log(response);
              location.reload();
            })
            .catch((error) => {
              console.log(error);
            });
        }
      }
    },
    generateEventLink(id) {
      if (id) {
        this.eventLink += id;
      }
    },
    searchUser() {
      if (this.input_search) {
        this.$api
          .post("searches/",
            {
              search: this.input_search
            }
          ).then(response => {
            console.log(response);
            this.search_results = response.data.result;
          }).catch(error => {
            this.search_message = "Aucun résultat trouvé :(";
          })
      }
    },
    effacerSearch() {
      this.input_search = null;
      this.search_results = null;
      this.search_message = null;
    },
    showModalInvite(idEvent) {
      this.invitation = idEvent;
      this.showModalFlagI = true;
    },
    cancelModalInvite() {
      this.showModalFlagI = false;
    },
    cancelModalSupp() {
      this.showModalFlag = false;
    },
    showModalLien(id) {
      this.showModalFlagL = true;
      this.generateEventLink(id);
    },
    cancelModalLien() {
      this.showModalFlagL = false;
      this.eventLink = "http://localhost:8080/DetailEvent/";
    },
    createInvitation() {
      if (this.invitation && this.search_results.id) {
        console.log(this.search_results.id);
        this.$api
          .post("invitations/",
            {
              idEvent: this.invitation,
              idUser: this.search_results.id,
              response: ""
            }
          ).then(response => {
            console.log(response);
          })
      }
    }
  },
};
</script>
<style lang="scss">
.title {
  color: white;
  font-family: "Poppins", sans-serif;
}
.btn-footer {
  margin: 10px 0em 10px 3em;
  width: 5em;
}
.event {
  color: black;
}

.search-result {
  background-color: rgb(221, 230, 221);
}

//31fd9fac19b0e13c01528246f92d4a9aa18869bbe5aa5581035be9ea3421ac9b0e5ce1d8
</style>
