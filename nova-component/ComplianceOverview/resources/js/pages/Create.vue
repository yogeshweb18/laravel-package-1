<template>
  <div id="app">
    <component v-bind:is="this.componentLoad" @clicked="updatecomponent" :complianceId="complianceId" :covenantId="covenantId" :action="action" :data="data"/>
  </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3';
import covenantData from '../../../../../nova-components/ComplianceOverview/resources/js/pages/Selectcov.vue';  
import overviewData from '../../../../../nova-components/ComplianceOverview/resources/js/pages/overview.vue';
import customCovenant from '../../../../../nova-components/ComplianceOverview/resources/js/pages/CustomCovenant.vue';
import timelines from '../../../../../nova-components/ComplianceOverview/resources/js/pages/Timeline.vue';
export default {
  el: '#app',
  name: 'app',
  components: {
     covenantData,
     overviewData,
     customCovenant,
     timelines,
  },
  props: ['componentLoad','complianceId','load','covenantId','action','data'],
  data() {
    return {
      'componentLoad':'',
      'complianceId':this.complianceId,
      'action':this.action,
      'data': this.data,
    }
  },
  methods: {
    updatecomponent(value,complianceId,action='',data=Array()) {
        this.componentLoad = value;
        this.complianceId = complianceId;
        this.data = data;
        this.action = action;
    }
  },
  
  created:function(){
    if(this.load) {
          this.componentLoad=this.load;
          //this.action=this.action;
    } else {
      this.componentLoad = 'overviewData';
    }
  },
  mounted() {
   	//this.componentLoad='customCovenant';
  },
}
</script>

<style>
/* Scoped Styles */
</style>
