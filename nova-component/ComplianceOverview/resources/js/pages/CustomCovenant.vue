<template>
  <div id="customCovenant">
  	<h1>Custom Covenant</h1>
  		<form id="compliance_covenant_form" class="dark:text-white text-lg opacity-70" style="min-height: 300px" @submit.prevent="saveCustomData()">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
                <label for="" class="inline-block pt-2 leading-tight">Type <span class="text-red-500 text-sm">*</span>
                </label>
              </div>
              <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 w-full md:w-3/5 md:py-5">
                  <label for="" class="inline-block pt-2 leading-tight">{{customCovenant.type}}
                </label>
              </div>
          </div>
          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
              <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
                <label for="" class="inline-block pt-2 leading-tight">Sub-Type <span class="text-red-500 text-sm">*</span>
                </label>
              </div>
              <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 w-full md:w-3/5 md:py-5">
                <input type="text" placeholder="" v-model="customCovenant.subType" class="w-full form-control form-input form-input-bordered" id="name-create-docName-text-field" name="docName" dusk="docName" list="name-list" required="required"><!----><!----><!---->
              </div>
          </div>
          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
              <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
                <label for="" class="inline-block pt-2 leading-tight">Frequency <span class="text-red-500 text-sm">*</span>
                </label>
              </div>
              <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 w-full md:w-3/5 md:py-5">
                <div class="text-left">
                          <select class="w-full form-control form-input-bordered select-box" v-model="customCovenant.frequency" required>
                          <option value="Monthly">Monthly</option>
                          <option value="Quarterly">Quarterly</option>
                          <option value="Half Yearly">Half Yearly</option>
                          <option value="Annually">Annually</option>
                          <option value="One Time">One Time</option>
                          <option value="Event Based">Event Based</option>
                        </select>
                       </div>
              </div>
          </div>
          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row">
                    <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
                      <label for="" class="inline-block pt-2 leading-tight">Start Date <span class="text-red-500 text-sm">*</span>
                      </label>
                    </div>
                    <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 w-full md:w-3/5 md:py-5">
                      <input v-model="customCovenant.startDate" placeholder="" type="date" name="" class="date-field" required="required">
                    </div>
                </div>
          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row">
                    <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
                      <label for="" class="inline-block pt-2 leading-tight">Due Date <span class="text-red-500 text-sm">*</span>
                      </label>
                    </div>
                    <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 w-full md:w-3/5 md:py-5">
                      <input v-model="customCovenant.dueDate" placeholder="" type="date" name="" class="date-field" required="required">
                    </div>
                </div>
                <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row">
                    <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
                      <label for="" class="inline-block pt-2 leading-tight">Applicable Month <span class="text-red-500 text-sm">*</span>
                      </label>
                    </div>
                    <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 w-full md:w-3/5 md:py-5">
                      <select class="form-input-bordered select-box form-control" v-model="customCovenant.applicableMonth" required>
                            <option v-for="data in this.months" :value="data">{{data}}</option>
                        </select>
                    </div>
                </div>
          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
              <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
                <label for="" class="inline-block pt-2 leading-tight">Covenant Description <span class="text-red-500 text-sm">*</span>
                </label>
              </div>
              <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 w-full md:w-3/5 md:py-5">
                <input type="text" placeholder="" v-model="customCovenant.description" class="w-full form-control form-input form-input-bordered" id="name-create-docName-text-field" name="docName" dusk="docName" list="name-list" required="required"><!----><!----><!---->
              </div>
          </div>
          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
              <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
                <label for="" class="inline-block pt-2 leading-tight">Target Value <span class="text-red-500 text-sm">*</span>
                </label>
              </div>
              <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 w-full md:w-3/5 md:py-5">
                <input type="text" placeholder="" v-model="customCovenant.targetValue" class="w-full form-control form-input form-input-bordered" id="name-create-docName-text-field" name="docName" dusk="docName" list="name-list" required="required"><!----><!----><!---->
              </div>
          </div>
          <div class="" index="0" v-for="item in customCovenant.covenantParameters">
            <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
                <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
                  <label for="" class="inline-block pt-2 leading-tight">Parameter <span class="text-red-500 text-sm">*</span>
                  </label>
                </div>
                <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 w-full md:w-3/5 md:py-5">
                  <input type="text" placeholder="" v-model="item.label" class="w-full form-control form-input form-input-bordered" id="name-create-docName-text-field" name="docName" dusk="docName" list="name-list" required="required"><!----><!----><!---->
                </div>
            </div>
          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
              <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
                <label for="" class="inline-block pt-2 leading-tight">Parameter Details <span class="text-red-500 text-sm">*</span>
                </label>
              </div>
              <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 w-full md:w-3/5 md:py-5">
                <input type="text" placeholder="" v-model="item.value" class="w-full form-control form-input form-input-bordered" id="name-create-docName-text-field" required="required"><!----><!----><!---->
              </div>
          </div>
          </div>
          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
              <label for="" class="inline-block pt-2 leading-tight">Status <span class="text-red-500 text-sm">*</span>
              </label>
            </div>
            <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 w-full md:w-3/5 md:py-5">
              <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">Active</label>      
            </div>
          </div>
          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
              <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
                <label for="" class="inline-block pt-2 leading-tight">Breach Condition <span class="text-red-500 text-sm">*</span>
                </label>
              </div>
              <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 w-full md:w-3/5 md:py-5">
                <input type="text" placeholder="" v-model="customCovenant.condition" class="w-full form-control form-input form-input-bordered" id="name-create-docName-text-field" name="docName" dusk="docName" list="name-list" required="required"><!----><!----><!---->
              </div>
          </div>
      </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow" style="margin-top:10px;">
          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row">
            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
                <label for="" class="inline-block pt-2 leading-tight">Child Covenant (If any)
                </label>
            </div>
            <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 w-full md:w-3/5 md:py-5">
            <span class="w-full form-input">  
              <input type="radio" v-model="customCovenant.isChild" v-bind:value="1" @change="isChild($event)">Yes
              <input type="radio" v-model="customCovenant.isChild" v-bind:value="0" @change="isChild($event)">No
            </span>
          </div>
          </div>

            <div id="childBlock" style="display:none;">
              <div class="" index="0" v-for="item in customCovenant.childCovenant">
                <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row">
                    <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
                      <label for="" class="inline-block pt-2 leading-tight">Child Covenant <span class="text-red-500 text-sm">*</span>
                      </label>
                    </div>
                    <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 w-full md:w-3/5 md:py-5">
                      <input v-model="item.label" placeholder="" type="text" name="" id="customChild" class="w-full form-control form-input form-input-bordered">
                    </div>
                </div>
                <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row">
                    <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
                      <label for="" class="inline-block pt-2 leading-tight">Due Date <span class="text-red-500 text-sm">*</span>
                      </label>
                    </div>
                    <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 w-full md:w-3/5 md:py-5">
                      <input v-model="item.value" placeholder="" type="date" id="customChildDuedate" name="" class="w-full form-control form-input form-input-bordered">
                    </div>
                </div>            
              </div>
          </div>
        </div>

          <button size="lg" align="center" component="button" dusk="create-button" type="submit" class="shadow relative bg-primary-500 hover:bg-primary-400 text-white dark:text-gray-900 cursor-pointer rounded text-sm font-bold focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600 inline-flex items-center justify-center h-9 px-3 shadow relative bg-primary-500 hover:bg-primary-400 text-white dark:text-gray-900" style="    margin: 30px;"><span class="">Submit</span><!----></button>

    </form> 
  </div>
