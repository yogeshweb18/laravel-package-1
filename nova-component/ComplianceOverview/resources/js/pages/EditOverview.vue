<template>
<div id="app">
   <h1 class="text-90 font-normal text-xl md:text-2xl mb-3">Edit Compliance</h1>
      <form id="compliance_form" class="dark:text-white text-lg opacity-70" enctype="multipart/form-data" style="min-height: 300px" @submit.prevent="saveData()">
      <div class="mb-8 space-y-4"></div>
       
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
              <label for="name-create-organization-text-field" class="label inline-block pt-2 leading-tight">CL Code <span class="text-red-500 text-sm">*</span>
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
            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
              <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight label">ISIN <span class="text-red-500 text-sm">*</span>
              </label>
            </div>
            <div class="md:mt-0 px-6 md:px-8 w-full md:w-3/5 md:py-5">
              <div class="" v-for="item in compliance.isinValues">
                <div class="isin_block" v-if="item.value != null">
                <input v-model="item.value" placeholder="Enter ISIN" type="text" name="isin[]" class="form-input1 form-input-bordered" disabled="disabled"> <button class="repeater" @click.prevent="removeIsin(item.value)">Remove -</button>
                </div> 
              </div>
            </div>
          </div>

          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
              <label for="" class="label inline-block pt-2 leading-tight">Start Date <span class="text-red-500 text-sm">*</span>
              </label>
            </div>
            <div class="md:mt-0 px-6 md:px-8 w-full md:w-3/5 md:py-5">
              <input type="date" v-model="compliance.startDate" class="form-control form-input form-input-bordered" id="name-create-organization-text-field" name="startDate" dusk="startDate" required="required"><!----><!----><!---->
            </div>
          </div>

          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
              <label for="" class="label inline-block pt-2 leading-tight">End Date <span class="text-red-500 text-sm">*</span>
              </label>
            </div>
            <div class="md:mt-0 px-6 md:px-8 w-full md:w-3/5 md:py-5">
              <input type="date" v-model="compliance.endDate" class="form-control form-input form-input-bordered" id="name-create-endDate-text-field" name="endDate" dusk="endDate" required="required"><!----><!----><!---->
            </div>
          </div>

          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
              <label for="" class="label inline-block pt-2 leading-tight">Priority <span class="text-red-500 text-sm">*</span>
              </label>
            </div>
            <div class="md:mt-0 px-6 md:px-8 w-full md:w-3/5 md:py-5">
            <select class="w-full form-control form-input-bordered select-box" v-model="compliance.priority" required>
              <option value="Low">Low</option>
              <option value="Medium">Medium</option>
              <option value="High">High</option>
            </select>
          </div>
          </div>

          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
              <label for="" class="label inline-block pt-2 leading-tight">Secured <span class="text-red-500 text-sm">*</span>
              </label>
            </div>
            <div class="md:mt-0 px-6 md:px-8 w-full md:w-3/5 md:py-5">
            <select class="w-full form-control form-input-bordered select-box" v-model="compliance.secured" required>
              <option value="secured">Secured</option>
              <option value="unsecured">Unsecured</option>
            </select>
          </div>
          </div>

          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
              <label for="" class="label inline-block pt-2 leading-tight">Inconsistency Treatment <span class="text-red-500 text-sm">*</span>
              </label>
            </div>
            <div class="md:mt-0 px-6 md:px-8 w-full md:w-3/5 md:py-5">
              <input type="text" placeholder="Enter Inconsistency Treatment" v-model="compliance.inconsistencyTreatment" class="w-full form-control form-input form-input-bordered" id="name-create-inconsistencyTreatment-text-field" name="inconsistencyTreatment" dusk="inconsistencyTreatment"  required="required"><!----><!----><!---->
            </div>
          </div>

          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
              <label for="" class="label inline-block pt-2 leading-tight">Client Reference <span class="text-red-500 text-sm">*</span>
              </label>
            </div>
            <div class="md:mt-0 px-6 md:px-8 w-full md:w-3/5 md:py-5">
            <select class="w-full form-control form-input-bordered select-box" v-model="compliance.clientReference" required>
                <option value="">Select Clients</option>
                <option v-for="data in compliance.clientData" :value="data.id">{{ data.name }}</option>
            </select>
          </div>
          </div>

          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
              <label for="" class="label inline-block pt-2 leading-tight">Mail CC <span class="text-red-500 text-sm">*</span>
              </label>
            </div>
            <div class="md:mt-0 px-6 md:px-8 w-full md:w-3/5 md:py-5">
              <input type="text" placeholder="Enter Mail CC" v-model="compliance.mailCC" class="w-full form-control form-input form-input-bordered" id="name-create-mailCC-text-field" name="mailCC" dusk="mailCC" required="required"><!----><!----><!---->
            </div>
          </div>


          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
              <label for="" class="label inline-block pt-2 leading-tight">Upload Document(s) <span class="text-red-500 text-sm"></span>
              </label>
            </div>
            <div class="md:mt-0 px-6 md:px-8 w-full md:w-3/5 md:py-5">
              <input type="file" multiple class="w-full form-control form-input form-input-bordered" id="name-create-documentUpload-text-field" name="newUpload[]" dusk="documentUpload" list="name-list" ref="files" @change="handleFilesUpload">
              <span class="upload-files" v-for="item in compliance.documentNames">
                <button @click.prevent="showFile(compliance.id,item)">{{item}}</button><button style="padding:10px;" v-on:click.prevent="removeFile(item)">x</button>
              </span>
            </div>
          </div>
          <div>
          	{{file}}
          </div>

          <button size="lg" align="center" component="button" dusk="create-button" type="submit" class="shadow relative bg-primary-500 hover:bg-primary-400 text-white dark:text-gray-900 cursor-pointer rounded text-sm font-bold focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600 inline-flex items-center justify-center h-9 px-3 shadow relative bg-primary-500 hover:bg-primary-400 text-white dark:text-gray-900" style="    margin: 30px;"><span class="">Update</span><!----></button>
      </div>  
    </form>
