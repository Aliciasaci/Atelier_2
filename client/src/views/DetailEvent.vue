<template>
  <section class="hero is-fullheight">
    <Header />
    <div class="hero-body" v-if="event_data">
      <div class="container">
        <div class="columns is-centered">
          <div class="column is-6-tablet is-5-desktop is-10-widescreen">
            <h4 class="title is-5 has-text-centered mb-20">Détail de l'évènement</h4>
            <p
              v-if="response_message"
              class="box has-text-centered response_message"
            >{{ response_message }}</p>
            <div class="box">
              <h4 class="title is-5 has-text-centered mb-20 title-chat">{{ event_data.titre }}</h4>
              <p>{{ event_data.description }}</p>
              <p>
                <b>Créateur:</b>
                {{ createur_informations.username }} -
                {{ createur_informations.email }}
              </p>
              <p>
                <b>Date</b>
                : {{ event_data.dateEvent.substring(0, 10) }}
                <b>à</b>
                {{ event_data.dateEvent.substring(11, 8) }}
              </p>
              <p>
                <b>Lieu</b>
                : {{ event_data.lieu }}
              </p>
              <footer class="card-footer" v-if="event_actif">
                <button
                  v-if="
                    $route.params.id && !already_response
                  "
                  class="button is-success is-light btn-footer"
                  @click="acceptInvit($route.params.id)"
                >Accepter</button>
                <button
                  v-if="
                    $route.params.id && !already_response
                  "
                  class="button is-danger is-light btn-footer"
                  @click="refuseEvent($route.params.id)"
                >Refuser</button>
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
                <div class="box" v-if="count_2">
                  <h4 id="oui-par" class="title is-6 title-chat has-text-centered">Ils ont dit Oui</h4>
                  <div class="box" v-for="index in count_2" :key="index">
                    <participant :participant="i_participate[index - 1]" />
                  </div>
                </div>
              </div>
              <div class="column">
                <div class="box">
                  <h4 id="non-par" class="title is-6 title-chat has-text-centered">Ils ont dit Non</h4>
                  <div v-if="count_1">
                    <div class="box" v-for="index in count_1" :key="index">
                      <participant :participant="i_dont_participate[index - 1]" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="box mt-6">
              <h4 class="title is-4 title-chat has-text-centered">Tchat de l'évènement</h4>
              <input
                class="input box mt-6"
                type="text"
                placeholder="Votre commentaire"
                v-model="input_message"
              />
              <button class="button is-outlined is-link" @click="sendComment">send</button>
              <div class="box mt-2" v-for="comment in comments" :key="comment.id">
                <commentaire :commentaire="comment" :key="comment.id" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div id="inviter" class="modal" :class="{ 'is-active': showModalFlagI }">
      <div class="modal-background"></div>
      <div class="modal-card">
        <header class="modal-card-head">
          <p class="modal-card-title is-5">Accéder à votre évènement</p>
        </header>
        <section class="modal-card-body ml-0.5">
          <p>
            <b>Votre psuedo</b>
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
          <button class="button ml-2 is-link is-outlined" @click="createInstanceVisiter">valider</button>
          <button class="button ml-2 is-black is-outlined" @click="effacerVisiteur">Effacer</button>
        </footer>
      </div>
    </div>
  </section>
</template>

