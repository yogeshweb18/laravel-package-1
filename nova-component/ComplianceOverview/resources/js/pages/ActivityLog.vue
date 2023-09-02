<template>
  <div id="app">
    <div id="nav">
      <div class="w-full flex items-center mb-6">
        <h1 class="text-90" style="margin:0px;">Activity Logs</h1>
      </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow" style=
    "padding:20px;">
      <table class="w-full table-default" id="example">
        <thead class="bg-gray-50 dark:bg-gray-800">
          <tr>
            <th>Subject ID</th>
            <th>Description</th>
            <th>Modules</th>
            <th>Causer</th>
            <th>Created At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(compliance,index) in compliances">
            <td class="px-2 py-2 border-t border-gray-100">{{compliance.subject_id}}</td>
            <td class="px-2 py-2 border-t border-gray-100">{{compliance.description}}</td>
            <td class="px-2 py-2 border-t border-gray-100">{{compliance.subject_type}}</td>
            <td class="px-2 py-2 border-t border-gray-100">{{compliance.name}}</td>
            <td class="px-2 py-2 border-t border-gray-100">{{compliance.created_at}}</td>
            <td class="actions-icons">

              <button @click.prevent="view(compliance.id)"><img src="/img/view_file.png" title="View" /></button>

            </td>
          </tr>           
        </tbody>
      </table>
    </div>
      <div id="viewModal">
        <Modal
          v-show="isModalVisible"
          @close="closeModal"
          :log="viewcompliance"
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
import Modal from '../../../../../nova-components/ComplianceOverview/resources/js/pages/LogView.vue';
//vue.component('modal', require('../../../../../nova-components/ComplianceOverview/resources/js/pages/View.vue'));
var compliancesData = [];
export default {
  data() {
    return {
      isModalVisible: false,
      viewcompliance: {},
    }
  },
  components: {
    Modal,
  },
  props: {
            compliances: {},
        },
  methods: {
      closeModal() {
        this.isModalVisible = false;
      },
      view(id) {
              //this.viewcompliance = compliance;
              //this.isModalVisible = true;
        Nova.request().post('/nova-vendor/compliance-overview/viewLog',{'id':id})
        .then(response => {
            if(response.data) {
              this.viewcompliance = response.data;
              this.isModalVisible = true;
            }            
        });
      },
      getCompliance() {
            //this.compliances = response.data.compliances;
            //compliancesData = response.data.compliances;
            //this.viewOnly = response.data.viewOnly;
            setTimeout(() => {
              var i = 1;
              var table = $('#example').DataTable({
                  columns: [
                      { data: 'subject_id' },
                      { data: 'description' },
                      { data: 'subject_type' },
                      { data: 'name' },
                      { data: 'created_at' },
                      { title: "", "defaultContent": ""},
                    ],
                    "columnDefs": [
                        { "targets": [2,3], "searchable": false }
                    ],
                    order: [[4, 'desc']],
                    stateSave: true,

              });

            },100);
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
