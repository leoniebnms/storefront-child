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

### 22/10/2026 – IT 13 : Ventes croisées

- **Mis en prod :** produits complémentaires associés dans l'admin (Produit -> Produits liés -> Ventes croisées) : Sprite, Coca Cola et Crêpe pour les 3 burgers. Bloc "Ils se completent" comme sur la maquette : sous la fiche produit (les ventes croisées du produit) et sous le total du panier (les ventes croisées des produits du panier, sans ceux déjà ajoutés). 3 suggestions au maximum, avec photo, nom, prix et bouton "Ajouter" (ou "Choisir" pour un produit à options). Produits épuisés non proposés.
- **Remarques :** commit XXXXXXX, thème en version 1.10.0. Pas de nouveau template : le bloc est ajouté par des hooks WooCommerce (woocommerce_after_single_product et woocommerce_after_cart), et le bloc de ventes croisées par défaut du panier a été retiré. Base importée en prod pour les produits liés.

### 22/10/2026 – IT 12 : Taxonomies

- **Mis en prod :** nouvelle taxonomie "Moments" pour les produits, indépendante des catégories, gérée dans Produits -> Moments (case à cocher dans chaque produit, colonne dans la liste des produits). 3 termes avec description : Le midi, Le goûter, La soirée, avec au moins 2 produits chacun. Page dédiée pour chaque moment, comme la maquette : titre, phrase d'intro, boutons pour passer d'un moment à l'autre et grille des produits (catégorie, nom, prix, bouton "Choisir" ou "Ajouter"). Lien "Nos moments" ajouté au menu.
- **Remarques :** commit XXXXXXX, thème en version 1.11.0. Nouveau template taxonomy-moment.php. Les boutons sont dans l'ordre de la journée grâce aux slugs (le-midi, le-gouter, la-soiree). Lien du menu en adresse complète pour le search-replace. IT 12 faite après l'IT 13.

### 22/10/2026 – IT 14 : Avis client

- **Mis en prod :** avis activés dans WooCommerce (notes par étoiles obligatoires, libellé "acheteur certifié", avis réservés aux acheteurs certifiés) et validation manuelle des avis (Réglages -> Discussion). Section "Avis clients" sur la fiche produit comme la maquette : note moyenne, liste des avis (prénom, "Achat vérifié", date, étoiles, commentaire) et cadre "Donner mon avis" avec 3 états : formulaire pour le client qui a acheté le produit, message pour le visiteur ou le client qui ne l'a pas acheté, "Merci pour votre avis !" après l'envoi.
- **Remarques :** commit XXXXXXX, thème en version 1.12.0. Nouveau template woocommerce/single-product-reviews.php. Les onglets de la fiche produit ayant été retirés à l'IT 2, les avis sont ajoutés sous "Ils se completent". Étoiles violettes de WooCommerce remplacées par la couleur rouille, note sous le titre retirée.

### 22/10/2026 – IT 15 : Frais de préparation

- **Mis en prod :** frais de préparation de 1,50 € pour les commandes de moins de 15 €, montant et seuil réglables dans WooCommerce -> Réglages -> Général -> Frais de préparation. Message dans le panier comme sur la maquette : "Plus que X € pour ne pas payer les frais de préparation" avec une barre de progression, puis "Frais de préparation offerts !" en vert une fois le seuil atteint. Ligne "Frais de préparation" avec "Offerts dès 15,00 € d'achat" au-dessus du total. Les frais disparaissent ou reviennent en changeant les quantités, sans recharger la page. Ligne des frais ajoutée dans la confirmation de commande, les e-mails et le détail de commande de Mon compte.
- **Remarques :** commit edcd2d1, thème en version 1.13.0. Les frais sont calculés sur le montant des produits après le code promo, sans la livraison. Pas de nouveau template : cart-totals.php, thankyou.php et view-order.php mis à jour, et ligne des frais ajoutée au récapitulatif des mails dans functions.php. Testé en local (1 Coca avec frais, 5 Coca sans frais, mail dans Mailpit) puis en prod. Base importée en prod pour le montant et le seuil.

### 22/10/2026 – IT 16 : Landing page

