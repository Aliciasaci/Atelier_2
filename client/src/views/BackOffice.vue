<template>
    <section class="hero is-fullheight">
        <Header />
        <h4 class="title is-2 has-text-centered mt-6">Backoffice</h4>
        <div class="hero-body">
            <div class="container">
                <div class="columns is-centered">
                    <div class="column is-6-tablet is-5-desktop is-6-widescreen">
                        <div class="columns is-centered">
                            <div class="card">
                                <div class="card-content">
                                    <div class="content">
                                        <button
                                            class="button is-danger"
                                            @click="deleteExpiredEvents()"
                                        >Supprimer les évènements expirés</button>
                                    </div>
                                </div>
                            </div>
                            <div class="card ml-6">
                                <div class="card-content">
                                    <div class="content">
                                        <button
                                            class="button is-danger"
                                            @click="deleteExpiredAccounts()"
                                        >Supprimer les comptes expirés</button>
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
            response_message: "",
        }
    },
    methods: {
        deleteExpiredAccounts() {
            this.$api
                .delete(`users/`)
                .then((response) => {
                    console.log(response);
                    if (response.status == 200) {
                        alert("Les comptes ont été supprimés avec succès");
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        deleteExpiredEvents() {
            this.$api
                .delete(`events/`)
                .then((response) => {
                     if (response.status == 200) {
                        alert("Les évènements ont été supprimés avec succès");
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
        }
    }
};
</script>

<style>
.title {
    color: white;
    font-family: "Poppins", sans-serif;
}
</style>