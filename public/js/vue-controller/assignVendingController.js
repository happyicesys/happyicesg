Vue.component('assignvending', {
  props: ['person_id'],
  data() {
    return {
      list: [],
      vendingselection: [],
      form: {
        vending_id: ''
      }
    }
  },
  mounted() {
    this.fetchVendingOption()
    this.fetchTable()
  },
  methods: {
    fetchVendingOption() {
      this.$http.get('/api/vending/avail/' + this.person_id + '/person')
            .then((response) => {
              this.vendingselection = response.data;
            })
    },
    fetchTable() {
      this.$http.get('/api/vending/' + this.person_id + '/person')
            .then((response) => {
              this.list = response.data;
            })
    },
    addVending() {
      this.$http.post('/vending/add/' + this.person_id + '/person', this.form)
                .then((response) => {
                  this.form.vending_id = ''
                  this.fetchVendingOption()
                  this.fetchTable()
                })
    },
    removeVending(vending_id) {
      this.$http.delete('/vending/remove/' + vending_id + '/person/' + this.person_id)
                .then((response) => {
                  this.fetchVendingOption()
                  this.fetchTable()
                })
    }
  }
});

Vue.component('select2', {
  template: '<select><slot></slot></select>',
  props: ['options', 'value'],
    mounted: function () {
      var vm = this
      $(this.$el)
        // init select2
        .select2({
          data: this.options,
          placeholder: 'Select...'
        })
        .val(this.value)
        .trigger('change')
        // emit event on change.
        .on('change', function () {
          vm.$emit('input', this.value)
        })
    },
    watch: {
      value: function (value) {
        // update value
        $(this.$el).val(value).trigger('change');
      },
      options: function (options) {
        // update options
        $(this.$el).select2({ data: options })
      }
    },
    destroyed: function () {
      $(this.$el).off().select2('destroy')
    }
  });

new Vue({
  el: '#assignVendingController',
});