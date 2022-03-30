<template>
  <section class="hero is-fullheight">
    <Header />
    <div class="hero-body">
      <div class="container">
        <div class="columns is-centered">
          <div class="column is-6-tablet is-5-desktop is-6-widescreen">
            <h4 class="title is-5 has-text-centered mb-20">Mes Invitations</h4>
            <div class="card mb-5" v-for="event in events" :key="event.id">
              <div class="card-content">
                <p class="title title-event event is-5">{{ event.titre }}</p>
                <p class="content">{{ event.description }}</p>
                <p class="subtitle event is-6">
                  <b>Organisateur :</b>
                </p>
                <p class="subtitle event is-6">
                  <b>Adresse :</b>
                  {{ event.lieu }}<br/>
                  <b>Le : </b>
                  <time datetime="2016-1-1">{{ event.dateEvent }}</time>
                </p>
              </div>
              <div class="card">
                <footer class="card-footer">
                  <router-link :to="{name:'DetailEvent' ,params:{id: event.id}}" class="button is-info is-light btn-footer">Consulter</router-link>
                </footer>
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
      user_id: this.$store.state.member.id,
      user_invitations: [],
      events: [],
    }
  },
  mounted() {
    this.getInvitationsByUserId();
  },
  methods: {
    getInvitationsByUserId() {
      this.$api
        .get(`users/${this.user_id}/invitations/`)
        .then((response) => {
          this.user_invitations = response.data.invitations;
          this.$store.commit('setInvitations',this.user_invitations);
          this.getEventsOfInvitations();
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getEventsOfInvitations() {
      if (this.user_invitations) {
        this.user_invitations.forEach(invitation => {
          this.$api
            .get(`/events/${invitation.idEvent}`)
            .then((response) => {
              this.events.push(response.data.event)
            })
            .catch((error) => {
              console.log(error);
            });
        })
      }
    },
  }
};
</script>

<style lang="scss">
.title {
  color: white;
}

.title-event{
  color : black;
}
.btn-footer {
  margin-left : 15em;
  margin-bottom: 1em;
  width: 7em;
}
</style>
