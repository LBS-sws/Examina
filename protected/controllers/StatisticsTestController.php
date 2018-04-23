<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class StatisticsTestController extends Controller
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
                'actions'=>array('index','view','detail'),
                'expression'=>array('StatisticsTestController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('SC01');
    }

    public function actionIndex($pageNum=0){
        $model = new StatisticsTestList;
        if (isset($_POST['StatisticsTestList'])) {
            $model->attributes = $_POST['StatisticsTestList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['statisticsTest_01']) && !empty($session['statisticsTest_01'])) {
                $criteria = $session['statisticsTest_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionView($index,$pageNum=0)
    {
        if(!empty($index)&&is_numeric($index)){
            $model = new StatisticsViewList;
            if (isset($_POST['StatisticsViewList'])) {
                $model->attributes = $_POST['StatisticsViewList'];
            } else {
                $session = Yii::app()->session;
                if (isset($session['statisticsView_02']) && !empty($session['statisticsView_02'])) {
                    $criteria = $session['statisticsView_02'];
                    $model->setCriteria($criteria);
                }
            }
            $model->determinePageNum($pageNum);
            $model->retrieveDataByPage($index,$model->pageNum);
            $this->render('view',array('model'=>$model));
        }
    }

    public function actionDetail($index,$staff)
    {
        $model = new StatisticsViewForm('view');
        if (!$model->retrieveData($index,$staff)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

}