var ___VUEAPP_APP_ID___ = new Vue({
    el: '#___VUEAPP_APP_ID___',

    // If you need a date picker,
    // add VueApp::PKG_VUEJS_DATEPICKER to 'packages' VueApp widget config
    // Refer to https://github.com/charliekassel/vuejs-datepicker
    components: {
        vuejsDatepicker
    },

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
            kParamObj: null,
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

        // Because kParamObj is an object, we have to parse to serialized version of kParamObj
        this.propsApp.kParamObj = JSON.parse(this.propsApp.kParamObj);

        this.loadAtomicClock();
    },

    methods: {

        readPropsApp: function () {
            for (var k in this.propsApp) {

                // Taken from: https://github.com/sindresorhus/decamelize/blob/master/index.js
                var attr = k
                .replace(/([a-z\d])([A-Z])/g, '$1-$2')
                .replace(/([A-Z]+)([A-Z][a-z\d]+)/g, '$1-$2')
                .toLowerCase();

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