<?php
namespace lrh\docker;
use Docker\Docker;
use Docker\DockerClientFactory;
use lrh\docker\model\ContainerConfig;
use Docker\API\Model\ContainersCreatePostBody;
use Docker\API\Model\PortBinding;
use Docker\API\Model\HostConfig;
use Docker\API\Model\RestartPolicy;


class Client 
{
    public $docker;

    /**
     * 连接docker
     * @param string $remote 远程 IP:端口
     * @return Docker\Docker
     */
    public function connectDocker($remote = 0)
    {
        //连接本地docker
        if ($remote == 0) {
            $docker = Docker::create();
        } else {
            //连接远程节点
            $client = DockerClientFactory::create([
                'remote_socket' => 'tcp://' . $remote,
            ]);
            $docker = Docker::create($client);
        }
        $this->docker = $docker;
        return $docker;
    }

    /**
     * 获取容器列表
     * @param Docker\Docker
     * @return
     * @throws \Http\Client\Socket\Exception\ConnectionException
     */
    public function getContainerList()
    {
        if ($this->docker == null) 
            return ['status'=>false ,'info'=>'无法获取容器列表，未连接docker'];
        
        try{
            $containers = $this->docker->containerList(['all' => true]);
        }catch( \RuntimeException $e ){
            $msg = $e->getMessage();
            return ['status'=>false,'info'=>$msg];
        }
        
        return ['status'=>true ,'data'=>$containers];
    }

    /**
     * 创建容器
     * @param \lrh\docker\model\ContainerConfig $config
     */
    public function createContainer( ContainerConfig $config )
    {
        try{
            $containerConfig = new ContainersCreatePostBody();

            //设置镜像
            $containerConfig->setImage($config->getImage());
            //交互设置
            $containerConfig->setAttachStdin(true);
            $containerConfig->setAttachStdout(true);
            $containerConfig->setAttachStderr(true);
            $containerConfig->setTty(true);
            $containerConfig->setOpenStdin(true);
            //重启策略
            $restart_policy = new RestartPolicy;
            $restart_policy->steName('unless-stopped');
            $containerConfig->setRestartPolicy($restart_policy);

            //绑定端口
            if( !empty($config->getPorts()) )
                $containerConfig = $this->bindPort($containerConfig, $config->getPorts() );
            //挂载目录
            if( !empty($config->getVolumes()) )
                $containerConfig = $this->bindVolumes($containerConfig, $config->getVolumes() );
            //内存限制
            if( !empty($config->getMemory()) )
                $containerConfig = $this->limitMemory($containerConfig, $config->getMemory() );
            
            //创建容器
            if( empty($config->getName()) )
                $containerCreateResult = $this->docker->containerCreate($containerConfig); 
            else    
                $containerCreateResult = $this->docker->containerCreate($containerConfig, ['name' => $config->getName()]);
                  
            //启动容器
            $container_id = $containerCreateResult->getId();
            return [ 'status'=>true, 'container_id'=>$container_id ];
        
        }catch( \RuntimeException $e ){
            $msg = $e->getMessage();
            return ['status'=>false,'info'=>$msg];
        }
        
    }

    /**
     * 绑定端口
     * @param  ContainersCreatePostBody $containerConfig
     * @param array $ports 容器端口 [ container_port => host_port ]
     * @return ContainersCreatePostBody $containerConfig
     */
    protected function bindPort( ContainersCreatePostBody $containerConfig, array $ports )
    {
        $exposedPorts = new \ArrayObject();
        $portMap = new \ArrayObject();
        if ($containerConfig->getHostConfig() == NULL)
            $hostConfig = new HostConfig();
        else
            $hostConfig = $containerConfig->getHostConfig();

        foreach( $ports as $cp=>$hp ){
            $exposePort = $cp . '/tcp';
            $exposedPorts[$exposePort] = new \stdClass;

            $portBinding = new PortBinding();
            if (isset($hp)) {
                $portBinding->setHostPort($hp);
            }
            $portBinding->setHostIp('127.0.0.1');   //防止外部访问该端口

            $portMap[$exposePort] = [$portBinding];
        }

        $containerConfig->setExposedPorts($exposedPorts);

        $hostConfig->setPortBindings($portMap);
        $containerConfig->setHostConfig($hostConfig);
        
        return $containerConfig;
    }

    /**
     * 目录映射、挂载
     * @param ContainersCreatePostBody $containerConfig
     * @param array $volumes
     * @return ContainersCreatePostBody $containerConfig
     */
    protected function bindVolumes( ContainersCreatePostBody $containerConfig, array $volumes )
    {
        if ($containerConfig->getHostConfig() == NULL)
            $hostConfig = new HostConfig();
        else
            $hostConfig = $containerConfig->getHostConfig();

        $hostConfig->setBinds($volumes);
        $containerConfig->setHostConfig($hostConfig);

        return $containerConfig;
    }

    /**
     * 内存限制
     * @param int $memorySetting bytes
     * @return ContainersCreatePostBody $containerConfig
     */
    protected function limitMemory( ContainersCreatePostBody $containerConfig, $memory )
    {
        if ($containerConfig->getHostConfig() == NULL)
            $hostConfig = new HostConfig();
        else
            $hostConfig = $containerConfig->getHostConfig();

        $hostConfig->setMemory($memory);
        $hostConfig->setMemorySwap(-1); //不限制swap使用

        return $containerConfig;
    }

    /**
     * 根据id暂停容器
     * @param string $id    容器id
     */
    public function stopContainer( string $id )
    {
        try{
            $this->docker->containerStop($id);
            return ['status'=>true];
        }catch( \RuntimeException $e ){
            $msg = $e->getMessage();
            return ['status'=>false,'info'=>$msg];
        }
        
    }

    /**
     * 根据id开启容器
     * @param string $id    容器id
     */
    public function startContainer( string $id )
    {
        try{
            $this->docker->containerStart($id);
            return ['status'=>true];
        }catch( \RuntimeException $e ){
            $msg = $e->getMessage();
            return ['status'=>false,'info'=>$msg];
        }
        
    }
}