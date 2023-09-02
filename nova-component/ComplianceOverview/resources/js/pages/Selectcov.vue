<template>

  <div id="selCovenant" v-if="showCustomCovenant==0">
		<h1>Select Covenant</h1>  
  	<form id="compliance_covenant_form" class="dark:text-white text-lg opacity-70" style="min-height: 300px" @submit.prevent="saveCovenantData()">
  		<div class="bg-white dark:bg-gray-800 rounded-lg shadow">
  		  <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
              <label for="" class="inline-block label leading-tight">Select Covenant<span class="text-red-500 text-sm">*</span>
              </label>
            </div>
            <div class="md:mt-0 md:px-8 w-full md:w-3/5 md:py-5">
            	<span>
        		    <Multiselect
				      v-model="value" :options="covenantOptions" id="ms1" :track-by="id" @deselect="removeType" @select="fetchSubtypes" :multiple="true" :searchable="true" ref="multiselect" :allow-empty="false" :hide-selected="true" placeholder="Select" mode="tags"
				    />
	        	</span>
          	</div>
          </div>
          <div class="overflow-hidden overflow-x-auto relative">
		    <div v-for="infoArray in covenantDetails.covenantInfo">
		    	<div v-for="(details,mainType) in infoArray">
		    	<div class="covenanttype-name"><span>{{mainType}}</span><span><a href="javascript:void(0)" class="hyperlinks" @click.prevent="addCustomCov(mainType)">Add Custom Covenant</a></span></div>
		    		<div v-if="typeof details !== 'undefined' && Object.keys(details).length === 0">
		    			<span style="color:red;">All standard covenants of this type is already added.</span>
		    		</div>
				   <table v-else class="w-full table-default" cellpadding="0" cellspacing="0" data-testid="resource-table">
				      <thead class="bg-gray-50 dark:bg-gray-800">
				         <tr>
				            <th class="td-fit uppercase text-xxs text-gray-500 pl-5 pr-2 py-2"><span class="sr-only">Selected Resources</span></th>
				            <th class="text-left px-2 uppercase text-gray-500 text-xxs py-2"><span>Subtype</span></th>
				            <th class="text-left px-2 uppercase text-gray-500 text-xxs py-2"><span>Comments</span></th>
				            <th class="text-left px-2 uppercase text-gray-500 text-xxs py-2"><span>Frequency</span></th>
				            <th class="text-left px-2 uppercase text-gray-500 text-xxs py-2"><span>Start - End Date</span></th>
				            <th class="text-left px-2 uppercase text-gray-500 text-xxs py-2"><span>Appl. Month</span></th>
				            <th class="text-left px-2 uppercase text-gray-500 text-xxs py-2"><span>Target</span></th>
				            <th class="text-left px-2 uppercase text-gray-500 text-xxs py-2"><span>Parameters</span></th>
				            <th class="text-left px-2 uppercase text-gray-500 text-xxs py-2"><span>Covenant Details</span></th>
				         </tr>
				      </thead>
				      <tbody>
				         <tr dusk="3-row" class="group" v-for="(covData,index) in details" >
				            <td class="py-2 border-t border-gray-100 dark:border-gray-700 px-2 cursor-pointer td-fit pl-5 pr-5 dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900"><input type="checkbox" name="selectCov[]" :id="covData.id" class="checkbox" @change="check($event)" aria-label="Select Resource 3" data-testid="clients-items-0-checkbox" dusk="3-checkbox" v-model="covData.selectedCovenant"></td>
				            <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
				               <div class="text-left"><input type="hidden" v-model="covData.subType" /><span class="text-90">{{covData.subType}}</span></div>
				               <div class="text-left">
				               	<textarea v-model="covData.description" :data-rowid="covData.id" class="enter-description" placeholder="Enter description">
				               		
				               	</textarea>
				               </div>
				            </td>
				            <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
				               <div class="text-left">
				               	<input type="text" placeholder="" name="covfrequency" class="w-full form-control form-input form-input-bordered" id="description" dusk="description" v-model="covData.comment" />
				               </div>
				            </td>
				            <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
				               <div class="text-left">
				                  <select class="w-full form-control form-input-bordered select-box" v-model="covData.frequency" :data-rowid="covData.id">
						              <option value="Monthly">Monthly</option>
						              <option value="Quarterly">Quarterly</option>
						              <option value="Half Yearly">Half Yearly</option>
						              <option value="Annually">Annually</option>
						              <option value="One Time">One Time</option>
						              <option value="Event Based">Event Based</option>
						            </select>
				               </div>
				            </td>
				            <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
				               <div class="text-left"><span><input type="date" name="startDate" class="w-full form-control form-input form-input-bordered" id="name-create-startDate-text-field" v-model="covData.startDate" :data-rowid="covData.id" /></span></div>
				               	<div class="text-left"><span><input type="date" name="dueDate" class="w-full form-control form-input form-input-bordered" id="name-create-dueDate-text-field" dusk="frequency" v-model="covData.dueDate" :data-rowid="covData.id" /></span></div>
				            </td>
				            <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
				               <div class="text-left">
				               	<span>
				               		<select class="form-input-bordered select-box" v-model="covData.applicableMonth" :data-rowid="covData.id">
						                <option v-for="data in this.months" :value="data">{{data}}</option>
						            </select>
				               	</span>
				               </div>
				            </td>
				            <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
				               <div class="text-left"><span class="text-90">
				               	<input type="text" placeholder="Target Value" name="targetValue" class="w-full form-control form-input form-input-bordered" id="name-create-targetValue-text-field" dusk="frequency" list="name-list" v-model="covData.targetValue" :data-rowid="covData.id" /></span></div>
				            </td>
				            <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
				               <div class="text-left">
				               	<span v-if="typeof covData.covenantParameters !== 'undefined' && Object.keys(covData.covenantParameters).length === 0">NA</span>
				               	<div v-if="typeof covData.covenantParameters !== 'undefined' && Object.keys(covData.covenantParameters).length>0" v-for="param in covData.covenantParameters">
				               		<input type="hidden" name="paramKey[]" class="w-full form-control form-input form-input-bordered" id="name-create-paramKey-text-field" dusk="paramKey" list="name-list" disabled v-model="param.key" />
				               		<select class="w-full form-input-bordered select-box" v-model="param.value" 
				               		v-if="param.type=='dropdown'" :data-rowid="covData.id">
						                <option value="" selected>{{param.label}}</option>
						                <option v-for="data in param.option" :value="data">{{data}}</option>
						            </select>
				               		<div v-if="param.type=='text'">
				               			<input type="hidden" name="paramKey[]" class="w-full form-control form-input form-input-bordered" id="name-create-paramKey-text-field" dusk="paramKey" list="name-list" disabled v-model="param.key" /><label>{{param.label}}:</label> 
				               			<input type="text" placeholder="" name="paramValue[]" class="form-control form-input form-input-bordered" id="name-create-paramValue-text-field" dusk="frequency" list="name-list" v-model="param.value" />
				               		</div>
				               		<div v-if="param.type=='optional'">
				               			<input type="hidden" name="paramKey[]" class="w-full form-control form-input form-input-bordered" id="name-create-paramKey-text-field" dusk="paramKey" list="name-list" disabled v-model="param.key" /><label>{{param.label}}:</label> 
				               			<input type="text" placeholder="" name="paramValue[]" class="form-control form-input form-input-bordered" id="name-create-paramValue-text-field" dusk="frequency" list="name-list" v-model="param.value" />
				               		</div>
				               </div>
				           		</div>
				            </td>
				            <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
				               <div class="text-left">
				               		<span v-if="typeof covData.childCovenant !== 'undefined' && covData.childCovenant.length<=0">NA</span>
				               		<div v-else="typeof covData.childCovenant !== 'undefined' && covData.childCovenant.length>0" v-for="child in covData.childCovenant">
				               			<span v-if="child.label != ''">
				               			<label>{{child.label}}:</label>
				               			<input type="date" placeholder="" name="child_covenant_value" class="form-control form-input form-input-bordered" id="child_covenant_value" dusk="child_covenant_value" :data-rowid="covData.id" v-model="child.value" />
				               			</span>
				               		</div>
				            	</div>
				            </td>
				         </tr>
				      </tbody>
				   </table>
				</div>
				</div>
			</div>
