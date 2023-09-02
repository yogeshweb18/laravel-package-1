<template>
<div id="app">
   <h1 class="text-90 font-normal text-xl md:text-2xl mb-3">Create Compliance</h1>
      <form id="compliance_form" class="dark:text-white text-lg opacity-70" enctype="multipart/form-data" style="min-height: 300px" @submit.prevent="saveData()">
        <input-hidden :value="csrfToken" name="_token"/>
      <div class="mb-8 space-y-4"></div>
       
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row error-message" index="0" ref="errors">
            <div v-for="error,index in errors">
              {{error[0]}}
            </div>
          </div>
          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
            <div class="px-6 md:px-8 w-full md:w-1/5 md:py-5">
              <label for="name-create-organization-text-field" class="inline-block leading-tight label">CL Code <span class="text-red-500 text-sm">*</span>
              </label>
            </div>
            <div class="md:mt-0 px-6 md:px-8 w-full md:w-3/5 md:py-5">
              <select class="w-full form-control form-input-bordered select-box" v-model="compliance.clcode" required @change.prevent="loadIsin()">
                <option value="">Select Clcodes</option>
                <option v-for="data in clcodeData" :value="data.clcode">{{ data.clcode }}</option>
              </select>
              <!--span>
                <vue-csv-import v-model="mappedCsv" :fields="fields">
                    <vue-csv-errors></vue-csv-errors>
                    <div>
                        <vue-csv-input class="test"></vue-csv-input>
                    </div>
                    <vue-csv-map></vue-csv-map>
                </vue-csv-import>
              <button @click.prevent="submitclcode()">Import</button>
              </span-->
              <span>
                <input type="file" name="csv" class="w-full form-control form-input form-input-bordered" ref="clcodefile" />
                <button @click.prevent="submitclcode()" class="repeater">Import</button>
              </span>
              <span>
               <button class="repeater"><a href="/uploads/ISIN_CL_Code_Mapping.csv" >Download Sample</a></button>
              </span>
              <div><p v-if="clcode_uploaded == 1" class="success-message"><img src="/img/tick.png" class="tick-icon">ClCode imported successfully</p></div>
            </div>

          </div>
          
          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
            <div class="px-6 md:px-8 w-full md:w-1/5 md:py-5">
              <label for="name-create-organization-text-field" class="inline-block leading-tight label">ISIN 
              </label>
            </div>
            <div class="md:mt-0 px-6 md:px-8 w-full md:w-3/5 md:py-5">
              <div class="" v-for="item in compliance.isin">
                <div class="isin_block" v-if="item.value != null">
                <input v-model="item.value" placeholder="Enter ISIN" type="text" name="isin[]" class="form-input1 form-input-bordered" disabled="disabled"> <button class="repeater" @click.prevent="removeIsin(item.value)">Remove -</button>
                </div> 
              </div>
            </div>
          </div>

          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
            <div class="px-6 md:px-8 w-full md:w-1/5 md:py-5">
              <label for="" class="inline-block leading-tight label">Start Date <span class="text-red-500 text-sm">*</span>
              </label>
            </div>
            <div class="md:mt-0 px-6 md:px-8 w-full md:w-3/5 md:py-5">
              <input type="date" v-model="compliance.startDate" class="date-field form-input form-input-bordered" id="name-create-organization-text-field" name="startDate" dusk="startDate" required="required"><!----><!----><!---->
            </div>
          </div>

          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
            <div class="px-6 md:px-8 w-full md:w-1/5 md:py-5">
              <label for="" class="inline-block leading-tight label">End Date <span class="text-red-500 text-sm">*</span>
              </label>
            </div>
            <div class="md:mt-0 px-6 md:px-8 w-full md:w-3/5 md:py-5">
              <input type="date" v-model="compliance.endDate" class="date-field form-input form-input-bordered" id="name-create-endDate-text-field" name="endDate" dusk="endDate" required="required"><!----><!----><!---->
            </div>
          </div>

          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
            <div class="px-6 md:px-8 w-full md:w-1/5 md:py-5">
              <label for="" class="inline-block leading-tight label">Priority <span class="text-red-500 text-sm">*</span>
              </label>
            </div>
            <div class="md:mt-0 px-6 md:px-8 w-full md:w-3/5 md:py-5">
            <select class="w-full form-input-bordered dropdown-field" v-model="compliance.priority" required>
              <option value="Low">Low</option>
              <option value="Medium">Medium</option>
              <option value="High" selected="selected">High</option>
            </select>
          </div>
          </div>

          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
            <div class="px-6 md:px-8 w-full md:w-1/5 md:py-5">
              <label for="" class="inline-block leading-tight label">Secured <span class="text-red-500 text-sm">*</span>
              </label>
            </div>
            <div class="md:mt-0 px-6 md:px-8 w-full md:w-3/5 md:py-5">
            <select class="w-full form-input-bordered dropdown-field" v-model="compliance.secured" required>
              <option value="" disabled selected>Select</option>
              <option value="secured">Secured</option>
              <option value="unsecured">Unsecured</option>
            </select>
          </div>
          </div>

          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
            <div class="px-6 md:px-8 w-full md:w-1/5 md:py-5">
              <label for="" class="inline-block leading-tight label">Inconsistency Treatment <span class="text-red-500 text-sm">*</span>
              </label>
            </div>
            <div class="md:mt-0 px-6 md:px-8 w-full md:w-3/5 md:py-5">
              <input type="text" placeholder="Enter Inconsistency Treatment" v-model="compliance.inconsistencyTreatment" class="w-full form-control form-input form-input-bordered" id="name-create-inconsistencyTreatment-text-field" name="inconsistencyTreatment" dusk="inconsistencyTreatment" list="name-list" required="required"><!----><!----><!---->
            </div>
          </div>

          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
            <div class="px-6 md:px-8 w-full md:w-1/5 md:py-5">
              <label for="" class="inline-block leading-tight label">Client Reference <span class="text-red-500 text-sm">*</span>
              </label>
            </div>
            <div class="px-6 md:px-8 w-full md:w-3/5 md:py-5">
            <select class="w-full form-control form-input-bordered select-box" v-model="compliance.clientReference" required>
                <option value="">Select Clients</option>
                <option v-for="data in compliance.clientData" :value="data.id">{{ data.name }}</option>
            </select>
          </div>
          </div>

          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
            <div class="px-6 md:px-8 w-full md:w-1/5 md:py-5">
              <label for="" class="inline-block leading-tight label">Mail CC <span class="text-red-500 text-sm">*</span>
              </label>
            </div>
            <div class="md:mt-0 px-6 md:px-8 w-full md:w-3/5 md:py-5">
              <input type="text" placeholder="Enter Mail CC" v-model="compliance.mailCC" class="w-full form-control form-input form-input-bordered" id="name-create-mailCC-text-field" name="mailCC" dusk="mailCC" list="name-list" required="required"><!----><!----><!---->
            </div>
          </div>


          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
            <div class="px-6 md:px-8 w-full md:w-1/5 md:py-5">
              <label for="" class="inline-block leading-tight label">Upload Document(s) <span class="text-red-500 text-sm">*</span>
              </label>
            </div>
            <div class="md:mt-0 px-6 md:px-8 w-full md:w-3/5 md:py-5">
              <input type="file" multiple class="w-full form-control form-input form-input-bordered" id="name-create-documentUpload-text-field" name="documentUpload[]" dusk="documentUpload" list="name-list" ref="files" @change="handleFilesUpload" required="required">
            </div>
          </div>


          <button size="lg" align="center" component="button" dusk="create-button" type="submit" class="shadow relative bg-primary-500 hover:bg-primary-400 text-white dark:text-gray-900 cursor-pointer rounded text-sm font-bold focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600 inline-flex items-center justify-center h-9 px-3 shadow relative bg-primary-500 hover:bg-primary-400 text-white dark:text-gray-900" style="    margin: 30px;"><span class="">Continue</span><!----></button>
      </div>  
    </form>
