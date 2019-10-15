<?php
    class Player {
        private $name; // Nom du joueur
        private $jeu;  // Partie de masterMind sauvgardé
        private $date_sauvgarde; // date de sauvgarde

        public function __construct($name,$jeu) {
            if (is_string($name) && $jeu instanceof MasterMind) { // bon type d'argument
                $this->name = $name;
                $this->jeu = $jeu;
                $this->date_sauvgarde = getdate();
            }
        }

        public function getName() {
            return $this->name;
        }

        public function getJeu() {
            return $this->jeu;
        }

        public function getDate() {
            return $this->date_sauvgarde;
        }
    }
?>