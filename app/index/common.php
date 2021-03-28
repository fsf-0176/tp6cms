<?php
// 这是系统自动生成的公共文件

use think\cache\driver\Redis;
use think\facade\Db;
use think\facade\Route;

/**
 *  路由有优先级匹配，注意顺序，只要满足条件就不会往下走了
 */
Route::group(function () {
    $redis = new Redis();
    $result = $redis->get('model');
  
    if(!$result){
        $result = Db::name('model')->where('status','1')->select();
        $redis->set('model',$result);
    }
    foreach ($result as $key => $value) {
        // 进入详情页
        Route::get($value['ctrl_name'].'/:show', $value['ctrl_name'].'/show');
        // 进入模型的首页
        Route::get($value['ctrl_name'], $value['ctrl_name'].'/index');
    }
});
