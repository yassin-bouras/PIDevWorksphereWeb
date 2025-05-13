<?php
// filepath: c:\Users\jacem\OneDrive\Documents\GitHub\symfony\PIDevWorksphereWeb\fix_candidature_paths.php

require __DIR__.'/vendor/autoload.php';

use App\Entity\Candidature;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

// Créer le kernel Symfony
$kernel = new \App\Kernel($_SERVER['APP_ENV'] ?? 'dev', (bool) ($_SERVER['APP_DEBUG'] ?? true));
$kernel->boot();
$container = $kernel->getContainer();

/** @var EntityManagerInterface $entityManager */
$entityManager = $container->get('doctrine.orm.entity_manager');

// Récupérer le chemin du dossier uploads
$uploadsDir = $container->getParameter('uploads_directory');
if (!is_dir($uploadsDir)) {
    mkdir($uploadsDir, 0777, true);
    echo "Dossier uploads créé : $uploadsDir\n";
}

$filesystem = new Filesystem();
$count = [
    'cv_fixed' => 0,
    'lm_fixed' => 0,
    'files_copied' => 0,
    'errors' => 0
];

echo "=== Début de la correction des chemins de fichiers ===\n";
echo "Dossier uploads: $uploadsDir\n";

// Récupérer toutes les candidatures
$candidatures = $entityManager->getRepository(Candidature::class)->findAll();
echo "Nombre de candidatures trouvées: " . count($candidatures) . "\n";

foreach ($candidatures as $candidature) {
    // Traiter le CV
    $cvPath = $candidature->getCv();
    
    if ($cvPath && (strpos($cvPath, ':') !== false || strpos($cvPath, '\\') !== false || strpos($cvPath, '/') !== false)) {
        // C'est un chemin absolu, on extrait juste le nom du fichier
        $fileName = basename($cvPath);
        echo "Traitement du CV: $cvPath -> $fileName\n";
        
        try {
            // Vérifier si le fichier d'origine existe
            if (file_exists($cvPath)) {
                // Copier le fichier dans le dossier uploads
                $destinationPath = $uploadsDir . '/' . $fileName;
                if (!file_exists($destinationPath)) {
                    $filesystem->copy($cvPath, $destinationPath, true);
                    $count['files_copied']++;
                    echo "  Fichier copié: $destinationPath\n";
                } else {
                    echo "  Fichier déjà existant dans la destination\n";
                }
            } else {
                echo "  ATTENTION: Fichier source introuvable: $cvPath\n";
            }
            
            // Mettre à jour le chemin dans la base de données (même si le fichier n'existe pas)
            $candidature->setCv($fileName);
            $count['cv_fixed']++;
        } catch (\Exception $e) {
            echo "  ERREUR lors du traitement du CV: " . $e->getMessage() . "\n";
            $count['errors']++;
        }
    }
    
    // Traiter la lettre de motivation
    $lmPath = $candidature->getLettreMotivation();
    
    if ($lmPath && (strpos($lmPath, ':') !== false || strpos($lmPath, '\\') !== false || strpos($lmPath, '/') !== false)) {
        // C'est un chemin absolu, on extrait juste le nom du fichier
        $fileName = basename($lmPath);
        echo "Traitement de la lettre de motivation: $lmPath -> $fileName\n";
        
        try {
            // Vérifier si le fichier d'origine existe
            if (file_exists($lmPath)) {
                // Copier le fichier dans le dossier uploads
                $destinationPath = $uploadsDir . '/' . $fileName;
                if (!file_exists($destinationPath)) {
                    $filesystem->copy($lmPath, $destinationPath, true);
                    $count['files_copied']++;
                    echo "  Fichier copié: $destinationPath\n";
                } else {
                    echo "  Fichier déjà existant dans la destination\n";
                }
            } else {
                echo "  ATTENTION: Fichier source introuvable: $lmPath\n";
            }
            
            // Mettre à jour le chemin dans la base de données (même si le fichier n'existe pas)
            $candidature->setLettreMotivation($fileName);
            $count['lm_fixed']++;
        } catch (\Exception $e) {
            echo "  ERREUR lors du traitement de la lettre de motivation: " . $e->getMessage() . "\n";
            $count['errors']++;
        }
    }
}

// Enregistrer les modifications dans la base de données
$entityManager->flush();

echo "\n=== Résumé de la migration ===\n";
echo "CVs corrigés: {$count['cv_fixed']}\n";
echo "Lettres de motivation corrigées: {$count['lm_fixed']}\n";
echo "Fichiers copiés: {$count['files_copied']}\n";
echo "Erreurs rencontrées: {$count['errors']}\n";
echo "Migration terminée!\n";