<script>
import axios from "axios";
import { LMap, LTileLayer, LMarker, LCircle, LPopup } from "vue2-leaflet";
import Commentaire from "../components/Commentaire.vue";
import Participant from "../components/Participant.vue";
export default {
  components: {
    LMap,
    LTileLayer,
    LMarker,
    LCircle,
    LPopup,
    Commentaire,
    Participant,
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
      participants: [],
      response_message: "",
      user_invitations: [],
      event_actif: true,
      input_message: "",
      id_createur: null,
      createur_informations: [],
      comments: [],
      showModalFlagI: false,
      username_visiter: "",
      already_response: false,
      i_participate: [],
      i_dont_participate: [],
      count_1: 0,
      count_2: 0,
    };
  },
  mounted() {
    //Si aucun access token alors recevoir la personne en tant que "visiteur"
    if (this.$store.state.member.role != 100 && !this.$store.state.token_visiteur && !this.$store.state.member) {
      let $token_visiteur =
        "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpc3MiOiJodHRwOlwvXC9hcGkuYXV0aGVudGlmaWNhdGlvbi5sb2NhbFwvYXV0aCIsImF1ZCI6Imh0dHA6XC9cL2FwaS5iYWNrb2ZmaWNlLmxvY2FsIiwiaWF0IjoxNjQ4NjMwNDIyLCJleHAiOjE2NTEyMjI0MjIsInVwciI6eyJ1c2VyX2lkIjoiYjFhOGY2YzctNTBhZi00Y2ExLWE5ZWYtYjA5NmM0N2E4NWMyIiwiZW1haWwiOiJseXphQGdtYWlsLmNvbSIsInVzZXJuYW1lIjoibHl6YSJ9fQ.Z1LpTuIyP1JKapaIi1sdyIeqwCmLL8lBwmdi0I_8eKgArTS7gKUSG1ne0DuqvQ4Tyi-jbTvhS9olXWkDDNt6LA";
      this.showModalFlagI = true;
      this.$store.commit("setTokenVisiteur", $token_visiteur);
    }
    if ((this.$store.state.token || this.$store.state.member || this.$store.state.$token_visiteur)) {
      this.generateEventsInformations();
      this.getParticipants();
      this.getCommentsOfEvent();
    }
  },
  methods: {
    generateEventsInformations() {
      this.event_id = this.$route.params.id;
      this.$api
        .get(`events/${this.event_id}`)
        .then((response) => {
          this.event_data = response.data.event;
          this.id_createur = response.data.event.idCreateur;
          if (this.$store.state.member.id == this.id_createur) {
            this.already_response = true;
          }
          this.getEventCreatorInformations();
          //Vérifier que l'évènement est toujours pas passé, dans le cas contraire, retirer les options "accepter" et "refuser"
          if (this.event_data) {
            if (Date.parse(this.event_data.dateEvent) < Date.now()) {
              this.event_actif = false;
            }
          }
          this.getCoordinateByAdresse();
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getCommentsOfEvent() {
      if (this.event_id) {
        this.$api
          .get(`events/${this.event_id}/comments`)
          .then((response) => {
            this.comments = response.data.data;
          })
          .catch((error) => {
            console.log(error);
          });
      }
    },
    getEventCreatorInformations() {
      if (this.id_createur) {
        this.$api
          .get(`users/${this.id_createur}`)
          .then((response) => {
            this.createur_informations = response.data.result;
          })
          .catch((error) => {
            console.log(error);
          });
      }
    },
    addMarker(event) {
      this.markerLatLng = event.latlng;
    },
    //get les coordonnée d'une adresse et positionner le marker
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
    getParticipants() {
      this.event_id = this.$route.params.id;
      this.$api
        .get(`events/participations/${this.event_id}`)
        .then((response) => {
          this.participants = response.data.resultat;
          this.getParticipantsResponse();
          if (this.participants) {
            for (let p in this.participants) {
              let par = this.participants[p];
              if (par.idUser == this.$store.state.member.id && par.response != '') {
                this.already_response = true;
              }
            }
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    acceptInvit(idEvent) {
      //Chercher l'invitation correspondante à l'évènement et au user qui y répond.
      //Si c'est le user actuellement connecté alors modifier l'invitation crée à travers la plateforme.
      if (this.$store.state.invitations) {
        this.$store.state.invitations.forEach((invitation) => {
          if (
            invitation.idEvent === idEvent &&
            invitation.idUser === this.$store.state.member.id &&
            this.$store.state.member.role == 100
          ) {
            this.$api
              .put(`/invitations/${invitation.id}`, {
                response: "oui",
              })
              .then((response) => {
                console.log(response);
                this.response_message = "L'invitation a bien été acceptée";
                window.setTimeout(function () {
                  location.reload();
                }, 3000);
              })
              .catch((error) => {
                console.log(error);
              });
          }
        });
        //Si c'est un user invité qui a utilisé une URL alors créer une instance invitation et renseigner la réponse
        if (this.$store.state.member.role == 0) {
          this.$api
            .post(`/invitations/`, {
              idUser: this.$store.state.member.id,
              idEvent: this.event_id,
              response: "oui",
            })
            .then((response) => {
              this.response_message = "L'invitation a bien été acceptée";
              this.response_status = true;
              window.setTimeout(function () {
                location.reload();
              }, 3000);
            })
            .catch((error) => {
              console.log(error);
            });
        }
      }
    },
    refuseEvent(idEvent) {
      //Chercher l'invitation correspondante à l'évènement et au user qui y répond.
      //Si c'est le user actuellement connecté alors modifier l'invitation crée à travers la plateforme
      this.$store.state.invitations.forEach((invitation) => {
        if (
          invitation.idEvent === idEvent &&
          invitation.idUser === this.$store.state.member.id &&
          this.$store.state.member.role == 100
        ) {
          this.$api
            .put(`/invitations/${invitation.id}`, {
              response: "non",
            })
            .then((response) => {
              this.response_message = "L'invitation a bien été refusée";
              this.response_status = true;
              window.setTimeout(function () {
                location.reload();
              }, 3000);
            })
            .catch((error) => {
              console.log(error);
            });
        }
      });
      //Si c'est un user invité alors créer une instance invitation et renseigner la réponse
      if (this.$store.state.member.role == 0) {
        this.$api
          .post(`/invitations/`, {
            idUser: this.$store.state.member.id,
            idEvent: this.event_id,
            response: "non",
          })
          .then((response) => {
            this.response_message = "L'invitation a bien été refusée";
            this.response_status = true;
            window.setTimeout(function () {
              location.reload();
            }, 3000);
          })
          .catch((error) => {
            console.log(error);
          });
      }
    },
    sendComment() {
      if (this.input_message) {
        this.$api
          .post(`comments/`, {
            id_event: this.event_id,
            id_user: this.$store.state.member.id,
            content: this.input_message,
          })
          .then((response) => {
            this.getCommentsOfEvent();
            this.input_message = "";
          })
          .catch((error) => {
            console.log(error);
          });
      }
    },
    //Créer une instance de visiteur composé de son username uniquement
    createInstanceVisiter() {
      if (this.username_visiter) {
        this.$api
          .post("visiteurs", {
            username: this.username_visiter,
          })
          .then((response) => {
            this.showModalFlagI = false;
            this.$store.commit("setMember", response.data.visiteur);
            this.generateEventsInformations();
            this.getParticipants();
            this.getCommentsOfEvent();
          });
      }
    },
    effacerVisiteur() {
      this.username_visiter = "";
    },
    getParticipantsResponse() {
      if (this.participants) {
        this.participants.forEach(p => {
          if (p.response == 'non') {
            this.i_dont_participate.push(p.idUser);
            this.count_1 += 1;
            console.log(this.count_1);
          }
          if (p.response == 'oui') {
            this.i_participate.push(p.idUser);
            this.count_2 += 1;
            console.log(this.count_2);
          }
        });
      }
    }
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

.response_message {
  color: rgb(48, 160, 85);
}
</style>