- **Mis en prod :** section "Nouveautes" sur la page d'accueil, entre "Click and collect" et "La carte", comme sur la maquette : titre, phrase d'intro et les 3 derniers produits ajoutés au catalogue, du plus récent au plus ancien. Chaque produit a sa photo avec une étiquette "Nouveau", sa catégorie, son nom sans accents, son prix "À partir de …" et un bouton "Commander" vers sa fiche. 3 colonnes en desktop, 2 colonnes en mobile. La liste se met à jour toute seule dès qu'un produit est publié, sans rien régler dans l'admin.
- **Remarques :** commit XXXXXXX, thème en version 1.14.0. Pas de nouveau template : la section est créée par la fonction watsons_nouveautes() et le code court [watsons_nouveautes], ajouté dans le contenu de la page d'accueil avant [watsons_carte]. Les cartes réutilisent les classes produit-carte des pages "Nos moments". Testé en changeant la date de publication d'un produit, qui passe alors en premier. Base importée en prod pour le code court de la page d'accueil.

### 23/10/2026 – IT 17 : Génération de factures

- **Mis en prod :** extension "PDF Invoices & Packing Slips for WooCommerce" réglée dans WooCommerce -> Factures PDF : logo, nom, adresse, téléphone, e-mail, SIRET et TVA du Watson's Pub, mentions légales en pied de page, numérotation 2026-0001, 2026-0002… La facture est créée automatiquement quand la commande passe "En cours" ou "Terminée" (commande validée) et jointe au mail envoyé au client, pas de facture pour une commande en attente ou échouée. Modèle de facture "watsons" comme la maquette : logo et "FACTURE" avec numéro et date, coordonnées du pub et cadre du client, cases commande / date / réception / paiement, tableau des produits avec prix unitaire, TVA et total TTC, code promo, frais de préparation et livraison, totaux HT / TVA / TTC, mention "Facture acquittée" en vert si la commande est payée, bande rouille en bas. Dans Mon compte : colonne "Facture" avec une icône de téléchargement dans Mes commandes, bouton "Télécharger ma facture (PDF)" et numéro de facture sur la page détail d'une commande.
- **Remarques :** commit 253ae28, thème en version 1.15.0. Nouveau template woocommerce/pdf/watsons/invoice.php (modèle personnalisé de l'extension), dashboard.php et view-order.php mis à jour. Le style de la facture est à la fin de style.css entre "DEBUT FACTURE PDF" et "FIN FACTURE PDF", lu par functions.php comme pour les e-mails (couleurs en hexadécimal et tableaux, car le moteur PDF ne gère ni les variables CSS ni flex/grid). Les taxes WooCommerce n'étant pas activées, la facture calcule une TVA de 10 % comprise dans les prix. Extension installée en prod avec wp plugin install : version 5.16.6 sur le serveur (la 7.1.3 n'est pas compatible), le modèle fonctionne avec les deux. Import de la base interrompu une première fois par un Ctrl+C (0 remplacement), relancé : 83 remplacements.

### 23/10/2026 – IT 18 : Recherche et filtrage

- **Mis en prod :** colonne de filtres à gauche de "La carte" (accueil et boutique), comme la maquette : recherche par mot (sans tenir compte des accents), curseur de prix maximum, cases Taille (lues dans l'attribut "Taille" des burgers, avec le nombre de produits), boutons "Filtrer" et "Réinitialiser les filtres". Les filtres se combinent avec les boutons de catégorie. Sous la colonne : les filtres actifs en étiquettes rouille avec une croix pour les retirer, et le nombre de produits trouvés. Message "Aucun produit" avec un bouton "Voir toute la carte" quand rien ne correspond. En mobile, la colonne est repliée dans un bouton "Filtrer". Le filtrage se fait sans recharger la page.
- **Remarques :** commit 44b7a8f, thème en version 1.16.0. Fonction watsons_carte() réécrite dans functions.php et nouvelle fonction watsons_tailles_produit(). Pas d'extension ni de réglage : seulement le thème mis en prod (git pull), pas d'import de la base. Premier git add refusé : le stat annonçait 1910 suppressions (fichiers abîmés par iCloud), fichiers restaurés avec git restore puis recollés avant le commit.
