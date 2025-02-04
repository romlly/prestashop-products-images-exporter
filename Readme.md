# PrestaShop Image Exporter

Un script CLI pour exporter toutes les images produits d'une boutique PrestaShop 8.1 avec leurs références.

## Fonctionnalités

- Export des images produits avec la référence du produit comme nom de fichier
- Suffixe "_cover" pour l'image principale et "_X" pour les images secondaires
- Organisation des images dans des dossiers par référence produit
- Messages de progression détaillés
- Gestion des erreurs
- Compatible PrestaShop 8.1

## Prérequis

- PrestaShop 8.1
- PHP 7.4 ou supérieur
- Droits d'écriture dans le dossier du script

## Installation

1. Placez le fichier `export-images.php` à la racine de votre installation PrestaShop
2. Rendez le script exécutable :
   ```bash
   chmod +x export-images.php
   ```

## Utilisation

Exécutez le script depuis la ligne de commande :
```bash
php export-images.php
```

### Structure des fichiers exportés

```
export_images/
├── REF123/
│   ├── REF123_cover.jpg
│   ├── REF123_1.jpg
│   └── REF123_2.jpg
├── REF456/
│   ├── REF456_cover.jpg
│   └── REF456_1.jpg
└── ...
```

- Chaque produit a son propre dossier nommé d'après sa référence
- Les images sont nommées avec la référence du produit suivie de :
  - `_cover` pour l'image principale
  - `_1`, `_2`, etc. pour les images supplémentaires selon leur position

## Messages d'erreur

- Si un produit n'a pas de référence, le script utilise `PROD_[ID]` comme référence
- Les erreurs sont affichées en rouge dans la console
- Les avertissements sont affichés en jaune
- Les succès sont affichés en vert

## Résolution des problèmes

1. **Le dossier d'export n'est pas créé**
   - Vérifiez les permissions du dossier racine
   - Assurez-vous que PHP a les droits d'écriture

2. **Aucun produit n'est trouvé**
   - Vérifiez que vous êtes bien à la racine de PrestaShop
   - Vérifiez que la base de données est accessible

3. **Les images ne sont pas copiées**
   - Vérifiez les permissions du dossier `img/p/`
   - Assurez-vous que les images existent physiquement

## Support

Pour les problèmes et questions, veuillez ouvrir une issue sur ce dépôt.

## Licence

MIT License
