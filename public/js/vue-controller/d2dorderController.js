if (document.querySelector('#d2dorderController')) {
  Vue.component('order', {
    template: '#order-template',
    data() {
      return {
        step1: false,
        step2: true,
        step3: false,
        form: {
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
        delivery: 5,
        covered: false,
        subtotal: 0,
        total: 0,
        totalqty: 0,
        submitable: false,
        date_options: [
          {
            'id': 1,
            'text': 'Within 1 Day'
          },
          {
            'id': 2,
            'text': 'Within 2 Days'
          }
        ],
        time_options: [
          {
            'id': 1,
            'text': '8am - 12pm'
          },
          {
            'id': 2,
            'text': '12pm - 5pm'
          }
        ]
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
          this.delivery = 0
        }
        return (parseFloat(this.subtotal) + parseFloat(this.delivery)).toFixed(2)
      },
      disableNext() {
        if (this.total < 50) {
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

  Vue.component('salesItem', {
    template: '#item-template',
    props: ['number', 'item', 'items', 'subtotal', 'finalstep'],
    data() {
      return {
        options: [{
          id: '',
          text: ''
        }, {
          id: 0,
          text: '0'
        }, {
          id: 1,
          text: '1'
        }, {
          id: 2,
          text: '2'
        }, {
          id: 3,
          text: '3'
        }, {
          id: 4,
          text: '4'
        }, {
          id: 5,
          text: '5'
        }, {
          id: 6,
          text: '6'
        }, {
          id: 7,
          text: '7'
        }, {
          id: 8,
          text: '8'
        }, {
          id: 9,
          text: '9'
        }, {
          id: 10,
          text: '10'
        },
        ],
        prev_amount: 0,
        amount: 0,
        after_amount: 0,
        prev_qty: 0,
        qty: 0,
        after_qty: 0
      }
    },
    watch: {
      'qty' (val) {
        this.amount = (this.qty * (this.item.quote_price / this.item.qty_divisor)).toFixed(2)
        this.after_amount = this.amount
        this.qty = val
        this.after_qty = this.qty
        this.$emit('beforeamount', this.prev_amount)
        this.$emit('beforeqty', this.prev_qty)
        this.$emit('afteramount', this.after_amount)
        this.$emit('afterqty', this.after_qty)
        this.prev_amount = this.after_amount
        this.after_amount = 0
        this.prev_qty = this.after_qty
        this.after_qty = 0
      }
    },
  })
  new Vue({
    el: '#d2dorderController',
  });
}