<button size="lg" align="center" component="button" dusk="create-button" type="submit" id="submitCov" class="shadow relative bg-primary-500 hover:bg-primary-400 text-white dark:text-gray-900 cursor-pointer rounded text-sm font-bold focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600 inline-flex items-center justify-center h-9 px-3 shadow relative bg-primary-500 hover:bg-primary-400 text-white dark:text-gray-900" style="display: none; margin: 30px;"><span class="">Continue</span><!----></button>
  		</div>
  	</form> 	
  </div>
  <div v-else-if="showCustomCovenant==1">
  	<KeepAlive>
  		<component v-bind:is="comp" @clicked="showParent" :type="selType"/>
  	</KeepAlive>
  </div>
</template>
<script>
import Multiselect from '../../../../../nova-components/ComplianceOverview/node_modules/@vueform/multiselect';
import customCovenant from '../../../../../nova-components/ComplianceOverview/resources/js/pages/CustomCovenant.vue';

export default {
  name: 'app',
  components: {
      Multiselect,
      customCovenant,
    },
  data() {
    return {
 	  'isCustomCovenant':'0',
 	  'selectedCovenant':[{
 	  	'id':'',
 	  	'subType':'',
 	  	'description':'',
 	  	'frequency':'',
 	  }],
      'covenantDetails' : {
      	'complianceId':this.complianceId,
      	'referenceCovenantId': this.covenantId,
      	'action': this.action,
        'covenantInfo':[
        ],
      },
      'finalcovenantDetails' : {
      	'complianceId':this.complianceId,
        'covenantInfo':[
        ],
      },
      'value': [],
      'covenantOptions': [
        ],
      'showCustomCovenant':0,
      'comp': 'customCovenant',
      'selType': '',
      'customCovenantCount': 1,
      'months': ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]
    }
  },
  props: ['complianceId','action','covenantId','data'],
  methods: {
	fetchCovenant(){
      Nova.request().post('/nova-vendor/compliance-overview/fetchCovenant',{'standard_covenant_id':this.data,'action':this.action})
      .then(response => {
          this.covenantOptions = response.data;         
      });
    },
    removeType(value, id) {
    	var covenantArr = this.covenantDetails.covenantInfo;
		covenantArr.forEach((alldata,i) => {
	    	Object.keys(alldata).forEach((data)=> {
	    	  if(data == value) {
	    	  	covenantArr.splice(i,1);
	    	  }
			}); 
		});
		this.covenantDetails.covenantInfo = covenantArr;
    },
    fetchSubtypes(value,select$){
  	
		Nova.request().post('/nova-vendor/compliance-overview/fetchSubtypes',{'type':value,'id':this.complianceId,'action':this.action,'standard_covenant_id':this.data})
	      .then(response => {
	      	if(response.data.success == "true") {
	         this.covenantDetails.covenantInfo.push(response.data.covenant); 
	      	}
	    });
    },
    addCustomCov(type) {
    	//this.$emit('clicked', 'customCovenant')
    	this.selType = type;
    	this.showCustomCovenant = 1;
    },
    saveCovenantData() {
    	Nova.request().post('/nova-vendor/compliance-overview/saveCovenantData',this.covenantDetails)
	      .then(response => {
	        if(response.data.status == 'success' && response.data.complianceId != '') {
	        	console.log(response.data);
              //this.$emit('clicked', 'timelines',response.data.complianceId,response.data.covenantIds);
              window.location.href="/admin/covenants?id="+response.data.complianceId;
            }          
	    });
    },
    check(e) {
    	var checkedId = e.target.id;
    	if(e.target.checked) {
    		const el1 = document.querySelectorAll('[data-rowid="'+checkedId+'"]');
    		for (const element of el1) {
			    element.setAttribute("required", "");    
			}
			if(document.getElementById("submitCov").style.display == "none") {
				document.getElementById("submitCov").style.display = "block";
			}
    	}
    	else{
    		const el1 = document.querySelectorAll('[data-rowid="'+checkedId+'"]');
    		for (const element of el1) {
			    element.removeAttribute("required");    
			}
			var checkboxes = document.querySelectorAll('input[type="checkbox"][name="selectCov[]"]')
			var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);
			if(checkedOne) {
				document.getElementById("submitCov").style.display = "block";
			} else {
				document.getElementById("submitCov").style.display = "none";
			}
    	}
    },
    showParent(customCovenant) {
        this.showCustomCovenant = 0;
        var type = customCovenant.type;
        var customCovArr = {
        };
        var covenantArr = this.covenantDetails.covenantInfo;
        var elem;
        covenantArr.forEach((alldata,i) => {
	    	Object.keys(alldata).forEach((data)=> {
	    	  if(data == type) {
	    	  	customCovenant.id = 'custom_'+this.customCovenantCount;
	    	  	this.customCovenantCount = this.customCovenantCount + 1;
	    	  	//customCovenant.selectedCovenant = true;
	    	  	covenantArr[i][data].push(customCovenant);
	    	  	elem = customCovenant.id;
	    	  }
			}); 
		}); 
		this.covenantDetails.covenantInfo = covenantArr;
    },
  },
   created:function(){
   	this.fetchCovenant();
   },
}
</script>
<style src="@vueform/multiselect/themes/default.css"></style>