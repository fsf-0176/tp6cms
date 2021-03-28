<?php

declare(strict_types=1);

namespace app\index\controller;

use app\BaseController;
use think\facade\View;

class Picture extends BaseController
{
    /**
     * 类的构造方法
     */
    private $theme = 'concise';
    public function __construct()
    {
        // 初始化baseController
        parent::initialize();
    }
    public function index()
    {
        return View::fetch($this->theme.'/Picture_list');
    }
    public function show()
    {
        return View::fetch($this->theme.'/Picture_detail');
    }
}
