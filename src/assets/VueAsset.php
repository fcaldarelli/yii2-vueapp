<?php

/**
 * @package yii2-vueapp
 * @version 1.0.0
 */

namespace sfmobile\vueapp\assets;

use yii\web\AssetBundle;

/**
 * Vue.js asset bundle
 * @author Fabrizio Caldarelli
 * @since 1.0
 */

class VueAsset extends AssetBundle
{
    public $sourcePath = '@vendor/npm-asset/vue';
    public function init()
    {
        parent::init();
        $this->js[] = YII_DEBUG ? 'dist/vue.js' : 'dist/vue.min.js';
    }
}
