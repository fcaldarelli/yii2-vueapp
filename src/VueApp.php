<?php

/**
 * @package yii2-vueapp
 * @version 1.0.0
 */

namespace sfmobile\vueapp;

use yii\base\Widget;
use yii\helpers\Html;
use sfmobile\vueapp\assets\VueAsset;
use sfmobile\vueapp\assets\MomentAsset;
use sfmobile\vueapp\assets\AxiosAsset;

/**
 * Vue.js App 
 * @author Fabrizio Caldarelli
 * @since 2.0
 * 
 * Content files are in vuejs/<actionName>/js, vuejs/<actionName>/tpl, vuejs/<actionName>/css folders
 */
class VueApp extends Widget
{
    public const PKG_AXIOS = 'axios';
    public const PKG_MOMENT = 'moment';

    public $contentsPath = null;
    public $packages = [ self::PKG_AXIOS ];
    public $debug = false;

    private $jsFiles;
    private $tplFiles;
    private $cssFiles;

    private function checkInit()
    {
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
        $this->jsFiles = [];
        $this->tplFiles = [];
        $this->cssFiles = [];

        if($this->contentsPath == null) 
        {
            $folderName = basename($this->view->viewFile, '.php');
            $this->contentsPath = sprintf('%s/vueapp/%s', dirname($this->view->viewFile), $folderName);
        }

        $this->checkInit();

        if(in_array(self::PKG_AXIOS, $this->packages)) AxiosAsset::register($this->view);
        if(in_array(self::PKG_MOMENT, $this->packages)) MomentAsset::register($this->view);

        VueAsset::register($this->view);

        $this->loadFilesContentsPath();

    }

    public function run()
    {
        $outContent = '';

        // Prepare js files
        foreach ($this->jsFiles as $jsFile) {
            $jsContent = file_get_contents(\Yii::getAlias($jsFile));
            $this->view->registerJs($jsContent);
        }

        // Prepare css files
        foreach ($this->cssFiles as $cssFile) {
            $cssContent = file_get_contents(\Yii::getAlias($cssFile));
            $this->view->registerCss($cssContent);
        }

        // Prepare template files
        foreach ($this->tplFiles as $tplFile) {
            $tplName = basename($tplFile);

            $tplContent = $this->view->render($tplFile);
            $outContent .= Html::tag('script', $tplContent, ['type' => 'text/x-template', 'id' => $tplName]);
        }

        return $outContent;
    }

    private function loadFilesContentsPath()
    {
        if($this->contentsPath != null)
        {
            $basePath = \Yii::getAlias($this->contentsPath);

            $jsPath = $basePath.'/js';
            $this->jsFiles = $this->loadFilesFromPath($jsPath);
            $tplPath = $basePath.'/tpl';
            $this->tplFiles = $this->loadFilesFromPath($tplPath);
            $cssPath = $basePath.'/css';
            $this->cssFiles = $this->loadFilesFromPath($cssPath);
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
