<?php

/**
 * @package yii2-vueapp
 * @version 1.0.0
 */

namespace sfmobile\vueapp;

use yii\base\Widget;
use yii\helpers\Html;
use sfmobile\vueapp\assets\VueAsset;

/**
 * Vue.js App 
 * @author Fabrizio Caldarelli
 * @since 2.0
 * 
 * Content files are in vueapp/<actionName>/js, vueapp/<actionName>/tpl, vueapp/<actionName>/css folders
 */
class VueApp extends Widget
{
    const PKG_AXIOS = 'axios';
    const PKG_MOMENT = 'moment';
    const PKG_VUEJS_DATEPICKER = 'vuejs_datepicker';
    const PKG_UIV = 'uiv';

    /**
     * id of vue app
     */
    public $id = null;

    /**
     * tag of vue app container
     */
    public $tag = 'div';

    /**
     * add v-cloak options to tag vue app container
     */
    public $vCloak = true;    

    /**
     * props data to pass to js script
     */
    public $propsData = [];

    /**
     * other options appended to tag vue app container
     */
    public $options = [];

    /**
     * path of /js, /css and /tpl files
     */
    public $contentsPath = null;

    /**
     * js files passed from user
     */
    public $jsFiles = [];

    /**
     * css files passed from user
     */
    public $cssFiles = [];

    /**
     * tpl files passed from user
     */
    public $tplFiles = [];

    /**
     * packages to load from assets
     */
    public $packages = [ self::PKG_AXIOS ];

    /**
     * Position registration js file. Default is View::POS_READY
     */
    public $positionJs = \yii\web\View::POS_READY;

    /**
     * debug mode
     */
    public $debug = false;

    /**
     * js files from contents path
     */
    private $contentsPathJsFiles = null;

    /**
     * tpl files from contents path
     */
    private $contentsPathTplFiles = null;

    /**
     * css files from contents path
     */
    private $contentsPathCssFiles = null;

    private function checkInit()
    {
        if($this->id == null)
        {
            throw new \Exception("Missing 'id' parameter");
        }

        if($this->contentsPath == null)
        {
            throw new \Exception("Missing contentsPath (usually __DIR__)");
        }
        else
        {
            if(file_exists($this->contentsPath) == false)
            {
                throw new \Exception( sprintf("contentsPath %s does not exist", $this->contentsPath) );
            }
        }

    }

    public function init()
    {
        parent::init();

        if($this->contentsPath == null) 
        {
            $folderName = basename($this->view->viewFile, '.php');
            $this->contentsPath = sprintf('%s/vueapp/%s', dirname($this->view->viewFile), $folderName);
        }

        $this->checkInit();

        // Load packages
        if(in_array(self::PKG_AXIOS, $this->packages)) \sfmobile\vueapp\packages\axios\AxiosAsset::register($this->view);
        if(in_array(self::PKG_MOMENT, $this->packages)) \sfmobile\vueapp\packages\moment\MomentAsset::register($this->view);
        if(in_array(self::PKG_VUEJS_DATEPICKER, $this->packages)) \sfmobile\vueapp\packages\vuejs_datepicker\VueJsDatepickerAsset::register($this->view);
        if(in_array(self::PKG_UIV, $this->packages)) \sfmobile\vueapp\packages\uiv\UivAsset::register($this->view);

        VueAsset::register($this->view);

        $this->loadFilesContentsPath();

        ob_start();
    }

    public function run()
    {
        $outContent = '';

        // Prepare js files
        foreach ($this->jsFiles as $jsFile) {
            $jsContent = $this->replaceJsTokens(file_get_contents(\Yii::getAlias($jsFile)));
            $this->view->registerJs($jsContent, $this->positionJs);
        }
        foreach ($this->contentsPathJsFiles as $jsFile) {
            $jsContent = $this->replaceJsTokens(file_get_contents(\Yii::getAlias($jsFile)));
            $this->view->registerJs($jsContent, $this->positionJs);
        }

        // Prepare css files
        foreach ($this->cssFiles as $cssFile) {
            $cssContent = file_get_contents(\Yii::getAlias($cssFile));
            $this->view->registerCss($cssContent);
        }
        foreach ($this->contentsPathCssFiles as $cssFile) {
            $cssContent = file_get_contents(\Yii::getAlias($cssFile));
            $this->view->registerCss($cssContent);
        }

        // Prepare template files
        foreach ($this->tplFiles as $tplFile) {
            $tplName = pathinfo($tplFile, PATHINFO_FILENAME);

            $tplContent = $this->view->renderFile($tplFile);
            $outContent .= Html::tag('script', $tplContent, ['type' => 'text/x-template', 'id' => $tplName]);
        }
        foreach ($this->contentsPathTplFiles as $tplFile) {
            $tplName = pathinfo($tplFile, PATHINFO_FILENAME);

            $tplContent = $this->view->renderFile($tplFile);
            $outContent .= Html::tag('script', $tplContent, ['type' => 'text/x-template', 'id' => $tplName]);
        }

        // Get inside widget content, between begin() ... end() method
        $insideWidgetContent = ob_get_clean();

        // Add options to html tag
        $htmlTagOptions = ['id' => $this->id];
        foreach($this->propsData as $key => $val)
        {
            $htmlTagOptions[\yii\helpers\Inflector::camel2id($key)] = $val;
        }
        if($this->vCloak) $htmlTagOptions['v-cloak'] = '';
        $htmlTagOptions = array_merge($htmlTagOptions, $this->options);

        // Fill output content
        $outContent .= Html::beginTag($this->tag, $htmlTagOptions);
        $outContent .= $insideWidgetContent;     
        $outContent .= Html::endTag($this->tag);

        return $outContent;
    }

    /**
     * Replace all tokens occurrences in js files
     * Token:
     *    ___VUE_APP_ID___ : app id
     */
    private function replaceJsTokens($content)
    {
        $content = str_replace('___VUEAPP_APP_ID___', $this->id, $content);
        return $content;
    }

    private function loadFilesContentsPath()
    {
        if($this->contentsPath != null)
        {
            $basePath = \Yii::getAlias($this->contentsPath);

            $jsPath = $basePath.'/js';
            $this->contentsPathJsFiles = $this->loadFilesFromPath($jsPath);
            $tplPath = $basePath.'/tpl';
            $this->contentsPathTplFiles = $this->loadFilesFromPath($tplPath);
            $cssPath = $basePath.'/css';
            $this->contentsPathCssFiles = $this->loadFilesFromPath($cssPath);
        }
    }

    private function loadFilesFromPath($path)
    {
        $arrOut = [];

        if(file_exists($path))
        {
            $scandir = scandir($path);
            foreach($scandir as $file)
            {
                $pathFile = sprintf('%s/%s', $path, $file);
                if(is_file($pathFile)) $arrOut[] = $pathFile;
            }
        }
        else
        {
            if($this->debug)
            {
                throw new \Exception( sprintf('path %s does not exist', $path) );
            }
        }

        return $arrOut;
    }


}
