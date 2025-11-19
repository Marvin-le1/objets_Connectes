#!/bin/bash

# Met à jour le repo
git pull origin main    # ou master, ou la branche que tu utilises

# Lance/maj les containers
docker compose up -d --build