</template>
<script>
export default {
  name: 'app',
  data() {
    return {
      'customCovenant' : {
        'type':this.type,
        'id': '',
        'isCustomCovenant' : 1,
        'startDate': '',
        'dueDate': '',
        'frequency' : '',
        'subType' : '',
        'isChild': '0',
        'childCovenant':[{
            'key':'custom_child',
            'label':'',
            'value':'',
            'type':'date',
        }],
        'covenantParameters': [{
            'key':'custom_parameter',
            'label':'',
            'value':'',
            'type':'text',
        }],
      },
      'value': this.value,
      'months': ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]
   }
  },
  props: ['type'],
  methods: {
    saveCustomData() {
       this.$emit('clicked',this.customCovenant);
    },
    isChild(event) {
      var data = event.target.value;
      if(data == 1) {
        document.getElementById("childBlock").style.display = "block";
        document.getElementById("customChild").setAttribute("required", "");
        document.getElementById("customChildDuedate").setAttribute("required", "");
      }
      else{
        document.getElementById("childBlock").style.display = "none";
        document.getElementById("customChild").removeAttribute("required");
        document.getElementById("customChildDuedate").removeAttribute("required");
      }
    },
    addChild() {
      this.customCovenant.childCovenant.push({});
    },
    removeChild() {
      this.customCovenant.childCovenant.pop({});
    },

    isFail(event) {
      var data = event.target.value;
      if(data == 1) {
        document.getElementById("failBlock").style.display = "block";
      }
      else{
        document.getElementById("failBlock").style.display = "none";
      }
    },
    addFail() {
      this.customCovenant.failCovenant.push({});
    },
    removeFail() {
      this.customCovenant.failCovenant.pop({});
    },
  }
	
}
</script>