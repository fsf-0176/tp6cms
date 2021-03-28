<?php

/** 增
 *  远程下载插件
 *  
 */
function remotePlugin($url)
{

    $arr = parse_url($url);
    $fileName = basename($arr['path']);
    $file = file_get_contents($url);
    $files = scandir(getAppPath() . 'plugins');
    $filename = pathinfo($fileName)['filename'];
    foreach ($files as $key => $value) {
        if ($value === $filename) {
            echo '插件已经存在';
            exit;
        }
    }
    // 判断文件夹是否存在
    if (!is_dir('./temp')) {
        mkdir('./temp');
    }

    if (file_put_contents('./temp/' . $fileName, $file)) {
        // 调用解压
        unzip('./temp/' . $fileName, getAppPath() . 'plugins');
        return [
            'code' => 200,
            'msg' => '下载成功'
        ];
    } else {
        return [
            'code' => 403,
            'msg' => '下载失败'
        ];
    }
}


/**
 * 删
 * 删除远程到本地得压缩包，也可以删除插件
 * @param string $path 
 */
function delDir($path)
{
    if (is_dir($path)) {
        // 是文件夹
        $files = scandir($path);
        foreach ($files as $key => $value) {
            if ($value === '.' or $value === '..')
                continue;
            if (is_dir($path . '/' . $value)) {
                delDir($path . '/' . $value);
                @rmdir($path . '/' . $value . '/');
            } else {
                unlink($path . '/' . $value);
            }
        }
        @rmdir($path);
        return true;
    }
    return false;
}

/**
 *  改
 *  修改插件状态
 *  @param string $file 文件路径
 *  @param bool $install 插件是否安装
 *  @param bool $status 插件状态是否启用
 *  @return array
 */
function setPluginStatus(string $file, bool $install, bool $status)
{
    if (!fileWritable($file)) {
        return [
            'code' => 500,
            'msg' => '没有写入权限'
        ];
    }
    $content = file_get_contents($file);
    if (!$content) {
        return [
            'code' => 500,
            'msg' => '没有写入权限'
        ];
    }
    $install = $install ? 1 : 0;
    $status = $status ? 1 : 0;
    // 如果插件卸载了，使用状态也要改变
    $status = $install === 0 ? 0 : $status;
    $regInstall = "/\'\s*install\s*\'\s*=\>\s*\d+\s*,{0,1}\s*\r{0,1}\n{0,1}/";
    $regStatus = "/\'\s*status\s*'\s*=\>\s*\d+\s*,{0,1}\s*\r{0,1}\n{0,1}/";
    $content = preg_filter($regInstall, "'install' => $install,\r\n        ", $content);
    $content = preg_filter($regStatus, "'status' => $status,\r\n        ", $content);
    file_put_contents($file, $content);
    return [
        'code' => 200,
        'msg' => '文件写入完毕'
    ];
}


/**
 * 查
 * 获取插件列表
 * @return array
 */
function getPlugins()
{
    $dir =  getAppPath() . 'plugins';
    $plugins = scandir($dir);
    $list = [];
    foreach ($plugins as $name) {
        $currDir = $dir . DIRECTORY_SEPARATOR . $name;
        if ($name === '.' or $name === '..')
            continue;
        // 如果是文件，退出当前循环
        if (is_file($currDir))
            continue;
        // 如果不是目录，退出当前循环
        if (!is_dir($currDir . DIRECTORY_SEPARATOR))
            continue;
        $info = getPluginInfo($name);
        if ($info) {
            // 增加右侧按钮组
            if (isset($info['code']) && $info['code'] === 404) {
                echo "请检查{$name}插件是否存在\$info属性,在$currDir";
                exit;
            }
            $str = '';
            if ($info['install'] == 1) {
                // 已安装，增加配置按钮
                $str .= '<a class="btn btn-primary btn-xs" href="javascript:void(0)" onclick="$.operate.edit(\'' . $name . '\')"><i class="fa fa-edit"></i> 配置</a> ';
                $str .= '<a class="btn btn-danger btn-xs confirm" href="javascript:void(0)" onclick="$.operate.pluginUninstall(\'' . $name . '\')"><i class="fa fa-edit"></i> 卸载</a> ';
            } else {
                // 未安装，增加安装按钮
                $str = '<a class="btn btn-primary btn-xs" href="javascript:void(0)" onclick="$.operate.pluginInstall(\'' . $name . '\')"><i class="fa fa-edit"></i> 安装</a>';
            }
            $info['button'] = $str;
            $list[] = $info;
        } else {
            echo "请检查{$name}插件是否有Plugin.php文件,在$currDir";
            exit;
        }
    }
    return $list;
}

/**
 *  获取插件属性
 *  @param string 
 *  @return bool
 */
function getPluginInfo(string $file)
{
    $class = "\\plugins\\{$file}\\Plugin";
    // 判断是否有这个类存在
    if (class_exists($class)) {
        // 读取插件配置信息
        if (isset(get_class_vars($class)['info'])) {
            return  get_class_vars($class)['info'];
        } else {
            return [
                'code' => 404,
                'msg' => '找不到info'
            ];
        }
    }
    return false;
}

