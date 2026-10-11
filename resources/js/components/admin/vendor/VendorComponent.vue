<template>
  <div>
    <div class="main-content-inner">
      <div class="row">
        <!-- Dark table start -->
        <div class="col-12 mt-5">
          <div class="card">
            <div class="card-body">
                <a href="#" class="btn btn-success " @click="createVendor()">Add vendor</a>
              <div class="data-tables datatable-dark">
                <table id="ordersrepo" style="width: 100%; text-align: center">
                  <thead>
                    <td>id</td>
                    <td>Name</td>
                    <td>Email</td>
                    <td>General Rating</td>
                    <td>max orders</td>
                    <td>min order price</td>
                    <td></td>
                    <td></td>
                    <td>details</td>
                    <td></td>
                  </thead>
                  <tbody>
                    <tr v-for="vendor in vendors" :key="vendor.id">
                      <td>{{ vendor.id }}</td>
                      <td>{{ vendor.name }}</td>
                      <td>{{ vendor.email }}</td>
                      <td>{{ vendor.rating.generalRating }}</td>
                      <td>{{ vendor.max_orders }}</td>
                      <td>{{ vendor.min_order_price }}</td>
                      <td><a href="#" class="btn btn-primary" @click="showReviews(vendor.id)" :vid="vendor.id">Reviews</a></td>
                      <td><a href="#" class="btn btn-info" @click="showTimeSlots(vendor.id)" :vid="vendor.id">Time Slots</a></td>
                      <td><a v-bind:href="'/admin/vendor/'+vendor.id" class="btn btn-warnig" >Details</a></td>
                      <td><a v-bind:href="'/admin/vendor-offres?vendor_id='+vendor.id" class="btn btn-warnig" >Offers</a></td>
                      
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <!-- Dark table end -->
      </div>
    </div>
    

  <div class="modal fade bd-slots-modal-lg item-pop" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">slots</h5>
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
            </div>
            <div class="modal-body">
             
             <table>
                 <thead>
                      <th style="width: 100px;">id</th>
                      <th style="width: 100px;">location</th>
                      <th style="width: 100px;">day</th>
                      <th style="width: 100px;">start</th>
                      <th style="width: 100px;">end</th>
                      <th style="width: 100px;">type</th>
                     <th  style="width: 100px;">allow</th>
                     <th  style="width: 100px;"></th>
                 </thead>
                 <tbody id=vendorslots>
                    
                 </tbody>
             </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>


  <div class="modal fade bd-reviews-modal-lg item-pop" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reviews</h5>
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
            </div>
            <div class="modal-body">
             
             <table>
                 <thead>
                     <th style="width: 100px;">title</th>
                     <th  style="width: 100px;">body</th>
                     <th>Customer#</th>
                 </thead>
                 <tbody id=vendorre>
                    
                 </tbody>
             </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

  <div class="modal fade bd-createvendor-modal-lg item-pop" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">new vendor</h5>
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
            </div>
            <div class="modal-body">
              
                <div class="form-group" >
                    <label for="ironbags" class="col-form-label">name</label>
                    <input class="form-control" type="text"  v-model="vname" id="name" name="name" required >
                </div>

                <div class="form-group" >
                    <label for="ironbags" class="col-form-label">email</label>
                    <input class="form-control" type="email" v-model="email" id="email" name="email required">
                </div>

                <div class="form-group" >
                    <label for="ironbags" class="col-form-label">max orders</label>
                    <input class="form-control" type="number" v-model="max_order" id="max_order" name="max_order">
                </div>
             
                <div class="form-group" >
                    <label for="ironbags" class="col-form-label">min order price</label>
                    <input class="form-control" type="number" v-model="min_order_price" id="min_order_price" name="min_order_price">
                </div>
             
                <div class="form-group" >
                    <label for="ironbags" class="col-form-label">delivery fee</label>
                    <input class="form-control" type="number" v-model="delivery_fee" id="delivery_fee" name="delivery_fee">
                </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" @click="saveVendor()" data-dismiss="modal">save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>

    
</div>
  </div>



  </div>
  
  <!-- main content area end -->
</template>

<script>
import ExampleComponent from '../../ExampleComponent.vue';
export default {
  components: { ExampleComponent },
  data() {
    return {
      token: localStorage.getItem("token"),
      vendors: [],
      vendor:[],
    vname:'',
    email:'',
    max_order:'',
    min_order_price:'',
    delivery_fee:'',

    };
  },
  mounted() {
    this.getVendors();
  },
  methods: {
      saveVendor(){
       axios
        .post("/api/admin/vendor-save", {
            vname:this.vname,
            email:this.email,
            max_orders:this.max_order,
            min_order_price:this.min_order_price,
            delivery_fee:this.delivery_fee,
          },
         )
        .then((res) => {
          this.getVendors();
        });
      },
    createVendor(){
        $('.bd-createvendor-modal-lg').modal('show');
    },
    showTimeSlots(vendorid){
       axios
        .post("/api/admin/vendor-slots", {
          vendor_id: vendorid,
        })
        .then((res) => {
          var slots = "";
          $.each(res.data, function (i, slot) {
            slots +=
              ` <tr>
                    <td>` +slot.id +`</td>
                    <td>` +slot.slot_location +`</td>
                    <td>` +slot.slot_day +`</td>
                    <td>` +slot.slot_start +`</td>
                    <td>` +slot.slot_end +`</td>
                    <td>` +slot.slot_type +`</td>
                    <td>` +slot.allow +`</td>
                    <td><a id=` +slot.id +` href="#" class="deleteslot btn btn-danger">delete</a></td>
                   
                </tr>`;
          });
          $("#vendorslots").html(slots);
        });
        $('.bd-slots-modal-lg').modal('show');


    },

    showReviews(vendorid){
        this.vendor =this.vendors[this.vendors.findIndex(x => x.id ===vendorid)];
        var revs="";
        $.each(this.vendor.rating.approvedRatings, function(i, review) {
            revs+=` <tr>
                        <td>`+review.title+`</td>
                        <td>`+review.body+`</td>
                        <td>`+review.author_id+`</td>
                    </tr>`;
         });
        $('#vendorre').html(revs);
        $('.bd-reviews-modal-lg').modal('show');
    },
    getVendors() {
      axios
        .post("/api/admin/vendor", {
          headers: {
            Authorization:this.token
          },
        })
        .then((res) => {
          this.vendors = res["data"]["data"];
        });
    },
  },
};

$(document).on('click','.deleteslot',function(){
     axios
        .delete("/api/admin/vendor-slots/"+$(this).attr('id'), {
        })
        .then((res) => {
        $('.bd-slots-modal-lg').modal('hide');
          
        });
})
</script>

<style lang="scss" scoped>
</style>