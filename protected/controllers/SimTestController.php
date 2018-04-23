<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class SimTestController extends Controller
{

    public function filters()
    {
        return array(
            'enforceSessionExpiration',
            'enforceNoConcurrentLogin',
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow',
                'actions'=>array('index','save'),
                'expression'=>array('SimTestController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('EM02');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('EM02');
    }

    public static function allowRead() {
        return true;
    }

    public function actionIndex(){
        $model = new SimTestForm('new');
        $this->render('index',array('model'=>$model));
    }

    public function actionSave()
    {
        if (isset($_POST['SimTestForm'])) {
            $model = new SimTestForm("save");
            $model->attributes = $_POST['SimTestForm'];
            if ($model->validate()) {
                $quizModel = new Examina(-1);
                $quizModel->_testNum = $model->exa_num;
                $quizModel->roundList();
                $this->render('form',array('model'=>$quizModel,));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('index',array('model'=>$model,));
            }
        }
    }
}