<?php
    //require 'MasterMind.php';
    //require 'Player.php';
    //include("MasterMind.php");
    spl_autoload_register(function ($class_name) {
        include $class_name . '.php';
    });

    class Players {
        private $players; // Tableau contenant tout les joueurs sauvgardé

        public function __construct() {
            settype($this->players,"array"); // typage de la variable $players
        }

        /* add an element to $players */
        public function addPlayer($player) {
            if ($player instanceof Player) {
                $bool = false;
                for ($i = 0 ; $i < $this->getSize(); $i++) {
                    if ($this->getPlayer($i)->getName() === $player->getName()) {
                        $bool = true;
                        $this->setPlayer($i,$player);
                        break;
                    }
                }
                if (!$bool) {
                    $this->players[] = $player;
                }

            } else {
                echo "Excpected type Player, get '". gettype($player)."' on Players::addPlayer(...)<br>";
            }
        }

        /* remove an element from $players */
        public function deletePlayer($i) {
            if ($i < $this->getSize() && $i >= 0) {
                array_splice($this->players,$i,1); // supprime et arrange les indices
            }
        }

        /* return the size of the array $players */
        public function getSize() {
            return count($this->players);
        }

        /* get an $player */
        public function getPlayer($i) {
            if ($i < $this->getSize() && $i >= 0) {
                return $this->players[$i];
            } else {
                echo "Segmentation fault on Players::getPlayer($i)<br>";
                return null;
            }
        }

        public function setPlayer($i,$player) {
            if ($i < $this->getSize() && $i >= 0 && $player instanceof Player) {
                $this->players[$i] = $player;
            } else {
                echo "Segmentation fault on Players::setPlayer($i,...)<br>";
            }
        }
    }
?>