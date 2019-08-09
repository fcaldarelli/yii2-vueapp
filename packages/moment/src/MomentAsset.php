<?php

/**
 * @package yii2-vueapp
 * @version 1.0.0
 */

namespace sfmobile\vueapp\packages\moment;

use yii\web\AssetBundle;

/**
 * Moment asset bundle
 * @author Fabrizio Caldarelli
 * @since 1.0
 */
class MomentAsset extends AssetBundle
{
    public $sourcePath = '@vendor/npm-asset/moment';
    public function init()
    {
        parent::init();
        $this->js[] = YII_DEBUG ? 'min/moment.min.js' : 'moment.js';
    }    
}
