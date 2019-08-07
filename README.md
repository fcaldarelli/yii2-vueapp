Vue.js helper for Yii2
=====================

This is a component that helps to create Vue.js app without usign webpack or similar.

All assets (js, css and templates) are injected directly in the html and this components
provides functionalities to split the code (js, css and templates) and to load parameters
from html root element.

Two default packages are embedded with this component: Axios and Moment. There is an example
that shows how use both in the code.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist fabriziocaldarelli/yii2-vueapp "@dev"
```

or add

```
"fabriziocaldarelli/yii2-vuejsapp": "@dev"
```

to the require section of your `composer.json` file.


Usage
-----

**1) The view file**

Inside the view, call VueApp widget:

```php
use \sfmobile\vueapp\VueApp;

// Css files are automatically loaded from vueapp/test/css/
// Template files are automatically loaded from vueapp/test/tpl/
// Js files are automatically loaded from vueapp/test/css/
// Passing packages parameter we require Axios and Moment package
VueApp::widget([
    'packages' => [VueApp::PKG_AXIOS, VueApp::PKG_MOMENT]
]);

?>

<!--
    To pass data from php/html to js vue app we defined propsApp attribute data in VueApp configuration and
    the we fill in html converting from camel case to dash
-->
<div id="vueAppTest" v-cloak
    k-param-1="value_1"
    k-param-2="value_2"
    k-param-3="value_3"
>

    kParam1: {{ propsApp.kParam1 }}
    <br />
    kParam2: {{ propsApp.kParam2 }}
    <br />
    kParam3: {{ propsApp.kParam3 }}
    <br />
    clock datetime: {{ clock_datetime | formatDateTime('DD/MM/YYYY HH:mm') }}

</div>
```

The most important parameter is packages that loads embedded packages such as Axios and Moment.

**2) The Vue app js files**

Starting from view path folder, js files for app and components are in vueapp/test/js/*actionName*/ .

For example, the path for main vue app js could be vueapp/test/js/test.js

```js

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
```

**3) The Vue app css files**

Starting from view path folder, css files for app and components are in vueapp/test/css/*actionName*/ .

For example, the path for main vue app css could be vueapp/test/css/test.css

```css
[v-cloak] {
    display: none
}
```

Tips & Tricks
-----

To pass data from html/php to js vue app, I used an attribute called propsApp, whithin are defined attributes passed in html root element.

For example, html root element "vueAppTest":

```html
<div id="vueAppTest" v-cloak
    k-param-1="value_1"
    k-param-2="value_2"
    k-param-3="value_3"
>
...
</div>
```

all parameters defined in data.propsApp are readed from html when app is mounted (calling readPropsApp method):

```js
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

    },

    mounted() {
        this.readPropsApp();
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
    }
}
```
