<?php

namespace app\controllers;

use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use yii\filters\auth\HttpBasicAuth;

class BookController extends ActiveController
{

	public $modelClass = 'app\models\Book';

	public function behaviors()
{
    $behaviors = parent::behaviors();

    // remove authentication filter
    $auth = $behaviors['authenticator'];
    unset($behaviors['authenticator']);

    // add CORS filter
    $behaviors['corsFilter'] = [
        'class' => \yii\filters\Cors::className(),
    ];

    // re-add authentication filter
    $behaviors['authenticator'] = $auth;
    // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
    $behaviors['authenticator']['except'] = ['options'];

    return $behaviors;
}

    public function actionSearch(){
	    if (!empty($_GET)) {
	        $model = new $this->modelClass;
	        foreach ($_GET as $key => $value) {
	            if (!$model->hasAttribute($key)) {
	                throw new \yii\web\HttpException(404, 'Invalid attribute:' . $key);
	            }
	        }
	        try {
	        	$query = $model->find();
	        	foreach ($_GET as $key => $value) {
	        		$query->andWhere(['like', $key, $value]);
	        	}
	            $provider = new ActiveDataProvider([
	                'query' => $query,
	                'pagination' => false
	            ]);
	        } catch (Exception $ex) {
	            throw new \yii\web\HttpException(500, 'Internal server error');
	        }

	        if ($provider->getCount() <= 0) {
	            throw new \yii\web\HttpException(404, 'No entries found with this query string');
	        } else {
	            return $provider;
	        }
	    } else {
	        throw new \yii\web\HttpException(400, 'There are no query string');
	    }
	}

}
