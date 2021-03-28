<?php

declare(strict_types=1);

namespace app\abc\controller;

use app\BaseController;
use think\cache\driver\Redis;
use think\facade\View;

class Index extends BaseController
{
    public function __construct()
    {
        parent::initialize();
        $redis = new Redis();
    }
    public function index()
    {
        createModule('test');
        View::assign('list', getPlugins());
        return View::fetch();
    }
}
