Виджеты регистрируются в панели администратора либо через функцию set_widget()
При регистрации виджета формируется файловая архитектура. 

В админ панели добавляются домены. Название - домен полностью. В правой колонке в поле Ярлык нужно ввести полное название домена, только с дефисами вместо точек.

Чтобы виджет подключился к домену, нужно создать заказ. В Заказе выбираются домен и виджет, устанавливается стоимость и срок, до которого виджет будет активен.

При добавлении виджета в админ-панели создается файловая структура в папке widgets
Для каждого виджета создается папка. В ней два раздела: с файлами виджета и с настройками
В папке с файлами должны быть:
 - индексный файл (указывается как обработчик для redirect_uri)
 - view.php (интерфейс виджета) - При подключении внутри marketplace 
 - файлы обработки хуков (hook_*name*.php). На них будет редирект при вебхуках
 - в папке assets будут папки js и css. В них - файлы, которые будут регистрироваться для включения через виджет Marketplace 

 В файлах виджета можно подключить файл loader.php из корневой папки темы. Таким образом будут доступны функции WordPress и ряд дополнительных методов и констант.

 При установке самостоятельного виджета и получении токенов необходимо зарегистрировать виджет. Для этого вызывается функция set_widget($data) из файла loader.php
$data - ассоциативный массив с такими полями:
- slug (короткое название виджета) - обязательное
- domain (полное название домена, для которого активирован виджет) - обязательное
- title (название виджета кириллицей) - не обязательное. При передаче виджет добавится в админку
- amo (ассоциативный массив с полями clientId и clientSecret) - не обязательное
- tokens (ассоциативный массив с полями access_token, refresh_token, expires, token_type, base_domain) - не обязательное
Метод можно вызывать несколько раз, записывая разные данные. Обязательные поля передаются при каждом вызове.

 В интерфейсе АМОСРМ скрипты виджета будут подключаться из двух файлов внутри '/widgets/slug виджета/files/assets/js/':
 - always.js (скрипты, которые должны отрабатывать при каждой смене url)
 - once.js (скрипты, которые срабатывают однажды при загрузке АМО)

 Также станет доступны функции get_tokens($domain, $widget) и set_token($domain, $widget) для получения и сохранения токенов. При получении токенов через get_tokens происходит обновление, если в этом есть необходимость

 Запросы на получение/сохранение настроек идут на адрес https://marketplace.market.com/widget/*widget-slug*/
 Где widget-slug - слаг виджета, указанный при его регистрации и в постоянных ссылках админки. 
 На этот же адрес отправляются все веб-хуки, которые в дальнейшем будут перенаправлены на скрипты-обоработчики.
 При получении/записи настроек необходимо передать параметры amodomain(полное имя домена в амо). Также при сохранении настроек или данных для авторизации они передаются в параметрах settings и auth_data соответственно