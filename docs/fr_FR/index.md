# Plugin nest

## Description

Le plugin Nest permet de piloter le thermostat Nest et de récupérer les
informations du Nest Protect.

Ce plugin utilise https://github.com/gboudreau/nest-api modifié pour Jeedom.

## Changelog

./changelog

## Pré-Requis

- Vous devez vous connecter à votre compte Nest via un compte Google

## Installation

- Rajouter le plugin dans Jeedom en mode github :

ID=nest

Utilisateur=Ultraboss77

Dépôt=plugin-nest

branche=V4 ou master (si Jeedom >= 4), sinon V3_legacy

- Activer le plugin

> **IMPORTANT**
>
>Il n'y a pas d'API pour le Nest E en europe. Ce plugin ne permet donc pas de piloter un thermostat Nest E en europe.

> **IMPORTANT**
>
> Il n'y a pas de remonté en temps réel (juste une synchronisation toute les 5min), ce plugin ne peut donc etre utilisé pour avoir une alerte en temps réel en cas d'incendie (nest protect)

Configuration du plugin 
=======================

Une fois le plugin installé, il vous faut renseigner vos informations de
connexion Nest :

-   **issueToken** et **Cookies** :

Using a Google Account
----------------------
The values of `$issue_token`, and `$cookies` are specific to your Google Account. To get them, follow these steps (only needs to be done once, as long as you stay logged into your Google Account).

- Open a Chrome browser tab in Incognito Mode (or clear your cache).
- Open Developer Tools (View/Developer/Developer Tools).
- Click on `Network` tab. Make sure `Preserve Log` is checked.
- In the `Filter` box, enter `issueToken`
- Go to https://home.nest.com, and click `Sign in with Google`. Log into your account.
- One network call (beginning with `iframerpc`) will appear in the Dev Tools window. Click on it.
- In the `Headers` tab, under `General`, copy the entire `Request URL` (beginning with `https://accounts.google.com`, ending with `nest.com`). This is your `$issue_token`.
- In the `Filter` box, enter `oauth2/iframe`
- Several network calls will appear in the Dev Tools window. Click on the last iframe call.
- In the `Headers` tab, under `Request Headers`, copy the entire cookie value (include the whole string which is several lines long and has many field/value pairs - do not include the `Cookie:` prefix). This is your `$cookies`; make sure all of it is on a single line.

----------------------

-   **Synchroniser** : permet de synchroniser Jeedom avec votre compte
    Nest pour découvrir automatiquement vos équipements Nest. A faire
    après avoir sauvegardé les paramètres précedent.

Configuration des équipements 
=============================

La configuration des équipements Nest est accessible à partir du menu
plugin puis communication :

Vous retrouvez ici toute la configuration de votre équipement :

-   **Nom de l’équipement Nest** : nom de votre équipement Nest

-   **Objet parent** : indique l’objet parent auquel appartient
    l’équipement

-   **Activer** : permet de rendre votre équipement actif

-   **Visible** : le rend visible sur le dashboard

-   **Catégorie** : les catégories de l’équipement (il peut appartenir à
    plusieurs catégories)

Sur la gauche vous retrouvez :

-   **Type** : le type de votre Nest

-   **ID** : l’ID de votre équipement chez Nest

-   **IP** : l’IP de votre équipement Nest

-   **MAC** : l’adresse MAC de votre équipement Nest

-   **Batterie** : la batterie (en mV)

-   **Santé** : santé de votre Nest (0 ⇒ OK, 1 ⇒ NOK (pas OK))

-   **Remplacer le** : date de remplacement des piles

-   **Dernière mise à jour** : date de dernière mise à jour des info
    (sur un Protect c’est une fois toutes les 24h)

-   **Dernier test** : date de dernier test (Protect seulement)

En dessous vous retrouvez la liste des commandes :

-   le nom affiché sur le dashboard

-   historiser : permet d’historiser la donnée

-   configuration avancée (petites roues crantées) : permet d’afficher
    la configuration avancée de la commande (méthode
    d’historisation, widget…​)

-   Tester : permet de tester la commande

-   supprimer (signe -) : permet de supprimer la commande


