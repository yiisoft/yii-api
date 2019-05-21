<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yiisoft\Yii\Rest\Actions;

use yii\base\Request;
use yii\base\Response;

/**
 * OptionsAction responds to the OPTIONS request by sending back an `Allow` header.
 *
 * For more details and usage information on OptionsAction, see the [guide article on rest controllers](guide:rest-controllers).
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 1.0
 */
class OptionsAction extends \yii\base\Action
{
    /**
     * @var array the HTTP verbs that are supported by the collection URL
     */
    public $collectionOptions = ['GET', 'POST', 'HEAD', 'OPTIONS'];
    /**
     * @var array the HTTP verbs that are supported by the resource URL
     */
    public $resourceOptions = ['GET', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'];
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
     * Responds to the OPTIONS request.
     * @param string $id
     */
    public function run($id = null)
    {
        if ($this->request->getMethod() !== 'OPTIONS') {
            $this->response->setStatusCode(405);
        }
        $options = $id === null ? $this->collectionOptions : $this->resourceOptions;
        $this->response->getHeaderCollection()
            ->set('Allow', implode(', ', $options))
            ->set('Access-Control-Allow-Method', implode(', ', $options));
    }
}
