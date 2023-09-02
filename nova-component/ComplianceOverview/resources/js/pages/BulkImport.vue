<template>
  <div>
    <Head title="Bulkimport" />

    <Heading class="mb-6">Bulk Import</Heading>

    <Card
      class="flex flex-col items-center justify-center"
      style="min-height: 300px"
    >
    
      <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
        <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:py-5" style="width:50%">
          <label for="" class="inline-block pt-2 leading-tight">Upload CSV File <span class="text-red-500 text-sm">*</span>
          </label>
        </div>
        <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 w-full md:py-5">
          <input type="file" multiple class="w-full form-control form-input form-input-bordered" id="name-create-documentUpload-text-field" name="documentUpload[]" dusk="documentUpload" list="name-list" required="required" ref="files" @change="handleFilesUpload">
          <span><button><a href="/uploads/covenant-data-upload.csv" >Download Sample</a></button>
        </span>
        </div>
      </div>
      <div class="spinner" v-if="uploading">
        <img src="/img/spinner.gif" alt="spinner" />
      </div>
    </Card>
  </div>
</template>

<script>
import Papa from 'papaparse';

export default {
  mounted() {
    
    //
  },
  methods: {
    handleFilesUpload(e) {
      let files = e.target.files || e.dataTransfer.files;
      this.files = this.$refs.files.files[0];
      var filesize = files[0].size / 1024 / 1024;
      if (files[0].type != 'text/csv') {
        Nova.error('Please select only CSV file');
        return;
      }
      if (filesize > 1) {
        Nova.error('Error! File is too large');
        return;
      }
      Papa.parse(files[0], {
        header: true,
        complete: this.onComplete,
        // error: undefined,
        skipEmptyLines: true,
        delimitersToGuess: [
          ",",
          "\t",
          "|",
          ";",
          Papa.RECORD_SEP,
          Papa.UNIT_SEP,
        ],
      });
    },

    onComplete(results, file) {

      var data_fields = ['cmp_id','clcode', 'isin', 'clientReference', 'secured', 'frequency', 'startDate', 'endDate', 'inconsistencyTreatment','type', 'subType', 'comments', 'targetValue', 'priority', 'mailCC', 'user', 'description', 'isCustomCovenant','period_of_replenishment_after_shortfall','maintained_as','manner_of_creation','account_number','additional_details','amount','period_for_renewal_before_expiry','rating_symbol','suffix','bank_account_number','custom_parameter','custom_value','custom_child_dueDate','custom_child','applicableMonth','covenantStartDate','covenantEndDate','period_for_cersai_from_security_creation','period_for_chg_from_security_creation'];
      var mandatory_fields = ['cmp_id','clcode', 'clientReference', 'secured', 'frequency', 'startDate', 'endDate', 'type', 'subType', 'user', 'isCustomCovenant','applicableMonth','covenantStartDate','covenantEndDate','inconsistencyTreatment'];

      const formDataObj = Object.fromEntries(results.data.entries());
      let form_data = new FormData();
      
      let result = {}
      for (let row of results.data) {
        for(let key2 of mandatory_fields) {
           if (!row[key2] || row[key2] === '' ) {
             Nova.error('Error: Some mandatory field is missing from the file');
             return;
           }
        }
      }
      form_data.append('csvfile', this.files)
      /*for (let field of data_fields) {
        form_data.append(field, JSON.stringify(result[field]))
      }*/
      const config = { headers: {'Content-Type': 'multipart/form-data'}};
      this.uploading = true;
      Nova.request()
        .post('/nova-vendor/compliance-overview/bulkUpload', form_data,config)
        .then((res) => {
          this.uploading = false;
          console.log(res.data.total_count);
          console.log(res.data.data_uploaded);
          if(res.data.save_status == 'success') {
            Nova.success('Success: File imported successfully');
          }else {
            Nova.error('Error: Incorrect file is uploaded');
             return;
          }
        });
    },
  },
  data() {
    return {
      'files': '',
      'uploading': false,
    }
  }
}
</script>

<style>
.spinner img {
  width: 20%;
  height: auto;
}
</style>
