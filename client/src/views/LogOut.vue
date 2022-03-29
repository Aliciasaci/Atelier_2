<template>
  <div>Deconnexion en cours</div>
</template>

<script>
export default {
  mounted() {
    this.$store.commit("setToken", false);
    this.$store.commit("setMember", false);
    setTimeout(() => {
      this.$router.push("/");
    }, 100);
  },
};
</script>
