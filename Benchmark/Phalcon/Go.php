<?php

namespace Benchmark\Phalcon;

class Go implements \Benchmark\TestInterface{

    public function setup($dbInfos){
        $di = new \Phalcon\DI();

        $di->set('db', new \Phalcon\Db\Adapter\Pdo\Mysql(array(
            "host" => $dbInfos["host"],
            "username" => $dbInfos["username"],
            "password" => $dbInfos["password"],
            "dbname" => $dbInfos["db-name"]
        )));
        $di->set('modelsManager', new \Phalcon\Mvc\Model\Manager());
        $di->set('modelsMetadata', new \Phalcon\Mvc\Model\Metadata\Memory());

        return $di;

    }
    
    public function launchSimple($dbInfos,&$memoryUsage, &$time) {

        $timeBu = microtime(true);
        $memoryBu = memory_get_usage();
        
        $this->setup($dbInfos);
       
        $trees = \Benchmark\Phalcon\Models\Tree::find();

        foreach($trees as $t){
            $t->id;
        }
        
        $memoryUsage = memory_get_usage() - $memoryBu;
        $time        = microtime(true) - $timeBu;


    }


    public function launchOneJoin($dbInfos, &$memoryUsage, &$time) {
        $timeBu = microtime(true);
        $memoryBu = memory_get_usage();

        $di=$this->setup($dbInfos);
        $manager=$di->get('modelsManager');

        $phql  = "SELECT * FROM Benchmark\Phalcon\Models\Tree LEFT JOIN Benchmark\Phalcon\Models\Lemon ON Benchmark\Phalcon\Models\Tree.id = Benchmark\Phalcon\Models\Lemon.tree_id";
        $trees = $manager->executeQuery($phql);

        foreach($trees as $t){
            foreach ($t->lemons as $lemon) {
                echo $lemon->id . " ";
            }

        }

        $memoryUsage = memory_get_usage() - $memoryBu;
        $time        = microtime(true) - $timeBu;


    }

    public function launchTwoJoin($dbInfos, &$memoryUsage, &$time) {
        $timeBu = microtime(true);
        $memoryBu = memory_get_usage();

        $di=$this->setup($dbInfos);
        $manager=$di->get('modelsManager');

        $phql  = "SELECT * FROM Benchmark\Phalcon\Models\Tree
            LEFT JOIN Benchmark\Phalcon\Models\Lemon ON Benchmark\Phalcon\Models\Tree.id = Benchmark\Phalcon\Models\Lemon.tree_id
            LEFT JOIN Benchmark\Phalcon\Models\Seed ON Benchmark\Phalcon\Models\Lemon.id = Benchmark\Phalcon\Models\Seed.lemon_id";
        $rows = $manager->executeQuery($phql);



        $memoryUsage = memory_get_usage() - $memoryBu;
        $time        = microtime(true) - $timeBu;
    }


    
    
}
