🏢 Projet de pointage RFID — INFDIET4 Objets connectés

🎯 Objectif

Ce projet a pour but de créer un système de pointage pour les employés d’une entreprise, permettant d’enregistrer automatiquement leurs entrées et sorties à l’aide d’un badge RFID.
Le service RH peut ensuite extraire un rapport de présence pour calculer les temps de travail.

⸻

⚙️ Fonctionnalités principales
• Lecture et écriture de badges RFID (module GT138 — 13,56 MHz).
• Détection automatique du type de badgeage (entrée ou sortie).
• Gestion des oublis de badge (ajout manuel d’un pointage avec type et heure).
• Calcul du temps de travail journalier et sur une plage de dates donnée.
• Prise en compte des règles internes :
• Pause déjeuner minimale de 1 heure.
• Durée journalière de 7 heures.
• Gestion des dépassements horaires.

⸻

🧰 Matériel utilisé
• Carte Arduino Uno® compatible
• Module RFID TAG GT138 (RC522)
• Badges RFID Mifare 13,56 MHz
• Câbles de connexion M/F
• Alimentation 3,3V

⸻

💻 Technologies
• Arduino IDE
• Bibliothèque MFRC522 (communication SPI)
• Langage : C++ (Arduino)
• Base de données (modèle conceptuel MCD défini pour la gestion des temps)

⸻

📈 Livrables et étapes 1. Mise en place du module RFID et lecture des badges 2. Système de badgeage manuel 3. Intégration du badgeage RFID 4. Calcul et affichage des rapports de présence 5. Présentation et démonstration finale du projet
