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
                <div class="content">
                    <label class="label">Age</label>
                  <div class="control mb-4">
                    <input class="input" type="text" value="age" readonly />
                  </div>
                    <label class="label">Sexe</label>
                   <div class="control mb-4">
                      <input class="input" type="text" value="masculin" readonly />
                    </div>
                    <label class="label">Date de naissance</label>
                  <div class="control mb-4"><input class="input" type="date" value="date de naissance" readonly/>
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
export default {};
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
  height: 350px;
}
</style>
