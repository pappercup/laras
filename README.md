# laras
just for learn

### 使用:

* 依赖

    * php >= 7.2.0
    * laravel = 5.5.*
    * swoole >= 4.0.4

* 安装

``` bash
composer require pappercup/laras
php artisan vendor:publish
```

* 配置

    config/swoole.php
    
* 使用 

```bash
// http: start stop reload restart
php artisan swoole:http start

// websocket
php artisan swoole:webSocket start
```
*  TODO:
    
    * pool
    * ...