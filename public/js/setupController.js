if(document.querySelector('#setupController')){
  Vue.component('postcodes', {
    template: '#postcode-template',
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
          del_date: 'Same Day',
          del_time: '8am - 12pm',
          remark: '',
        },
        deldate_option: [
          {id: 'Same Day', text: 'Same Day'},
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
            this.$http.get('/api/d2donlinesales').then((response) => {
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
        if(this.totalqty >= 4 || this.totalqty == 0 || this.covered){
          this.delivery = 0
        }else if(this.totalqty > 0 && this.totalqty < 4){
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
      }
    }
  })
  new Vue({
    el: '#d2dorderController',
  });
}