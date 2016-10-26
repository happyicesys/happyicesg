if(document.querySelector('#d2dorderController')){
    Vue.component('order', {
        template: '#order-template',
        data() {
            return {
                step1: true,
                step2: false,
                form: {
                    postcode: ''
                },
                loading_postcode: false,
                formErrors: []
            }
        },
        methods: {
            verifyPostcode() {
                this.loading_postcode = true
                this.$http.post('/postcode/verify', {postcode: this.form.postcode})
                    .then((response) => {
                        this.step1 = false
                        this.step2 = true
                    }, (response) => {
                        console.log(repsonse)
                    })
                this.loading_postcode = false
            }
        },
        computed: {
        },
        watch: {
        }
    });

    new Vue({
        el: '#d2dorderController',
    });
}