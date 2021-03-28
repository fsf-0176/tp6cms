<?php

namespace plugins\admin;

use think\plugins;

/**
 *  插件测试
 *  @author sifa·Fang
 */

class Plugin extends plugins
{
    public $info = [
        'name' => 'admin',
        'title' => '测试插件',
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

    /**
     *  实现testhook钩子方法
     *  @return mixed
     */
    public function adminhook($param = array())
    {
        $this->assign('list',$param);
        return $this->fetch();
    }

}
