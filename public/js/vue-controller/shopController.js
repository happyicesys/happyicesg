if (document.querySelector('#shopController')) {
  Vue.component('shop', {
    template: '#shop-template',
    data() {
      return {
        step1: true,
        step2: false,
        step3: false,
        form: {
          area_id: '',
          postcode: '',
          name: '',
          contact: '',
          email: '',
          street: '',
          block: '',
          floor: '',
          unit: '',
          del_date: 1,
          del_time: 2,
          remark: '',
        },
        deldate_option: [],
        deltime_option: [],
        loading: false,
        formErrors: [],
        items: [],
        covered: false,
        subtotal: 0,
        total: 0,
        totalqty: 0,
        submitable: false,
        date_options: [],
        time_options: []
      }
    },
    mounted() {
      // temporary
      this.showItemList()

      var el = this.$el
      setTimeout(function() {
        $(el).find('input').trigger('change')
      }, 20)
      this.pushDeltimeOptions()
      this.pushDeldateOptions()
    },
    methods: {
      verifyPostcode() {
        this.loading = true
        this.formErrors = ''
        this.form.street = ''
        this.form.block = ''
        this.$http.post('/postcode/verify', {
            postcode: this.form.postcode
          })
          .then(function(response) {
            var verified = response.body
            this.covered = verified.covered
            if (verified.postcode) {
              this.form.postcode = verified.postcode.value
              this.form.street = verified.postcode.street
              this.form.block = verified.postcode.block
            }
            this.$http.get('/api/d2ditems/' + this.covered).then(function(response) {
              var result = JSON.parse(JSON.stringify(response.body))
              this.items = result
            })
            this.step1 = false
            this.step2 = true
          }, function(response) {
            var result = response.body
            this.formErrors = result
          })
        this.loading = false
      },
      showItemList() {
        this.$http.get('/api/d2ditems/' + this.covered).then(function(response) {
          var result = JSON.parse(JSON.stringify(response.body))
          this.items = result
        })
      },
      deductTotal(value) {
        this.subtotal = (parseFloat(this.subtotal) - parseFloat(value)).toFixed(2)
      },
      addTotal(value) {
        this.subtotal = (parseFloat(this.subtotal) + parseFloat(value)).toFixed(2)
      },
      deductQty(value) {
        this.totalqty = parseInt(this.totalqty) - parseInt(value)
      },
      addQty(value) {
        this.totalqty = parseInt(this.totalqty) + parseInt(value)
      },
      fillForm() {
        this.step3 = true
      },
      validateOrder: _.debounce(function() {
        this.formErrors = ''
        this.$http.post('/api/validateOrder', this.form).then(function(response) {
          this.submitable = true
        }, function(response) {
          this.submitable = false
          this.formErrors = response.body
        });
      }, 400),
      pushDeldateOptions() {
        for (var i = 0; i < this.date_options.length; i++) {
          this.deldate_option.push({
            index: i,
            id: this.date_options[i].id,
            text: this.date_options[i].text
          })
        }
      },
      pushDeltimeOptions() {
        for (var i = 0; i < this.time_options.length; i++) {
          this.deltime_option.push({
            index: i,
            id: this.time_options[i].id,
            text: this.time_options[i].text
          })
        }
      }
    },
    computed: {
      total() {
        // if (this.totalqty >= 3 || this.totalqty == 0 || this.covered) {
        if (this.totalqty >= 3 || this.totalqty == 0) {
          this.delivery = 0
        } else if (this.totalqty > 0 && this.totalqty < 3) {
          this.delivery = 5
        }
        return (parseFloat(this.subtotal) + parseFloat(this.delivery)).toFixed(2)
      },
      disableNext() {
        if (this.total == 0 || (this.covered == false && this.totalqty <= 1)) {
          return true
        } else {
          return false
        }
      }
    },
  });

  Vue.component('select2', {
    props: ['options', 'value'],
    template: '#select2-template',
    mounted: function() {
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
  })
  new Vue({
    el: '#shopController',
  });
}