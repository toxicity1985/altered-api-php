#!/bin/bash

cd community_database || exit

git checkout main && git pull

cd .. || exit

# 1. Exécuter le script
php bin/application app:market-place:json
php bin/application app:unique:json BISE
php bin/application app:unique:json ALIZE
php bin/application app:unique:json CORE
php bin/application app:unique:json COREKS

# 2. Vérifier que le script s'est bien terminé
if [ $? -eq 0 ]; then
    echo "Le script s'est terminé avec succès."

    # 3. Format de la date
    BRANCH_NAME=$(date +"%d-%m-%Y")

    # 4. Aller dans le dossier altered_marketplace
    cd community_database || exit

    # 5. Git operations
    git checkout -b "update-$BRANCH_NAME"
    git add .
    git commit -m "Mise à jour du $(date +"%d/%m/%Y")"
    git push -u origin "update-$BRANCH_NAME"

    # 6. Créer la pull request
     PR_OUTPUT=$(gh pr create \
            --title "Mise à jour du $(date +"%d/%m/%Y")" \
            --body "Pull request générée automatiquement après exécution du script." \
            --base main)

     PR_URL=$(echo "$PR_OUTPUT" | grep -Eo 'https://github\.com/[^ ]+')

    gh pr merge --admin --rebase "$PR_URL"



else
    echo "Le script a échoué. Aucune opération Git n'a été effectuée."
fi