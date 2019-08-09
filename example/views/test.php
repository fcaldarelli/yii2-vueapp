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
    ],
    'packages' => [VueApp::PKG_AXIOS, VueApp::PKG_MOMENT]
]);
?>
    kParam1: {{ propsApp.kParam1 }}
    <br />
    kParam2: {{ propsApp.kParam2 }}
    <br />
    kParam3: {{ propsApp.kParam3 }}
    <br />
    clock datetime: {{ clock_datetime | formatDateTime('DD/MM/YYYY HH:mm') }}

<?php VueApp::end(); ?>