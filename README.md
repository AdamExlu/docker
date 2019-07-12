### 功能
基于 docker-php/docker-php composer包，对常用的docker功能进行二次封装，使用更加简单易懂。

### 使用
直接composer安装
```
composer require lrh/docker 
```

使用说明
------------

对docker的操作都封装在lrh\docker\Client类里面

使用的第一步，创建一个lrh\docker\Client类对象，并连接docker
```
$client = new Client;
//连接docker，不传参数则连接本机docker,若连接远程docker： 192.0.0.1:2375
$client->connectDocker(); 
```

若要创建一个容器，先创建一个lrh\docker\model\ContainerConfig类对象，将要创建的容器设置配置到相应的对象属性，然后传入创建容器方法
```
$config = new ContainerConfig;          //docker配置类
$config->setImage('centos:7');          //设置镜像
$config->setPorts([81 => 10002, 82 => 10003]);          //绑定端口
$config->setVolumes([ 'testVolumes:/root/temp' ]);      //挂载目录
$config->setMemory(1073741824);         //限制内存
$client->createContainer($config);      //创建容器

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
```



