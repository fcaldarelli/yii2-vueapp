<?php
namespace sfmobile_unit\vueapp;

use sfmobile\vueapp\VueApp;

class ExampleTest extends TestCase
{
    public function setUp()
    {
        // Change hashCallback to get the same results within different basePath environment 
        $this->mockWebApplication([
            'components' => [
                'assetManager' => [
                    'class' => \yii\web\AssetManager::class,
                    'hashCallback' => function ($path) {
                        $basePath = realpath(\Yii::$app->basePath . '/../');
                        $path = str_replace($basePath, '', $path);
                        return hash('md5', $path);
                    }
                ]
            ]
        ]);        
    }

    /**
     * Example without using render. Property 'contentsPath' is passed to the widget
     */
    public function testPositionEndWithoutRender()
    {
        ob_start();
        VueApp::begin([
            'id' => 'vueAppTest',
            'contentsPath' => __DIR__ . '/views/site',
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
            'packages' => [VueApp::PKG_AXIOS, VueApp::PKG_MOMENT, VueApp::PKG_VUEJS_DATEPICKER],
            'positionJs' => \yii\web\View::POS_END
        ]);
        VueApp::end();
        $out = ob_get_clean();

        $expected = <<<HTML
<div id="vueAppTest" k-param1="value_1" k-param2="value_2" k-param3="value_3" k-param-obj='{"a":10}' v-cloak=""></div>
HTML;


        $this->assertEqualsWithoutLE($expected, $out);        

    }

    /**
     * Example using renderPartial, only view content is rendered
     */
    public function testPositionEndRenderPartial()
    {
        $this->mockAction('site', 'example1');
        $out = \Yii::$app->controller->renderPartial('example1');

        $expected = <<<'HTML'
<div id="vueAppTest" k-param1="value_1" k-param2="value_2" k-param3="value_3" k-param-obj='{"a":10}' v-cloak="">kParam1: {{ propsApp.kParam1 }}
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

</div>
HTML;

        $this->assertEqualsWithoutLE($expected, $out);

        $this->removeMockedAction();
    }


    /**
     * Example using render, fully template
     */
    public function testPositionEndRenderTemplate()
    {
        $this->mockAction('site', 'example1');
        $out = \Yii::$app->controller->render('example1');

        $expected = <<<'HTML'
<html>
<head>
</head>
<body>
<div id="vueAppTest" k-param1="value_1" k-param2="value_2" k-param3="value_3" k-param-obj='{"a":10}' v-cloak="">kParam1: {{ propsApp.kParam1 }}
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

</div><script src="/assets/790eb809b6a96181a6e235d55bf53420/axios.js"></script>
<script src="/assets/87cde7a615738f07989c6f6df2e80f3d/moment.js"></script>
<script src="/assets/02cfdb972ec2474569609e6873b39041/vuejs-datepicker.js"></script>
<script src="/assets/d3d07862364b27932d4b7115e514d288/dist/vue.js"></script>
<script>var vueAppTest = new Vue({
    el: '#vueAppTest',

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
})</script></body>
</html>

HTML;

        $this->assertEqualsWithoutLE($expected, $out);

        $this->removeMockedAction();

    }



    /**
     * Example using render, fully template
     */
    public function testPositionJsReady()
    {
        $this->mockAction('site', 'example1-js-ready');
        $out = \Yii::$app->controller->render('example1-js-ready');

        $expected = <<<'HTML'
<html>
<head>
</head>
<body>
<div id="vueAppTest" k-param1="value_1" k-param2="value_2" k-param3="value_3" k-param-obj='{"a":10}' v-cloak="">kParam1: {{ propsApp.kParam1 }}
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

</div><script src="/assets/790eb809b6a96181a6e235d55bf53420/axios.js"></script>
<script src="/assets/87cde7a615738f07989c6f6df2e80f3d/moment.js"></script>
<script src="/assets/02cfdb972ec2474569609e6873b39041/vuejs-datepicker.js"></script>
<script src="/assets/d3d07862364b27932d4b7115e514d288/dist/vue.js"></script>
<script src="/assets/5a3438f37681977d0304f15694a66f65/jquery.js"></script>
<script>jQuery(function ($) {
var vueAppTest = new Vue({
    el: '#vueAppTest',

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
});</script></body>
</html>

HTML;

        $this->assertEqualsWithoutLE($expected, $out);

        $this->removeMockedAction();
    }    


}