/**
 * 获取根目录
 * @return string
 */
function getAppPath()
{
    return app()->getRootPath();
}

/**
 * 判断文件或目录是否可写
 * @param    string $file 文件或目录
 * @return    bool
 */
function fileWritable(string $file)
{
    if (is_dir($file)) {
        // 判断目录是否可写
        return is_writable($file);
    } elseif (file_exists($file)) {
        // 文件存在则判断文件是否可写
        return is_writable($file);
    } else {
        // 文件不存在则判断当前目录是否可写
        $file = pathinfo($file, PATHINFO_DIRNAME);
        return is_writable($file);
    }
}


/**
 *  解压压缩包
 *  @param string $zip 压缩包路径
 *  @param string $target 解压到目标路径
 *  @return array
 */
function unzip(string $zipPath, string $target)
{
    $zip = new \ZipArchive;
    if ($zip->open($zipPath)) {
        $zip->extractTo($target);
        if ($zip->close()) {
            // 删除远程到本地的文件
            delDir('./temp');
        }
        return [
            'code' => 200,
            'msg' => '安装成功'
        ];
    } else {
        return [
            'code' => 500,
            'msg' => '安装失败'
        ];
    }
}


/**
 * 把sql表导入数据库
 * @return bool
 */
function importDatabase()
{
    // 获取数据库配置文件
    $config = file_get_contents(getAppPath() . '/.env');
    preg_match_all("/(TYPE|HOSTNAME|DATABASE|USERNAME|PASSWORD|HOSTPORT|PREFIX)\s*=\s*.+\r\n/i", $config, $all);
    $config = [];
    foreach ($all[0] as $key => $value) {
        $k = trim(explode('=', $value)[0]);
        $v = trim(explode('=', $value)[1]);
        $config[$k] = $v;
    }
    // 读取sql文件数据
    $sql = file_get_contents('./static/fastadmin.sql');
    $sql = str_replace("`fa_", "`{$config['PREFIX']}", $sql);
    // 尝试能否自动创建数据库
    try {
        $pdo = new \PDO("{$config['TYPE']}:host={$config['HOSTNAME']};port={$config['HOSTPORT']}", "{$config['USERNAME']}", "{$config['PASSWORD']}");
        $pdo->query("CREATE DATABASE IF NOT EXISTS `{$config['DATABASE']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
        // 连接install命令中指定的数据库
        $instance = \think\facade\Db::connect('mysql');
        $instance->execute("SELECT 1");
        $instance->getPdo()->exec($sql);
        return true;
    } catch (\Throwable $th) {
        dump($th);
    }
}

/**
 * 设置配置文件
 * @param $config array ['HOSTNAME' => 'localhost', 'DATABASE' => 'test', 'USERNAME' => 'root', 'PASSWORD' => 'root', 'HOSTPORT' => 3306, 'PREFIX' => 'sf_']
 * @return array 
 */
function setEnv(array $config)
{
    $replace = function ($replactName, $replaceContents, $content) {
        $reg = "/$replactName\s*=\s*.+\n/i";
        $data = preg_replace($reg, $replaceContents . "\n", $content);
        return $data;
    };
    $cont = file_get_contents(getAppPath() . '/.env');
    $cont = preg_replace('/\r/', "", $cont);
    foreach ($config as $key => $value) {
        $cont = $replace($key, $key . ' = ' . $value, $cont);
    }
    $cont = preg_replace("/\n/", "\r\n", $cont);
    $res = @file_put_contents(getAppPath() . '/.env', $cont);
    if (!$res) {
        return [
            'code' => 403,
            'msg' => '修改数据库配置文件失败'
        ];
    }
    return [
        'code' => 200,
        'msg' => '修改成功'
    ];
}

/**
 *  创建模型、控制器、视图
 */
function createModule(string $name)
{
    $name = ucwords($name);
    $ctrl = getAppPath() . 'app/index/controller/' . $name . '.php';
    $model = getAppPath() . 'app/index/model/' . $name . '.php';
    $viewL = getAppPath() . 'app/index/view/default/' . $name . '_list.html';
    $viewD = getAppPath() . 'app/index/view/default/' . $name . '_detail.html';
    if (is_file($ctrl) || is_file($model) || is_file($viewL) || is_file($viewD)) {
        echo '文件存在';
        return;
    } else {

        $path = getAppPath() . 'public/template/';
        $cCont = preg_replace("/{{.[^}]*}}/", $name, file_get_contents($path . 'ctrl.txt'));
        $mCont = preg_replace("/{{.[^}]*}}/", $name, file_get_contents($path . 'model.txt'));
        $vlCont = preg_replace("/{{.[^}]*}}/", $name, file_get_contents($path . 'view_list.txt'));
        $vdCont = preg_replace("/{{.[^}]*}}/", $name, file_get_contents($path . 'view_detail.txt'));
        file_put_contents($ctrl, $cCont);
        file_put_contents($model, $mCont);
        file_put_contents($viewL, $vlCont);
        file_put_contents($viewD, $vdCont);
    }
}
