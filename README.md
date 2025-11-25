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
• Raspberry Pi
• Module RFID TAG GT138 (RC522)
• Badges RFID Mifare 13,56 MHz
• Câbles de connexion M/F
• Alimentation 3,3V

⸻

🔌 Branchement du module RFID (RC522)

Correspondance des pins :

• Pin 1  → VCC  
• Pin 6  → GND  
• Pin 19 → MOSI  
• Pin 21 → MISO  
• Pin 22 → RST  
• Pin 23 → SCK  
• Pin 24 → NOS  

Ces branchements permettent la communication SPI entre le module RFID et le Raspberry Pi.

💻 Technologies
• Environnement Raspberry Pi OS
• Bibliothèque MFRC522 (communication SPI)
• Langage : Python
• Base de données (modèle conceptuel MCD défini pour la gestion des temps)

⸻

📈 Livrables et étapes 1. Mise en place du module RFID et lecture des badges 2. Système de badgeage manuel 3. Intégration du badgeage RFID 4. Calcul et affichage des rapports de présence 5. Présentation et démonstration finale du projet

⸻

📦 Initialisation du projet

1. Clonez le projet depuis GitHub :
   ```bash
   git clone https://github.com/Marvin-le1/objets_Connectes.git
   ```
2. Accédez au dossier du projet :
   ```bash
   cd objets_Connectes
   ```
3. Assurez-vous que le script de démarrage possède les permissions nécessaires :
   ```bash
   chmod +x start.sh
   ```
4. Si vous utilisez le Raspberry Pi, installez les dépendances Python du lecteur RFID :
   ```bash
   pip3 install -r Script-Python/requirements.txt
   ```
4bis. Installez la librairie GPIO mise à jour (nécessaire sur Raspberry Pi) :
   ```bash
   sudo apt-get install -y python3-rpi-lgpio
   ```
5. Vérifiez que Docker et Docker Compose sont installés et fonctionnels :
   ```bash
   docker --version
   docker compose version
   ```

Une fois ces étapes terminées, vous pouvez lancer le projet en suivant la section ci‑dessous.

⸻

🚀 Lancement du projet

1. Assurez-vous d’être à la racine du projet (là où se trouve `docker-compose.yml`).
2. Si le script n’est pas exécutable, donnez-lui les permissions :
   ```bash
   chmod +x start.sh
   ```
3. Lancez ensuite le projet complet via Docker :
   ```bash
   ./start.sh
   ```
4. Une fois les services démarrés, accédez à l’interface React :
   http://localhost:3000
