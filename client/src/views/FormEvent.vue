<template>
  <section class="hero is-fullheight">
    <Header />
    <div class="hero-body">
      <div class="container">
        <div class="columns is-centered">
          <div class="column is-7-tablet is-6-desktop is-5-widescreen">
            <h4 class="title is-5 has-text-centered mb-20">Créer un évènement</h4>
            <div class="box has-text-centered" v-if="response_message">{{response_message}}</div>
            <div class="box has-text-centered error_message" v-if="error_message">{{ error_message }}</div>
            <form @submit.prevent="getCoordinateByAdresse">
              <div class="box">
                <div class="field">
                  <label class="label">Titre</label>
                  <div class="control">
                    <input
                      class="input"
                      type="text"
                      placeholder="Titre de votre évènement"
                      required
                      v-model="input_titre"
                    />
                  </div>
                </div>
                <div class="field">
                  <label class="label">Lieu</label>
                  <div class="control">
                    <input
                      class="input"
                      type="text"
                      placeholder="Votre adresse"
                      v-model="addresse"
                      required
                    />
                  </div>
                </div>
                <div class="field">
                  <label class="label">Date</label>
                  <div class="control">
                    <input
                      class="input"
                      type="date"
                      placeholder="date de votre évènement"
                      required
                      v-model="input_date"
                    />
                  </div>
                </div>
                <div class="field">
                  <label class="label">Heure</label>
                  <div class="control">
                    <input
                      class="input"
                      type="time"
                      placeholder="date de votre évènement"
                      required
                      v-model="input_time"
                    />
                  </div>
                </div>

                <div class="field">
                  <label class="label">Description</label>
                  <div class="control">
                    <textarea
                      rows="2"
                      class="textarea"
                      placeholder="Description"
                      required
                      v-model="input_description"
                    ></textarea>
                  </div>
                </div>

                <div class="field is-grouped">
                  <div class="control">
                    <button class="button is-link mr-5" type="submit" @click="submitEvent">Valider</button>
                  </div>
                  <div class="control">
                    <button class="button is-link is-light">Annuler</button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
        <div class="columns is-centered mt-5">
          <div class="column is-6-tablet is-5-desktop is-10-widescreen">
            <h4
              class="subtitle is-6 has-text-centered mb-20"
            >Vous pouvez également indiquer votre lieu sur la MAP :</h4>
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
          </div>
        </div>
      </div>
    </div>
    <Footer />
  </section>
</template>

<script>
import { LMap, LTileLayer, LMarker, LCircle, LPopup } from "vue2-leaflet";
import axios from "axios";
import moment from "moment";
export default {
  components: {
    LMap,
    LTileLayer,
    LMarker,
    LCircle,
    LPopup
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
      input_description: "",
      input_titre: "",
      input_date: null,
      input_time: null,
      response_message : "",
      error_message : "",
    };
  },
  mounted() {
    this.getCoordinateByUserIp();
    this.getCoordinateByAdresse();
  },
  methods: {
    submitEvent() {
      if(Date.parse(this.input_date +' '+this.input_time) > Date.now())
      {
        this.$api.post(`events/`, {
          titre: this.input_titre,
          description: this.input_description,
          dateEvent: {
            date: moment(this.input_date).format('DD-MM-YYYY'),
            heure: this.input_time
          },
          idCreateur: this.$store.state.member.id,
          lieu: this.addresse
        })
          .then((response) => {
            this.input_description = "";
            this.input_titre = "";
            this.input_time = "";
            this.input_date = "";
            this.addresse = "";
            this.response_message = "Votre évènement a été crée avec succès !";
          })
          .catch((error) => {
            console.log(error);
              this.response_message = "Une erreur est parvenue lors de la création de votre évènement :( !";
          });
      }
      else{
        this.error_message = "La date et l'heure choisies sont inférieure à la date et l'heure actuelles";
      }
      
    },
    getCoordinateByUserIp() {
      axios
        .get("http://ip-api.com/json/")
        .then((response) => {
          this.center = [response.data.lat, response.data.lon];
          this.markerLatLng = [response.data.lat, response.data.lon];
          this.ready = true;
          this.circle.center = [response.data.lat, response.data.lon];
        })
        .catch((error) => {
          console.log(error);
        });
    },
    addMarker(event) {
      this.markerLatLng = event.latlng;
      this.getAdresseByCoordinate();
    },
    getAdresseByCoordinate() {
      if (this.markerLatLng != null) {
        axios
          .get(
            `https://api.geoapify.com/v1/geocode/reverse?lat=${this.markerLatLng.lat}&lon=${this.markerLatLng.lng}&apiKey=feb8d3c41d7747c7a7cd3b367fb9c161`
          ).then((response) => {
            this.addresse =
              response.data.features[0].properties.address_line1 +
              ", " +
              response.data.features[0].properties.address_line2;
            this.circle.center = [
              response.data.features[0].properties.lat,
              response.data.features[0].properties.lon,
            ];
          })
          .catch((error) => {
            console.log(error);
          });
      }
    },
    getCoordinateByAdresse() {
      if (this.addresse) {
        axios
          .get(
            `https://api.geoapify.com/v1/geocode/search?text=${this.addresse}&apiKey=feb8d3c41d7747c7a7cd3b367fb9c161`
          )
          .then((response) => {
            this.center = [response.data.features[0].properties.lat, response.data.features[0].properties.lon];
            this.markerLatLng = [response.data.features[0].properties.lat, response.data.features[0].properties.lon];
            this.ready = true;
            this.circle.center = [response.data.features[0].properties.lat, response.data.features[0].properties.lon];
            this.zoom = 17;
          })
          .catch((error) => {
            console.log(error);
          });
      }
    },
  },
};
</script>

<style lang="scss">
.title,
.subtitle {
  color: white;
  font-family: "Poppins", sans-serif;
}
.error_message{
  color: red;
}
//feb8d3c41d7747c7a7cd3b367fb9c161
//
//this.input_time
</style>
