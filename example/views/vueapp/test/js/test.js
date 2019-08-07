
var vueAppTest = new Vue({
    el: '#vueAppTest',
    data: {

        /**
         * propsApp is used to collect attribute related to root container element.
         * This is the suggested way to pass data from php to js vue app.
         * All parameter are converted from dash to camel case (html k-param-1 become kParam1)
         */
        propsApp: {
            kParam1: null,
            kParam2: null,
            kParam3: null,
        },

        clock_datetime: null
    },

    filters: {
        formatDateTime: function (value, format) {
            return value ? moment(value).format(format) : null
        }
    },

    mounted() {
        this.readPropsApp();

        this.loadAtomicClock();
    },

    methods: {

        readPropsApp: function () {
            for (var k in this.propsApp) {
                var attr = k.replace(/[A-Z|0-9]/g, m => "-" + m.toLowerCase());
                console.log(k, attr);
                if (this.$el.attributes[attr] != undefined) {
                    this.propsApp[k] = this.$el.attributes[attr].value;
                }
            }
        },

        loadAtomicClock: function () {

            var self = this;

            axios
                .get('http://worldtimeapi.org/api/ip')
                .then(function (response) {
                    console.log(response);
                    self.clock_datetime = response.data.datetime;
                })
                .catch(error => console.log(error));
        },

    }
})