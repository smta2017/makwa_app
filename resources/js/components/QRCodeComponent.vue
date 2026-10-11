<template>
  <div>
    <div class="col-12 mt-5">
      <div class="card">
        <div class="card-body">
          <input type="checkbox" id="Reusable" v-model="reusable">
          <label for="checkbox"> reusable </label>
 
          <a href="#" class="btn btn-primary" @click="generateQr(1)">Generate Iron QR-Codes</a>
          <input type="number" max="100" id="codes-count" v-model="codes_count" />
          <a href="#" class="btn btn-dark" @click="generateQr(2)">Generate Clean & Iron QR-Codes</a>
          <a href="#" class="btn btn-success" @click="printQr(2)">Print List</a>

          <div id="data-tables" class="data-tables datatable-dark">
            <table id="orderTable" class="text-center">
              <tbody>
                <tr v-for="bag in bags" :key="bag.id">
                  <td>
                    <vue-qrcode
                      :value="bag.qrcode"
                     
                    />
                    <br />
                    <span >{{ bag.service.charAt(0) }}{{ bag.qrcode }}</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import singleqrcodecomponent from "./SingleQRCodeComponent.vue";
import VueQrcode from "vue-qrcode";
export default {
  data() {
    return {
      reusable:false,
      bags: [],
      codes_count: 10,
    };
  },
  components: {
    singleqrcodecomponent,
    VueQrcode,
  },
  mounted() {
    this.getFreeBags();
  },
  methods: {
    getFreeBags() {
      axios.get("/api/getFreeBags").then((res) => {
        this.bags = res.data;
        console.log(this.bags.length);
      });
    },
    generateQr(serv) {
      axios
        .get("/api/generate-qrcode", {
          params: {
            reusable: this.reusable,
            service_id: serv,
            counter: this.codes_count,
          },
        })
        .then((res) => {
          this.getFreeBags();
        });
    },
    printQr() {
      if (this.bags.length != 0) {
        axios.get("/api/update-qr-status").then((res) => {
          console.log(res);
        });

        this.$htmlToPaper("data-tables");
      }
    },
  },
};
</script>

<style lang="scss" scoped>
</style>