<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class StatisticsTitleController extends Controller
{
	public $function_id='SC02';

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
                'actions'=>array('index'),
                'expression'=>array('StatisticsTitleController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('SC02');
    }

    public function actionIndex($pageNum=0){
        $model = new StatisticsTitleList;
        if (isset($_POST['StatisticsTitleList'])) {
            $model->attributes = $_POST['StatisticsTitleList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['statisticsTitle_01']) && !empty($session['statisticsTitle_01'])) {
                $criteria = $session['statisticsTitle_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }
}