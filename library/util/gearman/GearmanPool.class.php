<?php

class GearmanPool {

    /**
     * 实现单例模式
     *
     * @param   array   $config
     * @return  GearmanWorker
     */
    public static function getWorker($config) {

        $client = new GearmanWorker();
        foreach ($config as $serverInfo) {
            $client->addServer($serverInfo['host'], $serverInfo['port']);
        }
        return $client;
    }

    /**
     * 实现单例模式
     *
     * @param   array   $config
     * @return  GearmanClient
     */
    public static function getClient($config) {

        $worker = new GearmanClient();
        foreach ($config as $serverInfo) {
            $worker->addServer($serverInfo['host'], $serverInfo['port']);
        }
        return $worker;
    }

}