<?php

namespace plugins\admin\controller;

use think\facade\View;

class Index
{
    public function link()
    {
        
        return View::fetch('info',['list'=>getPlugins()]);
    }
}
