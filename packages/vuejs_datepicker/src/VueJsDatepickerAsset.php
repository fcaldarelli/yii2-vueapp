<?php

/**
 * @package yii2-vueapp
 * @version 1.0.0
 */

namespace sfmobile\vueapp\packages\vuejs_datepicker;

use yii\web\AssetBundle;

/**
 * VueJs Datepicker asset bundle
 * @author Fabrizio Caldarelli
 * @since 1.0
 */
class VueJsDatepickerAsset extends AssetBundle
{
    public $sourcePath = '@vendor/npm-asset/vuejs-datepicker/dist';
    public function init()
    {
        parent::init();
        $this->js[] = YII_DEBUG ? 'vuejs-datepicker.js' : 'vuejs-datepicker.min.js';
    }    
}
