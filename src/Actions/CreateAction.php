<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yiisoft\Yii\Rest\Actions;

use yii\base\Model;
use yii\helpers\Url;
use yii\base\Request;
use yii\base\Response;
use yii\web\ServerErrorHttpException;
use Yiisoft\Yii\Rest\Action;

/**
 * CreateAction implements the API endpoint for creating a new model from the given data.
 *
 * For more details and usage information on CreateAction, see the [guide article on rest controllers](guide:rest-controllers).
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 1.0
 */
class CreateAction extends Action
{
    /**
     * @var string the scenario to be assigned to the new model before it is validated and saved.
     */
    public $scenario = Model::SCENARIO_DEFAULT;
    /**
     * @var string the name of the view action. This property is need to create the URL when the model is successfully created.
     */
    public $viewAction = 'view';
    /**
     * @var \yii\web\Request
     */
    protected $request;
    /**
     * @var \yii\web\Response
     */
    protected $response;

    public function __construct($id, $controller, Request $request, Response $response)
    {
        parent::__construct($id, $controller);
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Creates a new model.
     * @return \Yiisoft\ActiveRecord\ActiveRecordInterface the model newly created
     * @throws ServerErrorHttpException if there is any error when creating the model
     */
    public function run()
    {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }

        /* @var $model \Yiisoft\ActiveRecord\ActiveRecord */
        $model = new $this->modelClass([
            'scenario' => $this->scenario,
        ]);

        $model->load($this->request->getParsedBody(), '');

        if ($model->save()) {
            $this->response->setStatusCode(201);
            $id = implode(',', $model->getPrimaryKey(true));
            $this->response->getHeaderCollection()->set('Location', Url::toRoute([$this->viewAction, 'id' => $id], true));
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $model;
    }
}
