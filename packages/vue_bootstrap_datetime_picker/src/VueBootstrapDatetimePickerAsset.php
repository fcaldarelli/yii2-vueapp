<?php

namespace sfmobile\vueapp\packages\vue_bootstrap_datetime_picker;

use yii\web\AssetBundle;

/**
 * VueBootstrapDatetimePickerAsset asset bundle.
 */
class VueBootstrapDatetimePickerAsset extends AssetBundle
{
    public $sourcePath = '@vendor/npm-asset';
    public function init()
    {
        parent::init();
        $this->js[] = YII_DEBUG ? 'vue-bootstrap-datetimepicker/dist/vue-bootstrap-datetimepicker.js' : 'vue-bootstrap-datetimepicker/dist/vue-bootstrap-datetimepicker.min.js';
        $this->js[] = YII_DEBUG ? 'pc-bootstrap4-datetimepicker/build/js/bootstrap-datetimepicker.min.js' : 'pc-bootstrap4-datetimepicker/build/js/bootstrap-datetimepicker.min.js';
        $this->css[] = YII_DEBUG ? 'pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.min.css' : 'pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.min.css';
    }
    public $depends = [
        'yii\web\JqueryAsset',
        'sfmobile\vueapp\packages\moment\MomentAsset',
    ];
}
