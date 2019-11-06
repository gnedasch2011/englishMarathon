<?php

namespace frontend\modules\tasks\controllers;

use yii\web\Controller;

/**
 * Default controller for the `tasks` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
