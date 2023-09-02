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
             <h1 class="modal-header1 text-90 font-normal text-xl md:text-2xl mb-3">View Activity Log</h1>

              <div style="padding: 5px;border-bottom: solid 1px; border-color: #f3f3f3;" v-for="(item,key) in log">
                <span style="color:#96144c;">{{key}}</span> <b>[ New : {{item.new}}] </b> [ Old: {{item.old}} ]
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
    props: ['log'],
    methods: {
      close() {
        this.$emit('close');
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