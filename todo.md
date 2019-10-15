# TODO list of MasterMind game

- [DONE] Classe `Player` pour stocker un joueur;
- [DONE] (Rq le doublon ecrasera la premiere sauvgarde) Gerer le fait qu'un même joueur peut avoir plusieur jeu sauvgardé;
- [DONE] Classe `Players` pour stocker tout les joueurs;
- [DONE] Ajout de la possibilité de supprimer une sauvegarde;
- implementer puis tester la sauvgarde multiple;
  - Supprimer les ancien cookie stocké;
  - les remplacé par un seul cookie qui va contenir une instance de la class `Players`;
  - Gérer tout les joueurs avec ce cookie là;
- Classe `Scores` pour stocker les meilleurs scores;
- implementer `Scores` puis tester;
- Supprimer l'affichage du code secret lors de la première utilisation (Utile pour le débogage);
- Refonte graphique;
  