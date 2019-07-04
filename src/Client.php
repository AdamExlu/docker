<?php
namespace lrh\docker;
// use Docker\Docker;
// use Docker\DockerClientFactory;

class Client 
{
    public $docker;

    static public function test()
    {
        echo 1111;
    }

    // /**
    //  * 连接docker
    //  * @param string $remote 远程 IP:端口
    //  * @return Docker\Docker
    //  */
    // public function connectDocker($remote = 0)
    // {
    //     //连接本地docker
    //     if ($remote == 0) {
    //         $docker = Docker::create();
    //     } else {
    //         //连接远程节点
    //         $client = DockerClientFactory::create([
    //             'remote_socket' => 'tcp://' . $remote,
    //         ]);
    //         $docker = Docker::create($client);
    //     }
    //     $this->docker = $docker;
    //     return $docker;
    // }
}


