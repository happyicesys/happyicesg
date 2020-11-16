Vue.component('assignCustcategory', {
  props: ['user_id'],
  data() {
    return {
      list: [],
      custcategorySelection: [],
      form: {
        custcategoryId: ''
      }
    }
  },
  mounted() {
    this.fetchCustcategoryOption()
    this.fetchTable()
  },
  methods: {
    fetchCustcategoryOption() {
      this.$http.get('/api/custcat/user/' + this.user_id + '/2')
            .then((response) => {
              this.custcategorySelection = response.data;
            })
    },
    fetchTable() {
      this.$http.get('/api/custcat/user/' + this.user_id + '/1')
            .then((response) => {
              this.list = response.data;
            })
    },
    addCustcategory() {
      this.$http.post('/user/' + this.user_id + '/addcustcat', this.form)
                .then((response) => {
                  this.form.custcategoryId = ''
                  this.custcategorySelection = ''
                  this.fetchCustcategoryOption()
                  this.fetchTable()
                })
    },
    removeCustcategory(custcategoryId) {
      this.$http.delete('/user/' + this.user_id + '/removecustcat/' + custcategoryId)
                .then((response) => {
                  this.custcategorySelection = ''
                  this.fetchCustcategoryOption()
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
  el: '#assignCustcategoryController',
});