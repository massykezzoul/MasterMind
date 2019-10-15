<?php
    class MasterMind {
        private $_secret = "";          // String
        private $_taille = 0;           // int, nombre de chiffre dans secret
        private $_essais = array();     // instances de la classe Essais
        private $_nbEssai;         // nombre d'essais

        public function __construct($taille) {
            $this->_taille = $taille;
            $this->_nbEssai = 0;
            for ($i=0; $i < $this->_taille; $i++) { 
                $this->_secret .= rand(0,9);
            }
            echo 'secret : '. $this->_secret . '<br>'; // for debug
        }

        private function essaiCorrecte($essai) {
            if (is_string($essai) && strlen($essai) == $this->_taille) { // correct en taille
                for ($i = 0; $i < $this->_taille ; $i++)
                    if ($essai[$i] > '9' || $essai[$i] < '0')
                        return false;
                return true;
            } else 
                return false;
        }
        // @var $essai : Une chaine de caractère de taille = $_taille;
        // @return $couple : un Couple tq couple->x est le nombre de bien placé et couple->y le nombre de mal placé ; (-1,-1) si erreur
        public function test($essai) {
            if (!$this->essaiCorrecte($essai))
                return new Couple(-1,-1);
            $this->_nbEssai++;
            $e = new Essais($essai);
            array_push($this->_essais,$e);

            return $e->test($this->_secret);
            
        }
        // @return vrai si gagner (dernier essai gagner)
        public function isGagner() {
            return ($this->_nbEssai > 0 && $this->_essais[$this->_nbEssai - 1]->getBp() === $this->_taille);
        }
        // @return $_taille
        public function getTaille() {return $this->_taille;}
        // @return $_essais
        public function getEssais() {return $this->_essais;}
        // @return $_nbEssais
        public function getNbEssai() {return $this->_nbEssai;}
    }

    class Essais {
        private $_essai = "";
        private $_bp = 0; // nombre de bien placé
        private $_mp = 0; // nombre de mal placé

        public function __construct($essai) {
            $this->_essai = $essai;
            //$this->_bp = $bp;
            //$this->_mp = $mp;
        }

        // @return instanceof Couple
        public function test($secret) {
            $tmp = $secret;
            $tmpEssai = $this->_essai;
            for ($i=0; $i < strlen($secret); $i++) { 
                if ($tmpEssai[$i] == $tmp[$i]) {
                    $tmpEssai[$i] = '*';
                    $tmp[$i] = '*';
                    $this->_bp++;
                }
            }

            for ($i=0; $i < strlen($secret); $i++) { 
                for ($j=0; $j < strlen($secret); $j++) { 
                    if ($tmpEssai[$i] != '*' && $tmp[$j] != '*' && $tmpEssai[$i] == $tmp[$j]) {
                        $tmpEssai[$i] = '*';
                        $tmp[$j] = '*';
                        $this->_mp++;
                    }
                }
            }
            return new Couple($this->_bp,$this->_mp);
        }
        public function getEssai() {return $this->_essai;}
        public function getBp() {return $this->_bp;}
        public function getMp() {return $this->_mp;}

    }

    class Couple {
        private $_x;
        private $_y;

        public function __construct($_x,$_y) {
            $this->_x = $_x;
            $this->_y = $_y;
        }

        public function getX() {
            return $this->_x;
        }

        public function getY() {
            return $this->_y;
        }
    }
?>