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

### 16/10/2026 – IT 1 : Environnements de Dev et de Prod

- **Mis en prod :** installation de WordPress, WooCommerce 11.2.1, Storefront 4.6.2, Secure Custom Fields 6.9.6, Classic Editor 1.7.0 et du thème enfant. Import de la base de dev.
- **Remarques :** envoi des plugins trop lent (Bureau synchronisé avec iCloud) : je les ai téléchargés directement sur le serveur, aux mêmes versions.

### 17/10/2026 – IT 2 : Catalogue et produits simples

- **Mis en prod :** apparence (en-tête, pied de page, polices), 3 catégories et 9 produits, carte sur l'accueil et la boutique, fiche produit.
- **Remarques :** commit f3bcaca. Thème : git pull. Base : import + search-replace (45 remplacements). Images : envoi de uploads. Traductions installées en prod. Problèmes : fichiers bloqués par iCloud (git add), scp coupé une fois (relancé).

### 19/10/2026 – IT 3 : Produits variables

- **Mis en prod :** burgers en produits variables (sauce, taille, ingrédients à retirer), 3 niveaux de stock, sélecteur d'options comme la maquette.
- **Remarques :** commit b50108b. Serveur mmi.unilim.fr inaccessible le 19/10 au matin (connexion SSH refusée pour tous les étudiants) : déployée avec l'IT 4 une fois le serveur revenu. Thème : git pull. Base : sauvegarde, import + search-replace (45 remplacements). Images : envoi de uploads.

### 19/10/2026 – IT 4 : Enrichissement du contenu

- **Mis en prod :** champs personnalisés SCF (Composition, Allergènes, Valeurs nutritionnelles) remplis sur les 3 burgers, affichés avant le prix en menus dépliants.
- **Remarques :** commit c33e0e6. Déployée en même temps que l'IT 3 (même procédure). Vérifié en prod en fenêtre privée : menus dépliants, prix selon la taille, 3 niveaux de stock.

### 20/10/2026 – IT 5 : Panier et expédition

- **Mis en prod :** paiements "Virement" et "Paiement au retrait", zone d'expédition en France avec "Retrait en boutique" gratuit et "Livraison à domicile" 6,90 €, page Panier comme la maquette avec tableau, quantité − 1 +, bloc Réception, bloc Total + bouton panier sticky sur la page d'accueil et la page produit.
- **Remarques :** commit b93aa96. Pages Panier et Commande passées en shortcodes classiques ([woocommerce_cart], [woocommerce_checkout]) pour pouvoir les personnaliser. Template woocommerce/cart/cart-totals.php surchargé. Extension Packlink PRO désactivée.
