<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2020/12/23 0007
 * Time: 上午 10:10
 */
class StatisticsAllController extends Controller
{
	public $function_id='SC04';

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
                'expression'=>array('StatisticsAllController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('SC04');
    }

    public function actionIndex($pageNum=0){
        $model = new StatisticsAllList;
        if (isset($_POST['StatisticsAllList'])) {
            $model->attributes = $_POST['StatisticsAllList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['statisticsAll_01']) && !empty($session['statisticsAll_01'])) {
                $criteria = $session['statisticsAll_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPageAll($model->pageNum);
        $this->render('index',array('model'=>$model));
    }
}