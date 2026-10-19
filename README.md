# SAE301 – US-1.5 : Prod, mises à jour et journal

Léonie Beau-Nimis – BUT MMI, IUT du Limousin

ITERATION 1 - Environnements de Dev et de Prod

## 1. Accéder à la prod

|             | Adresse                                           |
| ----------- | ------------------------------------------------- |
| Front       | https://mmi.unilim.fr/~beaunimis1/sae301          |
| Back office | https://mmi.unilim.fr/~beaunimis1/sae301/wp-admin |

On se connecte au back office avec les mêmes identifiants qu'en dev.

Pour comparer, le dev est ici :

|             | Adresse                           |
| ----------- | --------------------------------- |
| Front       | https://sae301.ddev.site          |
| Back office | https://sae301.ddev.site/wp-admin |

## 2. Mettre à jour la prod

Règle : je modifie et je teste toujours en dev d'abord. Ensuite, je reporte en prod.

Pour se connecter au serveur :

```bash
ssh beaunimis1@mmi.unilim.fr
```

### J'ai modifié le thème enfant

Sur le Mac :

```bash
cd ~/Desktop/sae301/wp-content/themes/storefront-child
git add .
git commit -m "Ce que j'ai changé"
git push
```

Sur le serveur :

```bash
cd ~/public_html/sae301/wp-content/themes/storefront-child
git pull
```

### J'ai ajouté ou mis à jour un plugin

Je regarde la version en dev :

```bash
ddev wp plugin list
```

J'installe la même version sur le serveur :

```bash
cd ~/public_html/sae301
wp plugin install nom-du-plugin --version=X.X.X --force
wp plugin activate nom-du-plugin
```

### J'ai modifié le contenu ou les réglages (base de données)

Attention : l'import remplace toute la base de prod. Je fais une sauvegarde avant.

Sur le Mac, j'exporte la base et je l'envoie :

```bash
cd ~/Desktop/sae301
ddev wp db export sae301-dev.sql --add-drop-table
scp sae301-dev.sql beaunimis1@mmi.unilim.fr:~/
```

Sur le serveur, je sauvegarde, j'importe et je change les adresses :

```bash
cd ~/public_html/sae301
wp db export ~/sauvegarde-prod.sql
wp db import ~/sae301-dev.sql
wp search-replace 'https://sae301.ddev.site' 'https://mmi.unilim.fr/~beaunimis1/sae301' --all-tables-with-prefix --skip-columns=guid
wp cache flush
rm ~/sae301-dev.sql
```

### Après chaque mise à jour

- [ ] Le front de prod s'affiche
- [ ] Je peux me connecter au back office de prod
- [ ] J'ajoute une ligne au journal

## 3. Journal des mises en production

| Date       | Itération | Ce qui a été mis en prod                                                                                                                                        | Remarques                                                                                                                            |
| ---------- | --------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------ |
| 16/10/2026 | IT 1      | Installation de WordPress, WooCommerce 11.2.1, Storefront 4.6.2, Secure Custom Fields 6.9.6, Classic Editor 1.7.0 et du thème enfant. Import de la base de dev. | Envoi des plugins trop lent (Bureau synchronisé avec iCloud) : je les ai téléchargés directement sur le serveur, aux mêmes versions. |

ITERATION 2 :Catalogue et produits simples

| 17/10/2026 | IT 2 | Apparence (en-tête, pied de page, polices), 3 catégories et 9 produits, carte sur l'accueil et la boutique, fiche produit | Commit f3bcaca. Thème : git pull. Base : import + search-replace (45 remplacements). Images : envoi de uploads. Traductions installées en prod. Problèmes : fichiers bloqués par iCloud (git add), scp coupé une fois (relancé). |

| 19/10/2026 | IT 3 | Burgers en produits variables (sauce, taille, ingrédients à retirer), 3 niveaux de stock, sélecteur d'options comme la maquette | Commit b50108b. Mise en prod reportée : serveur mmi.unilim.fr inaccessible (connexion SSH refusée pour tous les étudiants le 19/10 au matin). |
