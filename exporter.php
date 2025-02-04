#!/usr/bin/env php
<?php
echo "Étape 1: Démarrage du script\n";

// Vérification de l'exécution en CLI
if (PHP_SAPI !== 'cli') {
    die('Ce script doit être exécuté en ligne de commande.');
}

echo "Étape 2: Mode CLI confirmé\n";

// Définition de l'environnement
define('_PS_MODE_DEV_', false);
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Étape 3: Variables d'environnement définies\n";

// Configuration de l'environnement CLI
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['SERVER_NAME'] = '127.0.0.1';
$_SERVER['REQUEST_URI'] = dirname($_SERVER['PHP_SELF']);
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

echo "Étape 4: Variables SERVER définies\n";
echo "Chemin du script: " . __FILE__ . "\n";
echo "Dossier courant: " . getcwd() . "\n";

try {
    echo "Étape 5: Début du bloc try\n";

    // Chargement de l'autoloader Composer
    echo "Étape 6: Tentative de chargement de l'autoloader\n";
    require_once(dirname(__FILE__).'/vendor/autoload.php');
    
    echo "Étape 7: Tentative de chargement de config.inc.php\n";
    require_once(dirname(__FILE__).'/config/config.inc.php');
    
    echo "Étape 8: PrestaShop initialisé\n";
    
    // Configuration
    $exportDir = _PS_ROOT_DIR_ . '/export_images/';
    echo "Étape 9: Dossier d'export défini: $exportDir\n";
    
    // Création du dossier d'export
    if (!is_dir($exportDir)) {
        echo "Étape 10: Tentative de création du dossier d'export\n";
        if (!mkdir($exportDir, 0755, true)) {
            $error = error_get_last();
            throw new Exception("Impossible de créer le dossier d'export : " . ($error ? $error['message'] : 'Erreur inconnue'));
        }
    }
    
    echo "Étape 11: Récupération des produits\n";
    $products = Product::getProducts(Context::getContext()->language->id, 0, 0, 'id_product', 'ASC', false, true);
    
    echo "Étape 12: Nombre de produits trouvés : " . (is_array($products) ? count($products) : "0") . "\n";

    if (empty($products)) {
        throw new Exception("Aucun produit trouvé");
    }

    foreach ($products as $product) {
        $id_product = $product['id_product'];
        echo "Traitement du produit ID: $id_product\n";
        
        // Récupération de la référence
        $productObj = new Product($id_product);
        $reference = $productObj->reference;
        
        if (empty($reference)) {
            echo "Pas de référence pour le produit $id_product, utilisation de PROD_$id_product\n";
            $reference = 'PROD_' . $id_product;
        }
        
        // Nettoyage de la référence
        $reference = preg_replace('/[^a-zA-Z0-9-_]/', '_', $reference);
        
        // Création du dossier produit
        $productDir = $exportDir . $reference . '/';
        if (!is_dir($productDir) && !mkdir($productDir, 0755, true)) {
            echo "Impossible de créer le dossier pour le produit $reference\n";
            continue;
        }
        
        // Récupération des images
        $images = Image::getImages(Context::getContext()->language->id, $id_product);
        
        if (empty($images)) {
            echo "Aucune image pour le produit $reference\n";
            continue;
        }
        
        foreach ($images as $image) {
            $imagePath = _PS_PROD_IMG_DIR_ . Image::getImgFolderStatic($image['id_image']) . $image['id_image'] . '.jpg';
            
            if (!file_exists($imagePath)) {
                echo "Image non trouvée : $imagePath\n";
                continue;
            }
            
            $suffix = ($image['cover'] == 1) ? '_cover' : '_' . $image['position'];
            $newImagePath = $productDir . $reference . $suffix . '.jpg';
            
            if (copy($imagePath, $newImagePath)) {
                echo "Image exportée : " . basename($newImagePath) . "\n";
            } else {
                echo "Échec de copie : $imagePath\n";
            }
        }
    }

} catch (Exception $e) {
    echo "ERREUR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "Fin du script\n";
exit(0);
