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

  new Vue({
    el: '#clientProductController',
  });
}