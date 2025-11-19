#!/bin/bash

# Met à jour le repo
git pull origin main    # ou master, ou la branche que tu utilises

# Lance/maj les containers
docker compose up -d --build

# Lance le script de lecture des badges
python3 Script-Python/Entree_Sortie.py &