<?php

namespace plugins\test\controller;
use think\facade\View;
class Index
{
    public function link()
    {
        hook('testhook');
        return View::fetch('info');
    }
}
