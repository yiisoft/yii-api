Форматирование ответа
===================

При обработке запросов к RESTful API, приложение обычно выполняет следующие шаги, связанные с форматированием ответа:

1. Определяет различные факторы, которые могут повлиять на формат ответа, такие как media type, язык, версия и т.д.
   Этот процесс также известен как [согласование содержимого](http://en.wikipedia.org/wiki/Content_negotiation).
2. Конвертирует объекты ресурсов в массивы, как описано в секции [Ресурсы](rest-resources.md).
   Этим занимается [[Yiisoft\Yii\Rest\Serializer]].
3. Конвертирует массивы в строки исходя из формата, определенного на этапе согласования содержимого. Это задача для
   [[yii\web\ResponseFormatterInterface|форматтера ответов]], регистрируемого с помощью компонента приложения
   [[yii\web\Response::formatters|response]].


## Согласование содержимого <span id="content-negotiation"></span>

Yii поддерживает согласование содержимого с помощью фильтра [[yii\filters\ContentNegotiator]]. Базовый класс
контроллера RESTful API - [[Yiisoft\Yii\Rest\Controller]] - использует этот фильтр под именем `contentNegotiator`.
Фильтр обеспечивает соответствие формата ответа и определяет используемый язык. Например, если запрос к RESTful API 
содержит следующий заголовок:

```
Accept: application/json; q=1.0, */*; q=0.1
```

То ответ будет в JSON-формате такого вида:

```
$ curl -i -H "Accept: application/json; q=1.0, */*; q=0.1" "http://localhost/users"

HTTP/1.1 200 OK
Date: Sun, 02 Mar 2014 05:31:43 GMT
Server: Apache/2.2.26 (Unix) DAV/2 PHP/5.4.20 mod_ssl/2.2.26 OpenSSL/0.9.8y
X-Powered-By: PHP/5.4.20
X-Pagination-Total-Count: 1000
X-Pagination-Page-Count: 50
X-Pagination-Current-Page: 1
X-Pagination-Per-Page: 20
Link: <http://localhost/users?page=1>; rel=self,
      <http://localhost/users?page=2>; rel=next,
      <http://localhost/users?page=50>; rel=last
Transfer-Encoding: chunked
Content-Type: application/json; charset=UTF-8

[
    {
        "id": 1,
        ...
    },
    {
        "id": 2,
        ...
    },
    ...
]
```

Под капотом происходит следующее: прежде, чем *действие* RESTful API контроллера будет выполнено, фильтр
[[yii\filters\ContentNegotiator]] проверит HTTP-заголовок `Accept` в запросе и установит, что
[[yii\web\Response::format|формат ответа]] должен быть в `'json'`. После того, как *действие* будет выполнено и вернет
итоговый объект ресурса или коллекцию, [[Yiisoft\Yii\Rest\Serializer]] конвертирует результат в массив.
И, наконец, [[yii\web\JsonResponseFormatter]] сериализует массив в строку в формате JSON и включит ее в тело ответа.

По умолчанию, RESTful API поддерживает и JSON, и XML форматы. Для того, чтобы добавить поддержку нового формата,
вы должны установить свою конфигурацию для свойства [[yii\filters\ContentNegotiator::formats|formats]] у фильтра
`contentNegotiator`, например, с использованием поведения такого вида:

```php
use yii\web\Response;

public function behaviors()
{
    $behaviors = parent::behaviors();
    $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_HTML;
    return $behaviors;
}
```

Ключи свойства `formats` - это поддерживаемые MIME-типы, а их значения должны соответствовать именам
форматов ответа, которые установлены в [[yii\web\Response::formatters]].


## Сериализация данных <span id="data-serializing"></span>

Как уже описывалось выше, [[Yiisoft\Yii\Rest\Serializer]] - это центральное место, отвечающее за конвертацию объектов ресурсов
или коллекций в массивы. Он реализует интерфейсы [[yii\base\Arrayable]] и [[yii\data\DataProviderInterface]].
Для объектов ресурсов, как правило, реализуется интерфейс [[yii\base\Arrayable]], а для коллекций -
[[yii\data\DataProviderInterface]].

Вы можете переконфигурировать сериализатор с помощью настройки свойства [[Yiisoft\Yii\Rest\Controller::serializer]], используя
конфигурационный массив. Например, иногда вам может быть нужно помочь упростить разработку клиентской части
приложения с помощью добавления информации о пагинации непосредственно в тело ответа. Чтобы сделать это,
переконфигурируйте свойство [[Yiisoft\Yii\Rest\Serializer::collectionEnvelope]] следующим образом:


```php
use Yiisoft\Yii\Rest\ActiveController;

class UserController extends ActiveController
{
    public $modelClass = \app\models\User::class;
    public $serializer = [
        '__class' => \Yiisoft\Yii\Rest\Serializer::class,
        'collectionEnvelope' => 'items',
    ];
}
```

Тогда вы можете получить следующий ответ на запрос `http://localhost/users`:

```
HTTP/1.1 200 OK
Date: Sun, 02 Mar 2014 05:31:43 GMT
Server: Apache/2.2.26 (Unix) DAV/2 PHP/5.4.20 mod_ssl/2.2.26 OpenSSL/0.9.8y
X-Powered-By: PHP/5.4.20
X-Pagination-Total-Count: 1000
X-Pagination-Page-Count: 50
X-Pagination-Current-Page: 1
X-Pagination-Per-Page: 20
Link: <http://localhost/users?page=1>; rel=self,
      <http://localhost/users?page=2>; rel=next,
      <http://localhost/users?page=50>; rel=last
Transfer-Encoding: chunked
Content-Type: application/json; charset=UTF-8

{
    "items": [
        {
            "id": 1,
            ...
        },
        {
            "id": 2,
            ...
        },
        ...
    ],
    "_links": {
        "self": {
            "href": "http://localhost/users?page=1"
        },
        "next": {
            "href": "http://localhost/users?page=2"
        },
        "last": {
            "href": "http://localhost/users?page=50"
        }
    },
    "_meta": {
        "totalCount": 1000,
        "pageCount": 50,
        "currentPage": 1,
        "perPage": 20
    }
}
```

### Настройка форматирования JSON

Ответ в формате JSON генерируется при помощи класса [[yii\web\JsonResponseFormatter|JsonResponseFormatter]], который
использует внутри [[yii\helpers\Json|класс-помощник JSON]]. Данный форматтер гибко настраивается. Например, 
опция [[yii\web\JsonResponseFormatter::$prettyPrint|$prettyPrint]] полезна на время разработки, так как при
её использовании ответы получаются более читаемыми. [[yii\web\JsonResponseFormatter::$encodeOptions|$encodeOptions]]
может пригодиться для более тонкой настройки кодирования.

Свойство [[yii\web\Response::formatters|formatters]] компонента приложения `response` может быть
[сконфигурировано](concept-configuration.md) следующим образом:

```php
'response' => [
    // ...
    'formatters' => [
        yii\web\Response::FORMAT_JSON => [
            '__class' => yii\web\JsonResponseFormatter::class,
            'prettyPrint' => YII_DEBUG, // используем "pretty" в режиме отладки
            'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
            // ...
        ],
    ],
],
```

При работе с базой данных через [DAO](db-dao.md), все данные представляются в виде строк, что не всегда корректно.
Особенно учитывая, что в JSON для чисел есть соответствующий тип. При использовании ActiveRecord значения числовых
столбцов приводятся к integer на этапе выборки из базы: [[yii\db\ActiveRecord::populateRecord()]].
