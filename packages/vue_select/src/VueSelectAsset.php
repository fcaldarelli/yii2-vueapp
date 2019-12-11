<?php

/**
 * @package yii2-vueapp
 * @version 1.0.0
 */

namespace sfmobile\vueapp\packages\vue_select;

use yii\web\AssetBundle;

/**
 * Vue Select asset bundle
 * @author Fabrizio Caldarelli
 * @since 1.0
 */
class VueSelectAsset extends AssetBundle
{
    public $sourcePath = '@vendor/npm-asset/vue-select/dist';
    public function init()
    {
        parent::init();
        $this->js[] = YII_DEBUG ? 'vue-select.js' : 'vue-select.js';
        $this->css[] = YII_DEBUG ? 'vue-select.css' : 'vue-select.css';
    }    
}
