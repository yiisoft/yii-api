<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yiisoft\Yii\Rest;

/**
 * ActiveController implements a common set of actions for supporting RESTful.
 * By default, the following actions are supported:
 * - `index`: list of models
 * - `view`: return the details of a model
 * - `create`: create a new model
 * - `update`: update an existing model
 * - `delete`: delete an existing model
 * - `options`: return the allowed HTTP methods
 * You may disable some of these actions by overriding [[actions()]] and unsetting the corresponding actions.
 * To add a new action, either override [[actions()]] by appending a new action class or write a new action method.
 * You should use middlewares to check whether the current user has the privilege to perform
 * the specified action against the specified model.
 * For more details and usage information on ActiveController, see the [guide article on rest controllers](guide:rest-controllers).
 */
class ActiveController
{
    /**
     * @var string the model class name. This property must be set.
     */
    public string $modelClass;

    public function __construct(string $modelClass)
    {
        $this->modelClass = $modelClass;
    }

    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'index' => [
                '__class' => Actions\IndexAction::class,
                'modelClass' => $this->modelClass,
            ],
            'view' => [
                '__class' => Actions\ViewAction::class,
                'modelClass' => $this->modelClass,
            ],
            'create' => [
                '__class' => Actions\CreateAction::class,
                'modelClass' => $this->modelClass,
            ],
            'update' => [
                '__class' => Actions\UpdateAction::class,
                'modelClass' => $this->modelClass,
            ],
            'delete' => [
                '__class' => Actions\DeleteAction::class,
                'modelClass' => $this->modelClass,
            ],
            'options' => [
                '__class' => Actions\OptionsAction::class,
            ],
        ];
    }
}