</div>
</template>
<script>
import {VueCsvSubmit,VueCsvMap, VueCsvInput, VueCsvErrors, VueCsvImport} from 'vue-csv-import';
//import covenantData from '../../../../../nova-components/Newcompliance/resources/js/pages/Tool.vue';  
export default {
  name: 'app',
  data() {
    return {
      'compliance' : {
        'clcode':'',
        'isin':[{}],
        'docName':'',
        'startDate':'',
        'endDate':'',
        'priority': 'High',
        'secured': '',
        'inconsistencyTreatment':'',
        'clientReference':'',
        'mailCC':'',
        'clientData':'',
        'documentUpload':[],
        'files': '',
        'nextComponent':'test',
      },
      'csrfToken': null,
      'clcodeData':[],
      'clcode_uploaded':0,
      'errors': '',
      'message':'',
       'mappedCsv': null,
       'fields': {
                    clcode: {required: true, label: "CLcode"},
                    isin: {required: true, label: "ISIN"},
                },
    }
  },
  components: {
      VueCsvImport,
      VueCsvInput,
      VueCsvSubmit,
      VueCsvErrors,
      VueCsvMap
    },
  methods: {
    saveData() {
      let formData = new FormData();
      const config = { headers: {'Content-Type': 'multipart/form-data'}};
      for( var i = 0; i < this.files.length; i++ ){
          let file = this.files[i];
          formData.append('files[' + i + ']', file);
      }
      var isin = [];
      var c = 0;
      Object.values(this.compliance.isin).forEach(val => {
        formData.append('isin[' + c + ']', val.value);
        c++;
      });
      formData.append('clcode', this.compliance.clcode);
      formData.append('docName', this.compliance.docName);
      formData.append('startDate', this.compliance.startDate);
      formData.append('endDate', this.compliance.endDate);
      formData.append('priority', this.compliance.priority);
      formData.append('secured', this.compliance.secured);
      formData.append('inconsistencyTreatment', this.compliance.inconsistencyTreatment);
      formData.append('clientReference', this.compliance.clientReference);
      formData.append('mailCC', this.compliance.mailCC);

        Nova.request().post('/nova-vendor/compliance-overview/store',formData,config)
        .then(
            response => {

              if(response.data.status == 'success' && response.data.complianceId != '') {
                    this.$emit('clicked', 'covenantData',response.data.complianceId)
              }

            }
        ).catch(err => {          
          if(err.response && err.response.data &&  err.response.data.errors){
            this.errors = err.response.data.errors;
            window.scrollTo(0,0);
          }
        
        });
      
      
    },
    submitclcode() {
      let formData = new FormData();
      const config = { headers: {'Content-Type': 'multipart/form-data'}};
      let clcodefile = this.$refs.clcodefile.files[0];
      formData.append('clcodefile[0]', clcodefile);
      Nova.request().post('/nova-vendor/compliance-overview/clcode-import',formData,config)
      .then(
          response => {

            if(response.data.status == 'success' && response.data.complianceId != '') {
                  this.$emit('clicked', 'covenantData',response.data.complianceId)
            }
            this.clcode_uploaded = 1;
          }
      ).catch(err => {          
        if(err.response && err.response.data &&  err.response.data.errors){
          this.errors = err.response.data.errors;
          window.scrollTo(0,0);
        }
      
      });
    },
    addInput() {
      this.compliance.isin.push({});
    },
    removeIsin(value) {
      const indexOfObject = this.compliance.isin.findIndex(object => {
        return object.value === value;
      });
      this.compliance.isin.splice(indexOfObject, 1);
    },
    fetchClients(){
      Nova.request().get('/nova-vendor/compliance-overview/fetchClients')
      .then(response => {
          this.compliance.clientData = response.data;
          
      });
    },
    fetchClcode(){
      Nova.request().get('/nova-vendor/compliance-overview/fetchClcode')
      .then(response => {
          this.clcodeData = response.data;
          
      });
    },
    loadIsin() {
      var clcode = this.compliance.clcode;
      Nova.request().post('/nova-vendor/compliance-overview/fetchIsins',{'clcode':clcode})
      .then(response => {
          console.log(response.data);  
          var result = response.data;
          this.compliance.isin = [];
          result.forEach((value,i) => {
            this.compliance.isin.push({value}); 
          });
        });
    },
    handleFilesUpload(){
        this.files = this.$refs.files.files;
    },
  },
  created:function(){
   this.fetchClcode();
   this.fetchClients();
  },
  mounted() {
    this.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
  },
}
</script>

<style>
/* Scoped Styles */
</style>
