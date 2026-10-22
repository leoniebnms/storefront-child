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

### 20/10/2026 – IT 6 : Checkout

- **Mis en prod :** bloc « Instructions pour le retrait » sur la page de commande (texte modifiable dans WooCommerce -> Réglages -> Général -> Click & Collect). Pages Commande et Confirmation faites comme les maquettes : formulaire dans l'ordre de la maquette, paiement dans le cadre de gauche, récapitulatif « Votre panier », bouton "Payer XX €", page Confirmation avec récap, numéro de commande, instructions et compte à rebours.
- **Remarques :** commits 1020829 et 6c7539d. Nouveau template woocommerce/checkout/thankyou.php. Le fichier avait été coupé par iCloud au premier commit (erreur critique en prod sur la page de confirmation, trouvée en activant WP_DEBUG), corrigé par un second commit.

### 20/10/2026 – IT 7 : Compte client

- **Mis en prod :** création de compte et connexion configurées (WooCommerce -> Comptes et confidentialité : création sur la page Mon compte et mot de passe choisi par le client). Pages Me connecter / M'inscrire, Récupérer mon mot de passe et Mon compte refaites comme les maquettes. Mon compte : informations personnelles (civilité, nom, prénom, téléphone, adresse, enregistrées comme adresse de facturation pour pré-remplir la commande), changement d'identifiant et de mot de passe, déconnexion. Icône Mon compte dans l'en-tête et lien dans le menu burger.
- **Remarques :** commit 2133ef1. Nouveaux templates woocommerce/myaccount/form-login.php, form-lost-password.php et dashboard.php. Fichiers vérifiés avant la mise en prod (git show) pour éviter une coupure par iCloud.

### 21/10/2026 – IT 8 : E-mails transactionnels

- **Mis en prod :** e-mails configurés dans WooCommerce -> Réglages -> E-mails (objets, titres, expéditeur "Watson's Pub", logo, couleurs, pied de page). Templates des e-mails refaits comme les maquettes : en-tête avec logo et titre, numéro de commande, récapitulatif du panier, pied de page rouille. Mails "En attente de paiement" (coordonnées bancaires et adresse de livraison), "Merci pour votre commande" et "Votre commande est prête !" (mail "Commande terminée" réutilisé). Instructions de retrait ajoutées dans les mails des commandes en retrait en boutique, avec le montant à régler au comptoir si le client paie au retrait.
- **Remarques :** commit b245402. Nouveaux templates woocommerce/emails/email-header.php, email-footer.php, customer-on-hold-order.php, customer-processing-order.php et customer-completed-order.php. Tests en local dans Mailpit (livraison + virement, retrait + paiement au retrait, passage en Terminée). Mail test reçu en prod sur Outlook (logo bloqué tant que l'expéditeur n'est pas approuvé). Correctif : caractères NUL ajoutés par iCloud dans woocommerce/archive-product.php, nettoyés et remis en prod (commit 69557de).

### 21/10/2026 – IT 9 : Commandes

- **Mis en prod :** gestion du stock vérifiée (baisse à la commande, remise en stock à l'annulation). Nouveau statut de commande "Prête" pour le retrait en boutique, avec le mail "Votre commande est prête !". Mail "Merci de votre visite !" quand la commande retirée et réglée au comptoir passe en "Terminée". Tableau "Mes commandes" dans Mon compte, page détail d'une commande, annulation par le client avec une fenêtre de confirmation. Mails "Paiement reçu !", "Commande annulée" (avec le remboursement si la commande était payée) et "Remboursement effectué".
- **Remarques :** commits c0831ef, f69ebf8 et 2e12f8c. Nouveaux fichiers inc/class-watsons-email-commande-prete.php et inc/class-watsons-email-commande-annulee.php, templates woocommerce/myaccount/view-order.php, woocommerce/emails/customer-ready-order.php, customer-commande-annulee.php et customer-refunded-order.php. Le style des mails est passé de functions.php à la fin de style.css. Thème en version 1.7.0 : sans changement de version, le navigateur gardait l'ancien style.css en cache et la prod s'affichait sans le CSS de l'IT 9.

### 21/10/2026 – IT 10 : Promotions

- **Mis en prod :** codes promo activés et créés dans Marketing -> Codes promo : PUB10 (−10 % sur 2 burgers), WATSON2 (−2 € sur 2 desserts) et ETE2026 (expiré, pour les tests). Bloc "Code promo" dans le panier comme sur la maquette : champ + bouton "Appliquer", code appliqué dans un cadre vert avec un lien "Retirer", messages d'erreur sous le champ (code erroné, expiré, ne s'appliquant à aucun produit du panier). Sous-total et réduction affichés au-dessus du total. Ligne du code promo ajoutée dans la confirmation de commande, les e-mails et le détail de commande de Mon compte.
- **Remarques :** commit 61598f6 + version 1.8.0 du thème. Template woocommerce/cart/cart-totals.php modifié, thankyou.php et view-order.php mis à jour. Les produits des récapitulatifs sont maintenant affichés avant la réduction, pour que le calcul soit lisible (produits − code + réception = total). Base importée en prod pour les codes promo.

### 22/10/2026 – IT 11 : Équipe

- **Mis en prod :** type de contenu "Équipe" (menu Équipe dans l'admin) avec un groupe de champs SCF "Profil du membre" (poste, arrivée, plat préféré, spécialité). 3 membres créés : Thomas Watson (gérant), Camille Durand (cheffe de cuisine), Hugo Martin (barman), avec photo, présentation et ordre d'affichage. Page "L'équipe" qui liste les membres et page de profil de chaque membre, avec "Le reste de l'équipe" pour passer d'un profil à l'autre. Lien "L'équipe" ajouté au menu.
- **Remarques :** commit 8922afe, thème en version 1.9.0. Nouveaux templates archive-membre.php et single-membre.php. Le lien du menu doit être une adresse complète (https://sae301.ddev.site/?post_type=membre) pour que le search-replace la convertisse en prod : avec "/?post_type=membre", le lien renvoyait à la racine de mmi.unilim.fr. Base renvoyée une 2e fois, car les photos avaient été ajoutées après le premier export. Photos envoyées avec scp (uploads/2026/10). functions.php vidé par iCloud sur le Mac, restauré avec git restore avant le commit.
