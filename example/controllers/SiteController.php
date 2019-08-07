<?php
namespace app\controllers;

use yii\web\Controller;

/**
 * Site controller
 */
class SiteController extends Controller
{
    public function actionTest()
    {
        return $this->render('test');
    }
}
