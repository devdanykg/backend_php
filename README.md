# Кейс №4 - Точка входа в API.
### Данная работа выполнена как тестовое задание
### Данный api выполнен так, как я бы его выполнил на прод.
#### Можно его было бы выполнить без зависимостей, обычной конструкцией switch с получением REQUEST_METHOD, чисто для показа моей логики суждения, но считаю, что такое даже нельзя показывать. Кучу неудобств, проблем и утечек может произойти. Поэтому работу выполнил именно в таком ключе.
---
Backend выполнен на чистом PHP с использованием следующих зависимостей:
+ [zendframework/zend-diactoros](https://github.com/zendframework/zend-diactoros)
+ [relay/relay](https://github.com/relayphp/Relay.Relay)
+ [php-di/php-di](https://github.com/PHP-DI/PHP-DI)
+ [doctrine/annotations](https://github.com/doctrine/annotations)
+ [mongodb/mongodb](https://github.com/mongodb/mongo-php-library)
+ [http-interop/response-sender](https://github.com/http-interop/response-sender)
+ [middlewares/request-handler](https://github.com/middlewares/request-handler)
+ [middlewares/fast-route](https://github.com/middlewares/fast-route)

---

В папке Tests распологаются тесты ко всем готовым методам(хоть их и немного).
Для взаимодействия с методами данные необходимо поместить в json строку и передавать в POST запросе.
Для авторизации чтобы взаимодействовать с методами - в заголовки необходимо добавить `Authorization: Basic Base64_String`
Данные шифруются `base64` в виде `user:password`.

p.s. Postman изначально их шифрует!

## Документация

> /bank - Метод взаимодействия со списком банков.
> > /getBanks **GET** - принимающие параметры отсутствуют.
> > > Выводит список всех банков.
> > > > `Данный запрос работает только с присланным заголовком Authorization!`

### Затрачено времени на разработку ± 2,5 часа.

#### Во время разработки я:
+ ##### Освоил работу с базой данных mongoDb. 
+ ##### Подкрепил навыки написания кода на PHP из-за длительного отсутствия практики.
