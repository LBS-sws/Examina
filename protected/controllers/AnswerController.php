<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class AnswerController extends Controller
{
	public $function_id='TP04';

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
                'actions'=>array('index','view','edit'),
                'expression'=>array('AnswerController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('AnswerController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('TP04');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('TP04');
    }

    public function actionIndex(){
        if(Yii::app()->user->validRWFunction('TP04')){
            $this->redirect("edit");
        }else{
            $this->redirect("view");
        }
    }

    public function actionView()
    {
        $flowTitleModel = new FlowTitleForm('view');
        $this->render('index',array('flowTitleModel'=>$flowTitleModel));
    }

    public function actionEdit()
    {
        $flowTitleModel = new FlowTitleForm('edit');
        $this->render('index',array('flowTitleModel'=>$flowTitleModel));
    }

}