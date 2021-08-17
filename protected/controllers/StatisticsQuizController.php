<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2020/12/23 0007
 * Time: 上午 10:10
 */
class StatisticsQuizController extends Controller
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
                'expression'=>array('StatisticsQuizController','allowReadOnly'),
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
        $model = new StatisticsQuizList;
        if (isset($_POST['StatisticsQuizList'])) {
            $model->attributes = $_POST['StatisticsQuizList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['statisticsQuiz_01']) && !empty($session['statisticsQuiz_01'])) {
                $criteria = $session['statisticsQuiz_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPageAll($model->pageNum);
        $this->render('index',array('model'=>$model));
    }
}