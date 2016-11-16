if(document.querySelector('#setupController')){
  Vue.component('postcodes', {
    template: '#postcode-template',
    data() {
      return {
        list: [],
        page_options: [
          {id: 100, text: '100 /page'},
          {id: 200, text: '200 /page'},
          {id: 500, text: '500 /page'},
        ],
        search: {
          area: '',
          am: '',
          postcode: '',
          assignto: '',
          street: '',
        },
        searching: false,
        loading: false,
        sortkey: '',
        reverse: false,
        selected_page: '100',
        pagination: {
          total: 0,
          from: 1,
          // required
          per_page: 1,
          current_page: 1,
          last_page: 0,
          to: 5
        },
        items: [],
        options: []
      }
    },
    mounted() {
      this.loadMember()
      this.fetchTable()
    },
    methods: {
      fetchTable() {
        this.searching = true
        this.loading = true
        let data = {
          // subject to change (search list and pagination)
          paginate: this.pagination.per_page,
          page: this.pagination.current_page,
          sortkey: this.sortkey,
          reverse: this.reverse,
          per_page: this.selected_page,
          area: this.search.area,
          am: this.search.am,
          postcode: this.search.postcode,
          assignto: this.search.assignto,
          street: this.search.street,
        }
        this.$http.get(
            // subject to change (search list and pagination)
            '/market/setup/postcodes?page=' + data.page +
            '&perpage=' + data.per_page +
            '&sortkey=' + data.sortkey +
            '&reverse=' + data.reverse +
            '&area=' + data.area +
            '&am=' + data.am +
            '&postcode=' + data.postcode +
            '&assignto=' + data.assignto +
            '&street=' + data.street
          ).then((response) => {
            const result = response.body;
            this.items = result.data;
            this.pagination = {
              total: result.total,
              from: result.from,
              to: result.to,
              per_page: result.per_page,
              current_page: result.current_page,
              last_page: result.last_page,
            }
            this.page_options.push({id: result.total, text: 'All'})
            this.loading = false
            this.searching = false;
        });
      },
      loadMember() {
        this.$http.get('/api/members/select').then((response) => {
          const result = JSON.parse(JSON.stringify(response.body))
          this.options = result
        })
      },
      searchData() {
        this.pagination.current_page = 1;
        this.fetchTable();
      },
      sortBy(sortkey) {
        this.pagination.current_page = 1;
        this.reverse = (this.sortkey == sortkey) ? ! this.reverse : false;
        this.sortkey = sortkey;
        this.fetchTable();
      },
      redirectEdit(id) {
        window.location.href = 'postcode/' + id + '/edit';
      }
    },
    watch: {
      'selected_page'(val) {
        this.selected_page = val;
        this.pagination.current_page = 1;
        this.fetchTable();
      }
    }
  })

  Vue.component('itemPostcodes', {
    template: '#postcode-item',
    props: ['number', 'item', 'items', 'pagination', 'options'],
    data() {
      return {
      }
    },
  })

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

  Vue.component('pagination', {
    template: '#paginate-template',
    props: {
      pagination: {
        type: Object,
        required: true
      },
      callback: {
        type: Function,
        required: true
      },
      offset: {
        type: Number,
        default: 4
      }
    },
    computed: {
      array: function () {
        if(!this.pagination.to) {
          return [];
        }
        var from = this.pagination.current_page - this.offset;
        if(from < 1) {
          from = 1;
        }
        var to = from + (this.offset * 2);
        if(to >= this.pagination.last_page) {
          to = this.pagination.last_page;
        }
        var arr = [];
        while (from <=to) {
          arr.push(from);
          from++;
        }
        return arr;
      }
    },
    watch: {
      'pagination.per_page': function () {
        this.callback();
      }
    },
    methods: {
      changePage: function (page) {
        if(page > 0 && page < this.pagination.last_page + 1 && page != this.pagination.current_page){
          this.pagination.current_page = page;
          this.callback();
        }
      }
    }
  })

  new Vue({
    el: '#setupController',
  });
}