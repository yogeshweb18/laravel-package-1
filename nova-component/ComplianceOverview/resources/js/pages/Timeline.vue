<template>
	<div id="app">
		<form id="compliance_form" class="dark:text-white text-lg opacity-70" enctype="multipart/form-data" style="min-height: 300px" @submit.prevent="saveTimelines()">
	      <div class="mb-8 space-y-4"></div>
	        <h1 class="text-90 font-normal text-xl md:text-2xl mb-3">Create Reminder Timelines</h1>
	        <div class="bg-white whitebox dark:bg-gray-800 rounded-lg shadow">
	        	<table class="w-full table-default" id="example">
	        		<tr>
	        			<td class="table25">
	        				<div class="label">CL Code:</div>
	        				<div class="field">{{compliance.clcode}}</div>
	        			</td>
	        			<td class="table25">
	        				<div class="label">Tags:</div>
	        				<div class="field">Lorem ipsum</div>
	        			</td>
	        			<td class="table25">
	        				<div class="label">Priority:</div>
	        				<div class="field">{{compliance.priority}}</div>
	        			</td>
	        			<td class="table25">
	        				<div class="label">Client:</div>
	        				<div class="field">{{compliance.name}}</div>
	        			</td>
	        		</tr>
	        		<tr>
	        			<td>
	        				<div class="label">Document Name:</div>
	        				<div class="field">{{compliance.docName}}</div>
	        			</td>
	        			<td>
	        				<div class="label">Start Date:</div>
	        				<div class="field">{{compliance.startDate}}</div>
	        			</td>
	        			<td>
	        				<div class="label">Secured/Un-secured:</div>
	        				<div class="field">{{compliance.secured}}</div>
	        			</td>
	        			<td>
	        				<div class="label">Mail CC:</div>
	        				<div class="field">{{compliance.mailcc}}</div>
	        			</td>
	        		</tr>
	        		<tr>
	        			<td>
	        				<div class="label">Uploaded Documents:</div>
	        				<div class="field">{{compliance.documentNames}}</div>
	        			</td>
	        			<td>
	        				<div class="label">End Date:</div>
	        				<div class="field">{{compliance.endDate}}</div>
	        			</td>
	        			<td>
	        				<div class="label">Inconsistency Treatment:</div>
	        				<div class="field">{{compliance.inconsistencyTreatment}}</div>
	        			</td>
	        		
	        		</tr>
	        	</table>
	        </div>

	        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
	        	<div v-if="Object.keys(compliance.covenantData).length>0" v-for="covData in compliance.covenantData">
               		<div class="title covenantheading" @click="covData.expanded = !covData.expanded">
				      <div>
				        <b>{{ covData.subType }}</b>
				      </div>
				      <div>
				        <span v-if="covData.expanded">&#x2191;</span>
				        <span v-else>&#x2193;</span>
				      </div>
				    </div>
				    <div class="description accordionbox" v-if="covData.expanded">
				      <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
			            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
			              <label for="" class="inline-block pt-2 leading-tight label">Start Date (Allotment Date)
			              </label>
			            </div>
			            <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 w-full md:w-3/5 md:py-5">
			              <label for="" class="inline-block pt-2 leading-tight label">{{covData.startDate}}
			              </label>
			            </div>
			          </div>
			          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
			            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
			              <label for="" class="inline-block pt-2 leading-tight label">End Date (Redemption Date)
			              </label>
			            </div>
			            <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 w-full md:w-3/5 md:py-5">
			              <label for="" class="inline-block pt-2 leading-tight label">{{covData.dueDate}}
			              </label>
			            </div>
			          </div>
			          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
			            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
			              <label for="" class="inline-block pt-2 leading-tight label">Applicable Month
			              </label>
			            </div>
			            <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 w-full md:w-3/5 md:py-5">
			              <label for="" class="inline-block pt-2 leading-tight label">{{covData.applicableMonth}}
			              </label>
			            </div>
			          </div>

			          <table class="w-full table-default" cellpadding="0" cellspacing="0" data-testid="resource-table" v-if="typeof covData.instances !== 'undefined' && Object.keys(covData.instances).length>0"  >
						    <thead class="bg-gray-50 dark:bg-gray-800">
						    	<tr>
						            <th class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2"><span>Instance#</span></th>
						            <th class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2"><span>Activate Before Days</span></th>
						            <th class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2"><span>Due Date</span></th>
						            <th class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2"><span>Applicable Month</span></th>
						            <th class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2"><span>Status</span></th>
						         </tr>
						    </thead>
						    <tbody>
						    	<tr dusk="3-row" class="group" v-for="instance in covData.instances">
						    		<td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
						               <div class="text-left"><input type="hidden" v-model="instance.instanceNo" /><span class="text-90 whitespace-nowrap">{{instance.instanceNo}}</span></div>
						            </td>
						            <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
						               <input type="text" placeholder="" name="covfrequency" class="w-full form-control form-input form-input-bordered" id="description" dusk="description" v-model="instance.activateBefore"/>
						            </td>
						            <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
						               <input type="date" v-model="instance.dueDate" class="form-control form-input form-input-bordered" id="name-create-organization-text-field" required="required">
						            </td>
						            <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
						               <div class="text-left"><input type="hidden" v-model="instance.applicableMonth" /><span class="text-90 whitespace-nowrap label">{{instance.applicableMonth}}</span></div>
						            </td>
						            <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
						               <div class="text-left"><input type="hidden" v-model="instance.status" /><span class="label">Not Started</span></div>
						            </td>
						    	</tr>
						    	<tr class="bg-gray-50 dark:bg-gray-800">
						    		<td class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2" colspan=5><span>Set Timelines (in Days)</span></td>
						    	</tr>
						    	<tr class="group">
						    		<td class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2">
						    			<span class="label">Reminder Before </span>
						            </td>
						            <td class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2">
						    			<input type="text" placeholder="" class="w-full form-control form-input form-input-bordered" id="description" dusk="description" v-model="covData.reminder.before"/>
						            </td>
						    	</tr>
						    	<tr class="group">
						    		<td class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2">
						    			<span class="label">Reminder Interval </span>
						            </td>
						            <td class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2">
						    			<input type="text" placeholder="" class="w-full form-control form-input form-input-bordered" id="description" dusk="description" v-model="covData.reminder.interval"/>
						            </td>
						    	</tr>
						    </tbody>
			          </table>
			          <div v-if="typeof covData.child !== 'undefined' && Object.keys(covData.child).length>0" v-for="child in covData.child">
			          	<div class="bg-gray-50 dark:bg-gray-800">
			          		Sub Covenant - {{child.label}}
			          	</div>
				          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
				            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
				              <label for="" class="inline-block pt-2 leading-tight label">Start Date (Allotment Date) <span class="text-red-500 text-sm">*</span>
				              </label>
				            </div>
				            <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 w-full md:w-3/5 md:py-5">
				              <input type="date" v-model="child.startDate" class="form-control-50 form-input form-input-bordered" id="name-create-organization-text-field" name="startDate" dusk="startDate" required="required"><!----><!----><!---->
				            </div>
				          </div>
				          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
				            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
				              <label for="" class="inline-block pt-2 leading-tight label">End Date (Allotment Date) <span class="text-red-500 text-sm">*</span>
				              </label>
				            </div>
				            <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 w-full md:w-3/5 md:py-5">
				              <input type="date" v-model="child.value" class="form-control-50 form-input form-input-bordered" id="name-create-organization-text-field" name="startDate" dusk="startDate" required="required"><!----><!----><!---->
				            </div>
				          </div>
				          <table class="w-full table-default" cellpadding="0" cellspacing="0" data-testid="resource-table" >
						    <thead class="bg-gray-50 dark:bg-gray-800">
						    	<tr>
						            <th class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2"><span>Instance#</span></th>
						            <th class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2"><span>Activate Before Days</span></th>
						            <th class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2"><span>Due Date</span></th>
						            <th class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2"><span>Applicable Month</span></th>
						            <th class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2"><span>Status</span></th>
						         </tr>
						    </thead>
						    <tbody>
						    	<tr dusk="3-row" class="group">
						    		<td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
						               <div class="text-left"><input type="hidden" v-model="child.instanceNo" /><span class="text-90 whitespace-nowrap">{{child.instanceNo}}</span></div>
						            </td>
						            <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
						               <input type="text" placeholder="" name="covfrequency" class="w-full form-control form-input form-input-bordered" id="description" dusk="description" v-model="child.activateBefore"/>
						            </td>
						            <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
						               <input type="date" v-model="child.dueDate" class="form-control form-input form-input-bordered" id="name-create-organization-text-field" required="required">
						            </td>
						            <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
						               <div class="text-left"><input type="hidden" v-model="child.applicableMonth" /><span class="text-90 whitespace-nowrap label">{{child.applicableMonth}}</span></div>
						            </td>
						            <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
						               <div class="text-left"><input type="hidden" v-model="child.status" /><span class="label">{{child.status}}</span></div>
						            </td>
						    	</tr>
						    	<tr class="bg-gray-50 dark:bg-gray-800">
						    		<td class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2" colspan=5><span>Set Timelines (in Days)</span></td>
						    	</tr>
						    	<tr class="group">
						    		<td class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2">
						    			<span class="label">Reminder Before </span>
						            </td>
						            <td class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2">
						    			<input type="text" placeholder="" class="w-full form-control form-input form-input-bordered" id="description" dusk="description" v-model="covData.reminder.before"/>
						            </td>
						    	</tr>
						    	<tr class="group">
						    		<td class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2">
						    			<span class="label">Reminder Interval </span>
						            </td>
						            <td class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2">
						    			<input type="text" placeholder="" class="w-full form-control form-input form-input-bordered" id="description" dusk="description" v-model="covData.reminder.interval"/>
						            </td>
						    	</tr>
						    </tbody>
			          </table>
			      	  </div>
					</div>
	       		</div>
	       		<button size="lg" align="center" component="button" dusk="create-button" type="submit" class="shadow relative bg-primary-500 hover:bg-primary-400 text-white dark:text-gray-900 cursor-pointer rounded text-sm font-bold focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600 inline-flex items-center justify-center h-9 px-3 shadow relative bg-primary-500 hover:bg-primary-400 text-white dark:text-gray-900" style="    margin: 30px;"><span class="">Submit</span><!----></button>
	    	</div>
	    </form>
	</div>
</template>
<script>
export default {
  name: 'app',
  props: ['complianceId','data'],
  data() {
    return {
 	  //'complianceId':this.complianceId,
 	  'complianceId':84,
 	  'compliance': null,
 	  'months': ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
    }
  },
  methods: {
  	saveTimelines() {
      Nova.request().post('/nova-vendor/compliance-overview/saveTimelines',this.compliance)
      .then(
          response => {
            console.log(response.data);
          }
      )
    },
  	getCovenantData(){
		Nova.request().post('/nova-vendor/compliance-overview/getComplianceCovenant',{'id':this.complianceId,'covenant_ids':this.data})
	      .then(response => {
	         this.compliance = response.data;          
	    });
    },
  },
  created:function(){
   	this.getCovenantData();
  },


 }
</script>
<style scoped>
.title {
  cursor: pointer;
  display: flex;
  justify-content: space-between;
}
.title,
.description {
  border: 1px solid black;
  padding: 5px;
}
</style>