<?php

namespace plugins\test;

use think\plugins;

/**
 *  插件测试
 *  @author sifa·Fang
 */

class Plugin extends plugins
{
    public $info = [
        'name' => 'test',
        'title' => '测试插件hh',
        'description' => 'thinkphp6插件测试',
        'status' => 1,
        'install' => 1,
        'author' => 'sifa·Fang',
        'version' => '1.0'
    ];
    /**
     *  安装方法
     *  @return bool
     */
    public function install()
    {
        return true;
    }
    /**
     *  卸载方法
     *  @return bool
     */
    public function uninstall()
    {
        return true;
    }

    public function testhook()
    {
        return $this->fetch('info');
    }

}
