<?php
// 这是系统自动生成的公共文件
/**
 * 获取插件方法
 */
use think\facade\Route;
Route::group(function () {
        // 后台动态路由
        Route::any(':controller', 'index');
});