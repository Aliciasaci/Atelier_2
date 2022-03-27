<template>
  <section class="hero is-fullheight">
    <Header />
    <div class="hero-body" v-if="event_data">
      <div class="container">
        <div class="columns is-centered">
          <div class="column is-6-tablet is-5-desktop is-10-widescreen">
            <h4 class="title is-5 has-text-centered mb-20">Détail de l'évènement</h4>
            <p v-if="response_message" class="box has-text-centered response_message">{{ response_message }}</p>
            <div class="box">
              <h4 class="title is-5 has-text-centered mb-20 title-chat">{{ event_data.titre }}</h4>
              <p>{{ event_data.description }}</p>
              <p>
                <b>Date</b>
                : {{ event_data.dateEvent.substr(0, 10) }}
                <b>à</b>
                {{ event_data.dateEvent.substr(11, 8) }}
              </p>
              <p>
                <b>Lieu</b>
                : {{ event_data.lieu }}
              </p>
              <footer class="card-footer">
                <button v-if="$route.params.id && !response_status" class="button is-success is-light btn-footer" @click="acceptInvit($route.params.id)" >Accepter</button>
                <button v-if="$route.params.id && !response_status" class="button is-danger is-light btn-footer" @click="refuseEvent($route.params.id)">Refuser</button>
              </footer>
            </div>
            <h4 class="title is-5 has-text-centered mb-20">Localiser l'endroit sur la MAP :</h4>
            <div class="box" id="map" v-if="ready">
              <template>
                <l-map style="height: 500px" :zoom="zoom" :center="center" @click="addMarker">
                  <l-tile-layer :url="url" :attribution="attribution"></l-tile-layer>
                  <l-marker :lat-lng="markerLatLng" ref="marker">
                    <l-popup v-if="addresse">{{ addresse }}</l-popup>
                  </l-marker>
                  <l-circle :lat-lng="circle.center" :radius="circle.radius" :color="circle.color" />
                </l-map>
              </template>
            </div>
            <div class="columns">
              <div class="column">
                <div class="box">
                  <h4 id="oui-par" class="title is-6 title-chat has-text-centered">Ils ont dit Oui</h4>
                  <div class="box" v-for="p in participants" :key="p.id">
                    <p>
                      <i class="fa-solid fa-check mr-2"></i>
                      {{ p.username }} - {{ p.email }}
                    </p>
                  </div>
                </div>
              </div>
              <div class="column">
                <div class="box">
                  <h4 id="non-par" class="title is-6 title-chat has-text-centered">Ils ont dit non</h4>
                  <div class="box" v-for="p in non_participants" :key="p.id">
                    <p>
                      <i class="fa-solid fa-xmark mr-2"></i>
                      {{ p.username }} - {{ p.email }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
            <div class="box mt-6">
              <h4 class="title is-6 title-chat has-text-centered">Tchat de l'évènement</h4>
              <input class="input box mt-6" type="text" placeholder="Votre message" />
              <div class="box mt-6">
                <article class="media">
                  <div class="media-left">
                    <figure class="image is-32x32 mr-2">
                      <img src="https://bulma.io/images/placeholders/128x128.png" alt="Image" />
                    </figure>
                  </div>
                  <div class="media-content">
                    <div class="content">
                      <p>Lorem ipsum dolor sit amet, consectetur adipiscing</p>
                    </div>
                  </div>
                </article>
              </div>
              <div class="box mt-6">
                <article class="media">
                  <div class="media-left">
                    <figure class="image is-32x32 mr-2">
                      <img src="https://bulma.io/images/placeholders/128x128.png" alt="Image" />
                    </figure>
                  </div>
                  <div class="media-content">
                    <div class="content">
                      <p>egestas. Nullam condimentum luctus turpis.</p>
                    </div>
                  </div>
                </article>
              </div>
              <div class="box mt-6">
                <article class="media">
                  <div class="media-left">
                    <figure class="image is-32x32 mr-2">
                      <img src="https://bulma.io/images/placeholders/128x128.png" alt="Image" />
                    </figure>
                  </div>
                  <div class="media-content">
                    <div class="content">
                      <p>egestas. Nullam condimentum luctus turpis.</p>
                    </div>
                  </div>
                </article>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import axios from "axios";
import { LMap, LTileLayer, LMarker, LCircle, LPopup } from "vue2-leaflet";
export default {
  components: {
    LMap,
    LTileLayer,
    LMarker,
    LCircle,
    LPopup,
  },
  data() {
    return {
      url: "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
      attribution:
        '&copy; <a target="_blank" href="http://osm.org/copyright">OpenStreetMap</a> contributors',
      zoom: 15,
      center: [],
      circle: {
        center: [],
        radius: 100,
        color: "red",
      },
      markerLatLng: [],
      addresse: "",
      markerLatLng: [],
      ready: false,
      event_id: null,
      event_data: null,
      non_participants: [],
      participants: [],
      response_message: "",
      user_invitations: [],
      response_status : false,
    };
  },
  mounted() {
    this.generateEventsInformations();
    this.getNonParticipants();
    this.getParticipants();
  },
  methods: {
    generateEventsInformations() {
      this.event_id = this.$route.params.id;
      this.$api
        .get(`events/${this.event_id}`)
        .then((response) => {
          this.event_data = response.data.event;
          this.getCoordinateByAdresse();
        })
        .catch((error) => {
          console.log(error);
        });
    },
    addMarker(event) {
      this.markerLatLng = event.latlng;
    },
    getCoordinateByAdresse() {
      if (this.event_data.lieu) {
        this.addresse = this.event_data.lieu;
        axios
          .get(
            `https://api.geoapify.com/v1/geocode/search?text=${this.addresse}&apiKey=feb8d3c41d7747c7a7cd3b367fb9c161`
          )
          .then((response) => {
            this.center = [
              response.data.features[0].properties.lat,
              response.data.features[0].properties.lon,
            ];
            this.markerLatLng = [
              response.data.features[0].properties.lat,
              response.data.features[0].properties.lon,
            ];
            this.ready = true;
            this.circle.center = [
              response.data.features[0].properties.lat,
              response.data.features[0].properties.lon,
            ];
            this.zoom = 17;
          })
          .catch((error) => {
            console.log(error);
          });
      }
    },
    getNonParticipants() {
      this.event_id = this.$route.params.id;
      this.$api
        .get(`events/non_participations/${this.event_id}`)
        .then((response) => {
          this.non_participants = response.data.participations[0];
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getParticipants() {
      this.event_id = this.$route.params.id;
      this.$api
        .get(`events/participations/${this.event_id}`)
        .then((response) => {
          this.participants = response.data.participations[0];
        })
        .catch((error) => {
          console.log(error);
        });
    },
    acceptInvit(idEvent) {
      //Chercher l'invitation correspondante à l'evenement et au user qui y répond.
      this.$store.state.invitations.forEach(invitation => {
        if (invitation.idEvent === idEvent && invitation.idUser === this.$store.state.member.id) {
          this.$api
            .put(`/invitations/${invitation.id}`, {
              response: "oui"
            })
            .then((response) => {
              console.log(response)
              this.response_message = "L'invitation a bien été acceptée";
              this.response_status = true;
              window.setTimeout(function(){location.reload()},3000)
            })
            .catch((error) => {
              console.log(error);
            });
        }
      })
    },
    refuseEvent(idEvent) {
      //Chercher l'invitation correspondante à l'evenement et au user qui y répond.
       this.$store.state.invitations.forEach(invitation => {
        if (invitation.idEvent === idEvent && invitation.idUser === this.$store.state.member.id) {
          this.$api
            .put(`/invitations/${invitation.id}`, {
              response: "non",
            })
            .then((response) => {
              console.log(response)
              this.response_message = "L'invitation a bien été refusée";
              this.response_status = true;
               window.setTimeout(function(){location.reload()},3000)
            })
            .catch((error) => {
              console.log(error);
            });
        }
      })
    },
  },
};
</script>

<style lang="scss">
.title {
  color: white;
}
.title-chat {
  color: black;
}

.btn-footer {
  margin: 10px 0em 0px 15em;
  width: 5em;
}
.side {
  height: 800px;
  width: 250px;
}

#non-par {
  color: red;
}

#oui-par {
  color: rgb(48, 160, 85);
}

.response_message{
    color: rgb(48, 160, 85);
}
</style>
