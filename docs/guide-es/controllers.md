Controladores
=============

Después de crear las clases de recursos y especificar cómo debe ser el formato de datos de recursos, el siguiente paso
es crear acciones del controlador para exponer los recursos a los usuarios finales a través de las APIs RESTful.

Yii ofrece dos clases controlador base para simplificar tu trabajo de crear acciones REST:
[[Yiisoft\Yii\Rest\Controller]] y [[Yiisoft\Yii\Rest\ActiveController]]. La diferencia entre estos dos controladores
es que este último proporciona un conjunto predeterminado de acciones que están específicamente diseñado para trabajar con
los recursos representados como [Active Record](db-active-record.md). Así que si estás utilizando [Active Record](db-active-record.md)
y te sientes cómodo con las acciones integradas provistas, podrías considerar extender tus controladores
de [[Yiisoft\Yii\Rest\ActiveController]], lo que te permitirá crear potentes APIs RESTful con un mínimo de código.

Ambos [[Yiisoft\Yii\Rest\Controller]] y [[Yiisoft\Yii\Rest\ActiveController]] proporcionan las siguientes características,
algunas de las cuales se describen en detalle en las siguientes secciones:

* Método de Validación HTTP;
* [Negociación de contenido y formato de datos](rest-response-formatting.md);
* [Autenticación](rest-authentication.md);
* [Límite de Rango](rest-rate-limiting.md).

[[Yiisoft\Yii\Rest\ActiveController]] además provee de las siguientes características:

* Un conjunto de acciones comunes necesarias: `index`, `view`, `create`, `update`, `delete`, `options`;
* La autorización del usuario de acuerdo a la acción y recurso solicitado.


## Creando Clases de Controlador <span id="creating-controller"></span>

Al crear una nueva clase de controlador, una convención para nombrar la clase del controlador es utilizar
el nombre del tipo de recurso en singular. Por ejemplo, para servir información de usuario,
el controlador puede ser nombrado como `UserController`.

Crear una nueva acción es similar a crear una acción para una aplicación Web. La única diferencia
es que en lugar de renderizar el resultado utilizando una vista llamando al método `render()`, para las acciones REST
regresas directamente los datos. El [[Yiisoft\Yii\Rest\Controller::serializer|serializer]] y el
[[yii\web\Response|response object]] se encargarán de la conversión de los datos originales
al formato solicitado. Por ejemplo,

```php
public function actionView($id)
{
    return User::findOne($id);
}
```


## Filtros <span id="filters"></span>

La mayoría de las características API REST son proporcionadas por [[Yiisoft\Yii\Rest\Controller]] son implementadas en los términos de [filtros](structure-filters.md).
En particular, los siguientes filtros se ejecutarán en el orden en que aparecen:

* [[yii\filters\ContentNegotiator|contentNegotiator]]: soporta la negociación de contenido, que se explica en
  la sección [Formateo de respuestas](rest-response-formatting.md);
* [[yii\filters\VerbFilter|verbFilter]]: soporta métodos de validación HTTP;
* [[yii\filters\auth\AuthMethod|authenticator]]: soporta la autenticación de usuarios, que se explica en
  la sección [Autenticación](rest-authentication.md);
* [[yii\filters\RateLimiter|rateLimiter]]: soporta la limitación de rango, que se explica en
  la sección [Límite de Rango](rest-rate-limiting.md).

Estos filtros se declaran nombrándolos en el método [[Yiisoft\Yii\Rest\Controller::behaviors()|behaviors()]].
Puede sobrescribir este método para configurar filtros individuales, desactivar algunos de ellos, o añadir los tuyos.
Por ejemplo, si sólo deseas utilizar la autenticación básica HTTP, puede escribir el siguiente código:

```php
use yii\filters\auth\HttpBasicAuth;

public function behaviors()
{
    $behaviors = parent::behaviors();
    $behaviors['authenticator'] = [
        '__class' => HttpBasicAuth::class,
    ];
    return $behaviors;
}
```


## Extendiendo `ActiveController` <span id="extending-active-controller"></span>

Si tu clase controlador extiende de [[Yiisoft\Yii\Rest\ActiveController]], debe establecer
su propiedad [[Yiisoft\Yii\Rest\ActiveController::modelClass|modelClass]] con el nombre de la clase del recurso
que planeas servir a través de este controlador. La clase debe extender de [[yii\db\ActiveRecord]].


### Personalizando Acciones <span id="customizing-actions"></span>

Por defecto, [[Yiisoft\Yii\Rest\ActiveController]] provee de las siguientes acciones:

* [[Yiisoft\Yii\Rest\IndexAction|index]]: listar recursos página por página;
* [[Yiisoft\Yii\Rest\ViewAction|view]]: devolver el detalle de un recurso específico;
* [[Yiisoft\Yii\Rest\CreateAction|create]]: crear un nuevo recurso;
* [[Yiisoft\Yii\Rest\UpdateAction|update]]: actualizar un recurso existente;
* [[Yiisoft\Yii\Rest\DeleteAction|delete]]: eliminar un recurso específico;
* [[Yiisoft\Yii\Rest\OptionsAction|options]]: devolver los métodos HTTP soportados.

Todas esta acciones se declaran a través de método [[Yiisoft\Yii\Rest\ActiveController::actions()|actions()]].
Puedes configurar estas acciones o desactivar alguna de ellas sobrescribiendo el método `actions()`, como se muestra a continuación,

```php
public function actions()
{
    $actions = parent::actions();

    // disable the "delete" and "create" actions
    unset($actions['delete'], $actions['create']);

    // customize the data provider preparation with the "prepareDataProvider()" method
    $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

    return $actions;
}

public function prepareDataProvider()
{
    // prepare and return a data provider for the "index" action
}
```

Por favor, consulta las referencias de clases de acciones individuales para aprender las opciones de configuración disponibles para cada una.


### Realizando Comprobación de Acceso <span id="performing-access-check"></span>

Al exponer los recursos a través de RESTful APIs, a menudo es necesario comprobar si el usuario actual tiene permiso
para acceder y manipular el/los recurso solicitado/s. Con [[Yiisoft\Yii\Rest\ActiveController]], esto puede lograrse
sobrescribiendo el método [[Yiisoft\Yii\Rest\ActiveController::checkAccess()|checkAccess()]] como a continuación, 

```php
/**
 * Checks the privilege of the current user.
 *
 * This method should be overridden to check whether the current user has the privilege
 * to run the specified action against the specified data model.
 * If the user does not have access, a [[ForbiddenHttpException]] should be thrown.
 *
 * @param string $action the ID of the action to be executed
 * @param \yii\base\Model $model the model to be accessed. If `null`, it means no specific model is being accessed.
 * @param array $params additional parameters
 * @throws ForbiddenHttpException if the user does not have access
 */
public function checkAccess($action, $model = null, $params = [])
{
    // check if the user can access $action and $model
    // throw ForbiddenHttpException if access should be denied
    if ($action === 'update' || $action === 'delete') {
        if ($model->author_id !== \Yii::$app->user->id)
            throw new \yii\web\ForbiddenHttpException(sprintf('You can only %s articles that you\'ve created.', $action));
    }
}
```

El método `checkAccess()` será llamado por defecto en las acciones predeterminadas de [[Yiisoft\Yii\Rest\ActiveController]]. Si creas
nuevas acciones y también deseas llevar a cabo la comprobación de acceso, debe llamar a este método de forma explícita en las nuevas acciones.

> Tip: Puedes implementar `checkAccess()` mediante el uso del [Componente Role-Based Access Control (RBAC)](security-authorization.md).
