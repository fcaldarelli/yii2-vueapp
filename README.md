Vue.js helper for Yii2
=====================

[![Latest Stable Version](https://poser.pugx.org/fabriziocaldarelli/yii2-vueapp/v/stable)](https://packagist.org/packages/fabriziocaldarelli/yii2-vueapp)
[![Total Downloads](https://poser.pugx.org/fabriziocaldarelli/yii2-vueapp/downloads)](https://packagist.org/packages/fabriziocaldarelli/yii2-vueapp)
[![Build Status](https://travis-ci.org/FabrizioCaldarelli/yii2-vueapp.svg?branch=master)](https://travis-ci.org/FabrizioCaldarelli/yii2-vueapp)

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
"fabriziocaldarelli/yii2-vueapp": "@dev"
```

to the require section of your `composer.json` file.

How it works
------------

This component injects js, css and tpl (php or html) files into the returned html.

These files are read starting from same folder of action view file, appending vueapp/*actionName*/js or vueapp/*actionName*/css or vueapp/*actionName*/tpl

VueApp::begin mainly supports three parameters:

- id: vue app html tag id selector;
- propsData: widget uses this element to pass data from html/php to js script;
- packages: list of packages that should be loaded into js vue script

**Pay attention**: *propsData* keys have the same name (and same case) in php and in js file.

Usage
-----

**1) The view file**

Inside the view, call VueApp widget:

```php
<?php
use \sfmobile\vueapp\VueApp;

/*
- Css files are automatically loaded from vueapp/test/css/
- Template files are automatically loaded from vueapp/test/tpl/
- Js files are automatically loaded from vueapp/test/css/
- Passing packages parameter we require Axios and Moment package

To pass data from php/html to js vue app we defined propsApp attribute data in VueApp configuration and the we fill in html converting from camel case to dash
*/

VueApp::begin([
    'id' => 'vueAppTest',
    'propsData' => [
        'kParam1' => 'value_1',
        'kParam2' => 'value_2',
        'kParam3' => 'value_3',
        'kParamObj' => ['a' => 10],
    ],
    /*
    'jsFiles' => [ ... ],    // list of other js files, that have precedente over js contents path files
    'cssFiles' => [ ... ],    // list of other css files, that have precedente over css contents path files
    'tplFiles' => [ ... ],    // list of other tpl files, that have precedente over tpl contents path files
    */
    'assets' => [ 
      \sfmobile\vueapp\assets\axios\AxiosAsset::class, 
      \sfmobile\vueapp\assets\moment\MomentAsset::class, 
      \sfmobile\vueapp\assets\vue_select\VueSelectAsset::class 
      \sfmobile\vueapp\assets\uid\UivAsset::class 
      \sfmobile\vueapp\assets\vue_bootstrap_datetime_picker\VueBootstrapDatetimePickerAsset::class 
    ]    
]);
?>
    kParam1: {{ propsApp.kParam1 }}
    <br />
    kParam2: {{ propsApp.kParam2 }}
    <br />
    kParam3: {{ propsApp.kParam3 }}
    <br />
    kParamObj: {{ propsApp.kParamObj ? propsApp.kParamObj.a : null }}
    <br />
    <!-- Refer to https://github.com/charliekassel/vuejs-datepicker -->
    <vuejs-datepicker></vuejs-datepicker>
    <br />
    clock datetime: {{ clock_datetime | formatDateTime('DD/MM/YYYY HH:mm') }}
    <br />
    <date-picker name="date" v-model="datePickerValue" :config="vueBootstrapDatetimePickerOptions"></date-picker>


<?php VueApp::end(); 
```

The most important parameter is packages that loads embedded packages such as Axios and Moment.

You have to install all packages declared in `assets` property:

```
$ php composer.phar require --prefer-dist fabriziocaldarelli/yii2-vueapp-uiv "@dev"
$ php composer.phar require --prefer-dist fabriziocaldarelli/yii2-vueapp-vue-select "@dev"
$ php composer.phar require --prefer-dist fabriziocaldarelli/yii2-vueapp-vuejs-datepicker "@dev"
$ php composer.phar require --prefer-dist fabriziocaldarelli/yii2-vueapp-vue-bootstrap-datetime-picker "@dev"
$ php composer.phar require --prefer-dist fabriziocaldarelli/yii2-vueapp-moment "@dev"
$ php composer.phar require --prefer-dist fabriziocaldarelli/yii2-vueapp-axios "@dev"
```


**2) The Vue app js files**

Starting from view path folder, js files for app and components are in vueapp/test/js/*actionName*/ .

For example, the path for main vue app js could be vueapp/test/js/test.js

```js

// Uix package asset - To avoid conflits:
// Vue.use(uiv, {prefix: 'uiv'}) : Components such as <alert> becomes <uiv-alert>

// Vue Select asset - To avoid conflicts:
// Vue.component('v-select', VueSelect.VueSelect);

var ___VUEAPP_APP_ID___ = new Vue({
    el: '#___VUEAPP_APP_ID___',

    // If you need a date picker,
    // add VueApp::PKG_VUEJS_DATEPICKER to 'packages' VueApp widget config
    // Refer to https://github.com/charliekassel/vuejs-datepicker
    components: {
        vuejsDatepicker,                                 // using VueJsDatePicker
        "date-picker": VueBootstrapDatetimePicker,       // using VueBootstrapDatetimePicker - https://github.com/ankurk91/vue-bootstrap-datetimepicker
        'v-select' : VueSelect.VueSelect                             // using VueSelect - https://vue-select.org/guide/install.html#yarn-npm
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

        clock_datetime: null,

        datePickerValue: null,

        vueBootstrapDatetimePickerOptions: {
            // https://momentjs.com/docs/#/displaying/
            format: "DD/MM/YYYY HH:mm",
            locale: 'it',
            useCurrent: false,
            showClear: true,
            showClose: true
        }        
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
                    .replace(/([\p{Lowercase_Letter}\d])(\p{Uppercase_Letter})/gu, `$1-$2`)
                    .replace(/(\p{Lowercase_Letter}+)(\p{Uppercase_Letter}[\p{Lowercase_Letter}\d]+)/gu, `$1-$2`)
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

<h2>1. Pass data from html/php to js</h2>
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
    }
}
```

<h2>2. Component registration</h2>

JS files loading order is important when the app js file depends from other js files.

So, I suggest to prefix all component files with '_' or suffix with '.component.' in order to load component js files firstly.

<h2>3. Pass object data from html/php to js</h2>
Passing objects/array from html/php to js is the same of primitive dat (Tips and triks #1).

The only different thing is that in mounted() function you need to parse json string to the object.

So, if kObject is the object passed from php, mounted() method will be:

```js
mounted() {
    this.readPropsApp();

    // Because kParamObj is an object, we have to parse to serialized version of kParamObj
    this.propsApp.kParamObj = JSON.parse(this.propsApp.kParamObj);
},
```
