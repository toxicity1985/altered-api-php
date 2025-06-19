#!/bin/bash

# 1. Exécuter le script
php bin/application app:market-place:json BISE

# 2. Vérifier que le script s'est bien terminé
if [ $? -eq 0 ]; then
    echo "Le script s'est terminé avec succès."

    # 3. Format de la date
    BRANCH_NAME=$(date +"%d-%m-%Y")

    # 4. Aller dans le dossier altered_marketplace
    cd altered_marketplace || exit

    # 5. Git operations
    git checkout -b "update-$BRANCH_NAME"
    git add .
    git commit -m "Mise à jour du $(date +"%d/%m/%Y")"
    git push -u origin "update-$BRANCH_NAME"

    # 6. Créer la pull request
    gh pr create --title "Mise à jour du $BRANCH_NAME" --body "Pull request générée automatiquement après exécution du script." --base main

else
    echo "Le script a échoué. Aucune opération Git n'a été effectuée."
fi