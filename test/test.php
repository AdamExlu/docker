<?php
require('../vendor/autoload.php');

$temp = new \lrh\docker\Client;
$temp->connectDocker();
$list = $temp->getContainerList();
var_dump($list);die;