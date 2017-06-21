if (document.querySelector('#clientProductController')) {
  Vue.component('productpage', {
    data() {
      return {
        itemcategories: [],
        products: []
      }
    },
    mounted() {
      this.loadItemcategories()
      this.loadProductsByItemcategory(1)
    },
    methods: {
      loadItemcategories() {
        this.$http.get('/api/itemcategories').then((response) => {
          this.itemcategories = response.data
        })
      },
      loadProductsByItemcategory(itemcategory_id) {
        this.products = []
        this.$http.get('/api/items/itemcategory/' + itemcategory_id).then((response) => {
          this.products = response.data
        })
      }
    }
  });

  Vue.filter('chunk', function (value, size) {
    return _.chunk(value, size); // using lodash
  });

  new Vue({
    el: '#clientProductController',
  });
}