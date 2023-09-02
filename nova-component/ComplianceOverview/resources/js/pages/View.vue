<template>
  <div class="modal-backdrop">
      <div class="modal modal30">
          <header class="">
            <button
              type="button"
              class="btn-close"
              @click="close"
            >
              x
            </button>
          </header>
             <h1 class="modal-header1 text-90 font-normal text-xl md:text-2xl mb-3">View Compliance</h1>
              <div class="bg-white dark:bg-gray-800">
                <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
                  <div class="mt-2 md:mt-0">
                    <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">CL Code
                    </label>
                    <span class="answer">
                      <label for="name-create-organization-text-field" class="answer">{{compliance.clcode}}</label>
                    </span>
                  </div>
                </div>
              </div>
              <div class="bg-white dark:bg-gray-800">
                <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
                  <div class="mt-2 md:mt-0">
                    <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">ISIN
                    </label>
                    <span class="answer">
                      <label for="name-create-organization-text-field" class="answer">{{compliance.isinValues}}</label>
                    </span>
                  </div>

                </div>
              </div>
              <div class="bg-white dark:bg-gray-800">
                <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
                  <div class="mt-2 md:mt-0">
                    <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">Document Name
                    </label>
                    <span class="answer">
                      <label for="name-create-organization-text-field" class="answer">{{compliance.docName}}</label>
                    </span>
                  </div>
                </div>
              </div>
              <div class="bg-white dark:bg-gray-800">
                <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
                  <div class="mt-2 md:mt-0">
                    <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">Start Date
                    </label>
                    <span class="answer">
                       <label for="name-create-organization-text-field" class="answer">{{compliance.startDate}}</label>
                    </span>
                  </div>
                </div>
              </div> 
              <div class="bg-white dark:bg-gray-800">
                <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
                  <div class="mt-2 md:mt-0">
                    <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">End Date
                    </label>
                    <span class="answer">
                       <label for="name-create-organization-text-field" class="answer">{{compliance.endDate}}</label>    
                    </span>
                  </div>
                </div>
              </div>  
              <div class="bg-white dark:bg-gray-800">
                <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
                  <div class="mt-2 md:mt-0">
                    <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">Priority
                    </label>
                    <span class="answer">
                      <label for="name-create-organization-text-field" class="answer">{{compliance.priority}}</label>
                    </span>
                  </div>
                </div>
              </div>
                 <div class="bg-white dark:bg-gray-800">
                <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
                  <div class="mt-2 md:mt-0">
                    <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">Secured/Un-secured
                    </label>
                     <span class="answer">
                       <label for="name-create-organization-text-field" class="answer">{{compliance.secured}}</label>
                     </span>
                  </div>
                </div>
              </div>
              <div class="bg-white dark:bg-gray-800">
                <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
                  <div class="mt-2 md:mt-0">
                    <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">Inconsistency Treatment
                    </label>
                    <span class="answer">
                      <label for="name-create-organization-text-field" class="answer">{{compliance.inconsistencyTreatment}}</label>
                    </span>
                  </div>
                </div>
              </div>
              <div class="bg-white dark:bg-gray-800">
                <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
                  <div class="mt-2 md:mt-0">
                    <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">Client Reference
                    </label>
                    <span class="answer">
                      <label for="name-create-organization-text-field" class="answer">{{compliance.name}}</label>
                    </span>
                  </div>
                </div>
              </div>
              <div class="bg-white dark:bg-gray-800">
                <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
                  <div class="mt-2 md:mt-0">
                    <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">Mail CC
                    </label>
                    <span class="answer">
                       <label for="name-create-organization-text-field" class="answer">{{compliance.mailCC}}</label>
                    </span>
                  </div>
                </div>
              </div>
              <div class="bg-white dark:bg-gray-800">
                <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
                  <div class="mt-2 md:mt-0">
                    <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">Uploads
                    </label>
                    <span class="answer" v-for="uploads in compliance.documentNames">
                      <button @click.prevent="getAttachment(compliance.complianceId,uploads)">{{uploads}}</button>
                    </span>
                  </div>
                </div>
              </div>
              <footer class="modal-footer">
        <button
          type="button"
          class="flex-shrink-0 shadow rounded focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring bg-primary-500 hover:bg-primary-400 active:bg-primary-600 text-white dark:text-gray-800 inline-flex items-center font-bold px-4 h-9 text-sm flex-shrink-0"
          @click="close"
        >
          Close
        </button>
      </footer>            
        </div>  
        
    </div>
</template>

<script>
  export default {
    name: 'Modal',
    props: ['compliance'],
    methods: {
      close() {
        this.$emit('close');
      },
      getAttachment(id,upload) {
        window.open("/admin/compliance-overview/attachment?id="+id+"&file="+upload);
      },
    },
  };
</script>

<style>
  .modal-backdrop {
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: rgba(0, 0, 0, 0.3);
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .modal {
    background: #FFFFFF;
    box-shadow: 2px 2px 20px 1px;
    overflow-x: auto;
    display: flex;
    flex-direction: column;
  }

  .modal-header,
  .modal-footer {
    padding: 15px;
    display: flex;
  }

  .modal-header {
    position: relative;
    border-bottom: 1px solid #eeeeee;
    color: #4AAE9B;
    justify-content: space-between;
  }

  .modal-footer {
    border-top: 1px solid #eeeeee;
    flex-direction: column;
    justify-content: flex-end;
  }

  .modal-body {
    position: relative;
    padding: 20px 10px;
  }

  .btn-close {
    position: absolute;
    top: 0;
    right: 0;
    border: none;
    font-size: 20px;
    padding: 10px;
    cursor: pointer;
    font-weight: bold;
    color: #4AAE9B;
    background: transparent;
  }

  .btn-green {
    color: white;
    background: #4AAE9B;
    border: 1px solid #4AAE9B;
    border-radius: 2px;
  }
</style>