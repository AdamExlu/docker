<?php
require('../vendor/autoload.php');
use \lrh\docker\model\ContainerConfig;
use \lrh\docker\Client;

$client = new Client;
$client->connectDocker(); //连接docker，不传参数则连接本机docker,若连接远程docker： 192.1.1.1:2375

$config = new ContainerConfig;    //docker配置类
$config->setImage('centos:7');    //镜像
$config->setPorts([81 => 10002, 82 => 10003]);    //绑定端口
$config->setVolumes([ 'testVolumes:/root/temp' ]);    //挂载目录
$config->setMemory(1073741824);   //限制内存

$res = $client->createContainer($config);  //创建容器
if( $res['status'] ){
    $id = $res['container_id'];
    $start_res = $client->startContainer($id);  //启动容器
    if( $start_res['status'] )
        echo '容器创建成功！';
    else
        echo '容器创建失败！';
}else{
    echo $res['info'];
}
