<?php

namespace Benchmark\PDO;

use Benchmark\PDO\Models\Lemon;
use Benchmark\PDO\Models\Seed;
use Benchmark\PDO\Models\Tree;

class Go implements \Benchmark\TestInterface{

    /**
     * @var \PDO
     */
    protected $pdo;

    public function getName(){
        return "PDO";
    }

    public function __construct($dbInfos){
        $pdo=new \PDO(
            "mysql:dbname=".$dbInfos["db-name"].";host=".$dbInfos["host"],
            $dbInfos["username"],
            $dbInfos["password"]
        );
        $this->pdo = $pdo;
    }
    
    public function launchSimple() {
        $pdo = $this->pdo;
        $q = $pdo->prepare("SELECT * FROM tree");
        $q->execute();

        $trees=array();

        while($r=$q->fetch()){
            $t=new Tree();
            $t->setAge($r["age"]);
            $t->setId($r["id"]);
            $trees[]=$t;
        }
    }

    public function launchOneJoin() {
        $pdo = $this->pdo;
        $q=$pdo->prepare(" SELECT tree.*,lemon.id as lemon_id, lemon.mature FROM tree LEFT JOIN lemon ON lemon.tree_id=tree.id");
        $q->execute();

        $trees=array();

        while($r=$q->fetch()){
            if(!isset( $trees[$r["id"]] )){
                $t = new Tree();
                $t->setAge($r["age"]);
                $t->setId($r["id"]);
                $trees[$r["id"]]=$t;
            }
            $l = new Lemon();
            $l->setId($r["lemon_id"]);
            $l->setMature($r["mature"]);
            $l->setTree($trees[$r["id"]]);
            $t->addLemon($l);
        }
    }

    public function launchTwoJoin() {
        $pdo = $this->pdo;
        $q=$pdo->prepare(" SELECT tree.*,lemon.id as lemon_id, lemon.mature, seed.id as seed_id,seed.fertil FROM tree LEFT JOIN lemon ON lemon.tree_id=tree.id JOIN seed on seed.lemon_id=lemon.id");
        $q->execute();


        $trees=array();
        $lemons=array();

        while($r=$q->fetch()){

            if(!isset( $trees[$r["id"]] )){
                $t = new Tree();
                $t->setAge($r["age"]);
                $t->setId($r["id"]);
                $trees[$r["id"]]=$t;
            }

            if(!isset( $lemons[$r["lemon_id"]] )){
                $l = new Lemon();
                $l->setId($r["lemon_id"]);
                $l->setMature($r["mature"]);
                $l->setTree($trees[$r["id"]]);
                $t->addLemon($l);
                $lemons[$r["lemon_id"]]=$l;
            }

            $s=new Seed();
            $s->setId($r["seed_id"]);
            $s->setFertil($r["fertil"]);
            $s->setLemon($lemons[$r["lemon_id"]]);

            $lemons[$r["lemon_id"]]->addSeed($s);
        }
    }
}
