<?php
namespace lrh\docker\model;

/**
 * Created by Vscode.
 * User: lurunhao
 * Date: 2019/7/10
 * Time: 14:05
 * 
 * 容器数据类
 * 
 */
class ContainerConfig {

    /**
     * 容器名称
     * @var string
     */
    protected $name;

    /**
     * 容器镜像名（必填）
     * @var string
     */
    protected $image;

    /**
     * 容器端口设置，格式 [ container_port => host_port ]
     * @var array   
     */
    protected $ports;

    /**
     * 容器挂载设置
     * 格式 [ 宿主机文件夹绝对路径(bind)：容器内部文件夹绝对路径 , "/home/www/Containers/redis_data:/usr/local/redis/data" ]
     * 格式 [ volume名(volume)：容器内部文件夹绝对路径 , "volume_name:/usr/local/redis/data" ]
     * @var array   [ container_port => host_port ]
     */
    protected $volumes;

    /**
     * 容器内存限制 bytes   1G = 1073741824 = 1*1024*1024*1024 
     * @var int 
     */
    protected $memory;

    /**
     * @return string
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return array
     */
    public function getPorts(): ?array
    {
        return $this->ports;
    }

    /**
     * @param array $ports 
     * @return self
     */
    public function setPorts(?array $ports): self
    {
        $this->ports = $ports;
        return $this;
    }

    /**
     * @return array
     */
    public function getVolumes(): ?array
    {
        return $this->volumes;
    }

    /**
     * @param array $ports 
     * @return self
     */
    public function setVolumes(?array $volumes): self
    {
        $this->volumes = $volumes;
        return $this;
    }

    /**
     * @return int
     */
    public function getMemory(): ?int
    {
        return $this->memory;
    }

    /**
     * @param int $memory   
     * @return self
     */
    public function setMemory(?int $memory): self
    {
        $this->memory = $memory;
        return $this;
    }
}
