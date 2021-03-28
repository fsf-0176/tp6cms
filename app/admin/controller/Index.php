<?php

declare(strict_types=1);

namespace app\admin\controller;

use app\BaseController;
use think\cache\driver\Redis;
use think\facade\Request;
use think\facade\View;

class Index extends BaseController
{
    public function __construct()
    {
        $redis = new Redis();
        // $redis->set('test','这是存储的内容');
        // 插件禁用/开启操作
        // $file = app()->getRootPath() . 'plugins' . DIRECTORY_SEPARATOR . 'test' . DIRECTORY_SEPARATOR . 'Plugin.php';
        // $res = setPluginStatus($file,true,true);
        // 远程下载插件
        // remotePlugin('http://119.29.108.194/Public/remote.zip');
        // 卸载插件
        // delDir(getAppPath().'/plugins/remote');
        //创建数据表
        // setEnv(
        //     [
        //         'HOSTNAME' => 'localhost',
        //         'DATABASE' => 'test',
        //         'USERNAME' => 'root',
        //         'PASSWORD' => 'root',
        //         'HOSTPORT' => 3306,
        //         'PREFIX' => 'fsf_'
        //     ]
        // );
        // 删除数据表
        // \think\facade\Db::execute('DROP TABLE fsf_sms');
        // 数据库配置好后直接导入数据
        // $this->importDatabase();
        // echo   phpinfo();
        // var_dump($redis->get('test'));
    }
    public function index()
    {
        $path = Request::pathinfo();
        $url = explode('/',$path)[0];
        View::assign('list', getPlugins());
        return View::fetch($url);
    }
}
