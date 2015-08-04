## 简介

为laravel5做的一个[i18n](https://zh.wikipedia.org/wiki/%E5%9B%BD%E9%99%85%E5%8C%96%E4%B8%8E%E6%9C%AC%E5%9C%B0%E5%8C%96)的插件，具体的实现是使用的[gettext](https://zh.wikipedia.org/wiki/Gettext)函数库来实现的。


## 基础概念

gettext是i18n的具体实现，在计算机处理多语言国际化的时候，大部分都是基于gettext来实现的。gettext不仅是一个函数库，而且还提供一些工具，最常用的有：

- xgettext,用来从程序源代码中提取除要翻译的字符串。通常会把提取除的信息放入一个叫.pot的文件里面。
- msginit,用来把.pot文件生成.po文件。
- msgfmt，用来把.po文件生成为一个.mo文件，.mo文件是一个二进制的文件。.mo文件是用来给程序用的。

### 各个文件的作用

- .pot，模板文件，使用xgettext工具生成。
- .po，具体到某个语言的翻译文本文件。 使用msginit生成。
- .mo，二进制文件，底层程序进行国际化处理用的就是这个文件，它是使用msgfmt生成。

### 运转流程
1. 从源码中提取要进行翻译的文本，生成pot文件。
2. 根据pot文件生成对应语言的po文件，在po文件里面进行翻译。
3. 最后把po文件生成机器能够识别的mo二进制文件。

以上这些操作都可以使用poedit这款GUI软件来完成


## 使用simple/i18n插件

### 安装
在composer.json里面加上一下代码

```javascript

  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/dongyuhappy/i18n.git"
    }
  ],
  "minimum-stability": "dev"
```

然后在require字段新增一个节点
```javascript
"simple/i18n": "master"
```

然后执行
```shell

composer update simple/i18n -vvv

```


### 在laravel5项目里面使用simpl/i18n

1. 在config目录下的app.php里面在找到providers数据节点，把`\Simple\I18n\I18nServiceProvider::class`加进去，注册成为一个服务。
2. 在laravel里面所有注册的服务对外的接口都是以Facade对外提供的，所有要新建一个Facade，代码见下。
3. 在config的app.php里面找到aliases节点新增一个节点`"I18n" => \App\Facades\I18n::class,`
4. 上面3步进行完后，就可以使用`I18n::方法名称`的形式进行相关操作了，正常来说类似国际化的这种需求都是系统在一个地方统一处理的，所以这个时候可以使用`php artisan make:middleware I18nMiddleware`来创建一个中间件。可以在中间件的handler方法里面进行相关操作，代码见下。
5. 然后在你的代码里面可以使用_($msg),gettext($msg),I18n::trans($msg)来操作要翻译的字符串

app\facades\I18n.php 为Facade文件

```php

namespace App\Facades;



use Illuminate\Support\Facades\Facade;

class I18n extends Facade{
    protected static function getFacadeAccessor()
    {
        return 'i18n';
    }
}

```


app\Http\Middleware\I18nModdleware.php为中间件文件

```php

namespace App\Http\Middleware;

use App\Facades\I18n;
use Closure;
use \Illuminate\Http\Request;

class I18nMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @throws \Exception
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {


        I18n::transInit();//初始化操作

        // TODO 可以从Request对象的自定义参数(或者cookie/session)里面取出locale信息，进行转换。
        //TODO I18n::setLocale($locale)

        return $next($request);
    }
}

```

### 配置
目前的默认配置为

```php

return [
    "locale" => "zh_CN",//默认的locale
    "domain" => "message",//默认的domain
    "locale_dir" => base_path("resources" . DIRECTORY_SEPARATOR . 'locale' . DIRECTORY_SEPARATOR),//默认的locale文件所在的根目录
];

```

可以在项目的config目录新建一个i18n.php的配置文件，覆盖你想要配置的字段。


## 注意事项

1. mo文件的命名规则必须是 `:local_dir/:local/LC_MESSAGES/:domian.mo`
2. 待翻译的字符串必须是ASCII







