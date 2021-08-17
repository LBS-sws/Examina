<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class FlowPlanController extends Controller
{
	public $function_id='TP07';

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
                'actions'=>array('index','edit'),
                'expression'=>array('FlowPlanController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('view'),
                'expression'=>array('FlowPlanController','allowReadOnly'),
            ),
            array('allow',
                'actions'=>array('ajaxFlowPlan'),
                'expression'=>array('FlowTitleController','allowWrite'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowWrite() {
        return !empty(Yii::app()->user->id);
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('TP07');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('TP07');
    }

    public function actionIndex($pageNum=0){
        $model = new FlowPlanList;
        if (isset($_POST['FlowPlanList'])) {
            $model->attributes = $_POST['FlowPlanList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['flowPlan_01']) && !empty($session['flowPlan_01'])) {
                $criteria = $session['flowPlan_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionEdit($index)
    {
        $model = new FlowPlanForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new FlowPlanForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }
}