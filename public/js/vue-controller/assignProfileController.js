Vue.component('assignprofile', {
  props: ['user_id'],
  data() {
    return {
      list: [],
      profileselection: [],
      form: {
        profile_id: ''
      }
    }
  },
  mounted() {
    this.fetchProfileOption()
    this.fetchTable()
  },
  methods: {
    fetchProfileOption() {
      this.$http.get('/api/user/' + this.user_id + '/nonprofile')
            .then((response) => {
              this.profileselection = response.data;
            })
    },
    fetchTable() {
      this.$http.get('/api/user/' + this.user_id + '/profile')
            .then((response) => {
              this.list = response.data;
            })
    },
    addProfile() {
      this.$http.post('/user/' + this.user_id + '/addprofile', this.form)
                .then((response) => {
                  this.form.profile_id = ''
                  this.profileselection = ''
                  this.fetchProfileOption()
                  this.fetchTable()
                })
    },
    removeProfile(profile_id) {
      this.$http.delete('/user/' + this.user_id + '/removeprofile/' + profile_id)
                .then((response) => {
                  this.profileselection = ''
                  this.fetchProfileOption()
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
  el: '#assignProfileController',
});