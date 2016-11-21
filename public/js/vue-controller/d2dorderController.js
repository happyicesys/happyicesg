if(document.querySelector('#d2dorderController')){
  Vue.component('order', {
    template: '#order-template',
    data() {
      return {
        step1: true,
        step2: false,
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
          del_date: 'Within 1 Day',
          del_time: '8am - 12pm',
          remark: '',
        },
        deldate_option: [
          {id: 'Within 1 Day', text: 'Within 1 Day'},
          {id: 'Within 2 Days', text: 'Within 2 Days'},
        ],
        deltime_option: [
          {id: '8am - 12pm', text: '8am - 12pm'},
          {id: '12pm - 5pm', text: '12pm - 5pm'},
          {id: '5pm - 9pm', text: '5pm - 9pm'},
        ],
        loading: false,
        formErrors: [],
        items: [],
        delivery: 7,
        covered: false,
        subtotal: 0,
        total: 0,
        totalqty: 0,
        submitable: false,
      }
    },
    methods: {
      verifyPostcode() {
        this.loading = true
        this.formErrors = ''
        this.form.street = ''
        this.form.block = ''
        this.$http.post('/postcode/verify', {postcode: this.form.postcode})
          .then((response) => {
            const verified = response.body
            this.covered = verified.covered
            if(verified.postcode){
              this.form.postcode = verified.postcode.value
              this.form.street = verified.postcode.street
              this.form.block = verified.postcode.block
            }
            this.$http.get('/api/d2ditems/' + this.covered).then((response) => {
              const result = JSON.parse(JSON.stringify(response.body))
              this.items = result
            })
            this.step1 = false
            this.step2 = true
          }, (response) => {
            const result = response.body
            this.formErrors = result
          })
        this.loading = false
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
        this.$http.post('/api/validateOrder', this.form).then((response) => {
          this.submitable = true
        }, (response) => {
          this.submitable = false
          this.formErrors = response.body
        });
      }, 400)
    },
    computed: {
      total() {
        if(this.totalqty >= 2 || this.totalqty == 0 || this.covered){
          this.delivery = 0
        }else if(this.totalqty > 0 && this.totalqty < 2){
          this.delivery = 7
        }
        return (parseFloat(this.subtotal) + parseFloat(this.delivery)).toFixed(2)
      },
      submitable() {
        if(this.form.name && this.form.contact && this.form.email && this.form.postcode && this.form.block && this.form.floor && this.form.unit) {
          return true
        }else{
          return false
        }
      },
      disableNext() {
        if(this.total == 0 || (this.covered == false && this.totalqty <= 1)) {
          return true
        }else {
          return false
        }
      }
    }
  });

Vue.component('select2', {
  props: ['options', 'value'],
  template: '#select2-template',
  mounted: function () {
    var vm = this
    $(this.$el)
      .val(this.value)
      .select2({
        data: this.options,
      })
      .on('change', function () {
        vm.$emit('input', this.value)
      })
  },
  watch: {
    value: function (value) {
      $(this.$el).select2('val', value)
    },
    options: function (options) {
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
      options: [
        {id: '' , text: ''},
        {id: 0 , text: '0'},
        {id: 1, text: '1'},
        {id: 2, text: '2'},
        {id: 3, text: '3'},
        {id: 4, text: '4'},
        {id: 5, text: '5'},
        {id: 6, text: '6'},
        {id: 7, text: '7'},
        {id: 8, text: '8'},
        {id: 9, text: '9'},
        {id: 10, text: '10'},
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
    'qty'(val) {
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