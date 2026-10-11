<template>
  <div>
    <h1>{{ vendorid }}</h1>
  </div>
</template>

<script>
export default {
  data() {
    return {};
  },
  props: ["vendorid"],
  mounted() {},
  methods: {
    getVendors() {
      axios
        .post("/api/admin/vendor/" + this.vendorid, {
          headers: {
            Authorization:
              "bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9tYWt3YS50ZXN0XC9sb2dpblwvYWRtaW4iLCJpYXQiOjE2MTc2NTIwNDMsImV4cCI6NjE2MTc2NTE5ODMsIm5iZiI6MTYxNzY1MjA0MywianRpIjoiVUpTMWFRNjZENlRwRjNpRCIsInN1YiI6MSwicHJ2IjoiY2YyODRjMmIxZTA2ZjMzYzI2YmQ1Nzk3NTY2ZDlmZDc0YmUxMWJmNSJ9.uShlDtuyzKa0VTKm2x1ABunLlZ8wkZWUjwSKgJJSn3o",
          },
        })
        .then((res) => {
          this.vendors = res["data"]["data"];
          console.log(this.vendors);
        });
    },
  },
};
</script>

<style lang="scss" scoped>
</style>