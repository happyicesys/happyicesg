Vue.component('transsubscribe', {
  props: ['user_id'],
  data() {
    return {
      list: [],
      userselection: [],
      form: {
        user_id: ''
      }
    }
  },
  mounted() {
    this.fetchUserOption()
    this.fetchTable()
  },
  methods: {
    fetchUserOption() {
      this.$http.get('/api/transaction/email/nonsubscription')
            .then((response) => {
              console.log(response)
              this.userselection = response.data;
            })
    },
    fetchTable() {
      this.$http.get('/api/transaction/email/subscription')
            .then((response) => {
              this.list = response.data;
            })
    },
    addUser() {
      this.$http.post('/api/transaction/email/addsubscriber', this.form)
                .then((response) => {
                  this.form.user_id = ''
                  this.fetchUserOption()
                  this.fetchTable()
                })
    },
    removeUser(user_id) {
      this.$http.delete('/api/transaction/email/removesubscriber/' + user_id)
                .then((response) => {
                  this.fetchUserOption()
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
  el: '#transSubscriptionController',
});