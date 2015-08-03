<?php

namespace Vendor\Simple\I18n;


class I18n
{

    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;


    /**
     *
     * @var string
     */
    protected $domain = null;

    /**
     * 语言
     * @var string
     */
    protected $locale = null;


    /**
     * mo文件所在的目录
     * @var string
     */
    protected $localeDir = null;

    function __construct($app, $domain, $locale, $localeDir)
    {

        $this->setApp($app);
        $this->setDomain($domain);
        $this->setLocale($locale);
        $this->setLocaleDir($localeDir);
    }


    /**
     * @return \Illuminate\Contracts\Foundation\Application
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function setApp($app)
    {
        $this->app = $app;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     * @throws I18nException
     */
    public function setLocale($locale)
    {
        //设置系统的环境变量
        if (putenv("LANGUAGE=" . $locale) === false) {
            throw new I18nException("设置系统环境变量" . "LANGUAGE=" . $locale . '失败~！');
        };//设置locale
        if (setlocale(LC_ALL, $locale) === false) {
            throw new I18nException("selocale" . $locale . '执行失败~！');
        }
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getLocaleDir()
    {
        return $this->localeDir;
    }

    /**
     * @param $localeDir
     * @throws I18nException
     */
    public function setLocaleDir($localeDir)
    {
        $realLocaleDir = realpath($localeDir);
        if ($realLocaleDir === false) {
            throw new I18nException($localeDir . "不存在~！");
        }
        $this->localeDir = $localeDir;
    }


    /**
     * 检查是否准备就绪
     * @return bool
     */
    private function ready()
    {
        if (empty($this->domain) || empty($this->locale) || empty($this->localeDir)) {
            return false;
        }
        return true;
    }

    /**
     * 翻译前的初始化操作
     * @throws I18nException
     */
    public function transInit()
    {
        if ($this->ready() == false) {
            throw new I18nException("请正确设置domain,locale,localeDir信息~！");
        }
        if (bindtextdomain($this->domain, $this->localeDir) === false) {
            throw new I18nException("绑定domain失败~！");
        }

        textdomain($this->domain);//切换域

    }

    /**
     * 翻译
     * @param $msg
     * @return string
     */
    public function trans($msg)
    {
        return _($msg);
    }


}