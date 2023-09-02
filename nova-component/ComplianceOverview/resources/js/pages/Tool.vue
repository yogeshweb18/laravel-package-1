<template>
  <div id="app">
    <div id="nav">
      <div class="w-full flex items-center mb-6">
        <h1 class="text-90" style="margin:0px;">Compliance Summary</h1>
        <div class="flex-shrink-0 ml-auto" v-if="viewOnly != 1">
                           <Link size="md" class="flex-shrink-0 shadow rounded focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring bg-primary-500 hover:bg-primary-400 active:bg-primary-600 text-white dark:text-gray-800 inline-flex items-center font-bold px-4 h-9 text-sm flex-shrink-0" dusk="create-button" href="/admin/compliance-overview/create"><span class="hidden md:inline-block">Create Compliance</span><span class="inline-block md:hidden">Create</span></Link>
        </div>
      </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow" style=
    "padding:20px;">
      <table ref="dataTable" class="w-full table-default" id="example">
        <thead class="bg-gray-50 dark:bg-gray-800">
          <tr>
            <th>SN</th>
            <th>ID</th>
            <th>CLCode</th>
            <th>ISIN</th>
            <th>Company</th>
            <th>Secured/Unsecured</th>
            <th>Inconsistency Treatment</th>
            <th>Status</th>
            <th>Maker</th>
            <th>Actions</th>
          </tr>
        </thead>
      </table>
    </div>
      <div id="viewModal">
        <Modal
          v-show="isModalVisible"
          @close="closeModal"
          :compliance="viewcompliance"
        />
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3';
import 'bootstrap/dist/css/bootstrap.min.css';
import "datatables.net-dt/js/dataTables.dataTables";
import "datatables.net-dt/css/jquery.dataTables.min.css";
import $ from 'jquery';
import Modal from '../../../../../nova-components/ComplianceOverview/resources/js/pages/View.vue';
//vue.component('modal', require('../../../../../nova-components/ComplianceOverview/resources/js/pages/View.vue'));
var compliancesData = [];
export default {
  data() {
    return {
      compliances:[], 
      isModalVisible: false,
      viewcompliance: {},
      viewOnly: this.viewOnly,
      isApprover: this.isApprover
    }
  },
  components: {
    Modal,
  },
  methods: {
      closeModal() {
        this.isModalVisible = false;
      },
      view(id) {
        Nova.request().post('/nova-vendor/compliance-overview/view',{'id':id})
        .then(response => {
            if(response.data.status == 'success') {
              this.viewcompliance = response.data.compliance;
              this.isModalVisible = true;
            }            
        });
      },

      edit(id) {
        var url = "/admin/compliance-overview/edit/"+id;
        window.open(url, '_blank');
      },

      addCovenant(id) {
        var url = "/admin/compliance-overview/add/"+id;
        window.open(url, '_blank');
      },

      viewAttached(id) {
        var url = "/admin/covenants?id="+id;
        window.open(url, '_blank');
      },

      close(id) {
        if(confirm("Do you really want to close the compliance?")){

            Nova.request().post('/nova-vendor/compliance-overview/close',{'id':id})
            .then(response => {
                if(response.data.status == 'success') {
                  location.reload();
                }            
            })
            .catch(error => {
                console.log(error);
            })
        }
      },

      getCompliance() {

        setTimeout(() => {
            var i = 1;
            const self = this;
            var table = $(this.$refs.dataTable).DataTable({
                "serverSide": true,
                "processing": true,
                "ajax":{
                  "url": "/nova-vendor/compliance-overview/list",
                  "dataType": "json",
                  "type": "POST",
                  "data": {_token:$('meta[name="csrf-token"]').attr('content')}
                  },
                columns: [
                    { 
                      "render": function(data, type, full, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                      } 
                    },
                    {
                      data: 'id', 'orderable':false
                    },
                    { data: 'clcode' },
                    { data: 'isinValues', 'orderable':false },
                    { data: 'cname', 'orderable':false },
                    { data: 'secured' },
                    { data: 'inconsistencyTreatment' },
                    { data: 'complianceStatus','orderable':false },
                    { data: 'name','orderable':false },
                    { data: 'actions', 'orderable':false
                    },
                  ],
                  //order: [[8, 'asc']],
                  "deferRender": true,
                  rowCallback(row, data) {
                    $(row).on('click', '.view-placeholder',() => {
                      self.view(data.id);
                    });
                    $(row).on('click', '.edit-placeholder',() => {
                      self.edit(data.id);
                    });
                    $(row).on('click', '.attached-placeholder',() => {
                      self.viewAttached(data.id);
                    });
                    $(row).on('click', '.close-placeholder',() => {
                      self.close(data.id);
                    });
                    $(row).on('click', '.add-placeholder',() => {
                      self.addCovenant(data.id);
                    });
                  },
                  //stateSave: true,

            });

            

          },100);


        /*Nova.request().get('/nova-vendor/compliance-overview/list')
        .then(response => {
            this.compliances = response.data.compliances;
            compliancesData = response.data.compliances;
            this.viewOnly = response.data.viewOnly;
            this.isApprover = response.data.isApprover;

        });*/
      },


  },
  created:function(){
   this.getCompliance();
  },
  mounted() {
    //this.getCompliance();
  },
}
</script>

<style>
thead {
    position: sticky;
    top: 0px;
  }
</style>