</div>
</template>
<script>
//import { Link } from '@inertiajs/inertia-vue3';
//import covenantData from '../../../../../nova-components/Newcompliance/resources/js/pages/Tool.vue';  
export default {
  name: 'app',
  props: {
            compliance: {},
        },
  data() {
    return {
    	'file':null,
      'clcodeData':[],
      'clcode_uploaded':0,
    }
  },
  methods: {
    saveData() {

      let formData = new FormData();
      const config = { headers: {'Content-Type': 'multipart/form-data'}};
      if(this.files && this.files.length) {
	      for( var i = 0; i < this.files.length; i++ ){
	          let file = this.files[i];
	          formData.append('newfiles[' + i + ']', file);
	      }
  	  }
      var isin = [];
      var c = 0;
      Object.values(this.compliance.isinValues).forEach(val => {
		  isin[c] = val.value;
		  c++;
		});
      formData.append('id', this.compliance.id);
      formData.append('clcode', this.compliance.clcode);
      formData.append('isin', isin);
      formData.append('docName', this.compliance.docName);
      formData.append('startDate', this.compliance.startDate);
      formData.append('endDate', this.compliance.endDate);
      formData.append('priority', this.compliance.priority);
      formData.append('secured', this.compliance.secured);
      formData.append('inconsistencyTreatment', this.compliance.inconsistencyTreatment);
      formData.append('clientReference', this.compliance.clientReference);
      formData.append('mailCC', this.compliance.mailCC);
      formData.append('oldfiles', JSON.stringify(this.compliance.documentNames));
      Nova.request().post('/nova-vendor/compliance-overview/update',formData,config)
      .then(
          response => {
            if(response.data.status == 'success') {
                window.location.href="/admin/compliance-overview";
              }
          }
      )
    },
    showFile(id,upload) {
    	/*Nova.request().post('/nova-vendor/compliance-overview/attachment',{'filename':filename})
      .then(response => {
          this.file = response.data;
          
      });*/
      window.open("/admin/compliance-overview/attachment?id="+id+"&file="+upload);
    },
    fetchClcode(){
      var isins = this.compliance.isinValues;
      this.compliance.isinValues = [];
      isins.forEach((value,i) => {
        this.compliance.isinValues.push({value}); 
      });
      Nova.request().get('/nova-vendor/compliance-overview/fetchClcode')
      .then(response => {
          this.clcodeData = response.data;
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
            this.clcode_uploaded = 1;
          }
      ).catch(err => {          
        if(err.response && err.response.data &&  err.response.data.errors){
          this.errors = err.response.data.errors;
          window.scrollTo(0,0);
        }
      
      });
    },
    loadIsin() {
      var clcode = this.compliance.clcode;
      Nova.request().post('/nova-vendor/compliance-overview/fetchIsins',{'clcode':clcode})
      .then(response => {
          console.log(response.data);  
          var result = response.data;
          this.compliance.isinValues = [];
          result.forEach((value,i) => {
            this.compliance.isinValues.push({value}); 
          });
        });
    },
    removeIsin(value) {
      const indexOfObject = this.compliance.isinValues.findIndex(object => {
        return object.value === value;
      });
      this.compliance.isinValues.splice(indexOfObject, 1);
    },
    removeFile(filename){
    	this.compliance.documentNames.pop({filename});
    },
    fetchClients(){
      Nova.request().get('/nova-vendor/compliance-overview/fetchClients')
      .then(response => {
          this.compliance.clientData = response.data;
          
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
    //
  },
}
</script>

<style>
/* Scoped Styles */
</style>
