<?php

/**
 * SPIP, Système de publication pour l'internet
 *
 * Copyright © avec tendresse depuis 2001
 * Arnaud Martin, Antoine Pitrou, Philippe Rivière, Emmanuel Saint-James
 *
 * Ce programme est un logiciel libre distribué sous licence GNU/GPL.
 */

declare(strict_types=1);

/**
 * Écrire un contenu dans un fichier encapsulé en PHP pour en empêcher l'accès en l'absence
 * de fichier htaccess
 *
 * @uses ecrire_fichier()
 *
 * @param string $fichier
 *     Chemin du fichier
 * @param string $contenu
 *     Contenu à écrire
 * @param bool $ecrire_quand_meme
 *     - true pour ne pas raler en cas d'erreur
 *     - false affichera un message si on est webmestre
 * @param bool $truncate
 *     Écriture avec troncation ?
 */
function ecrire_fichier_securise($fichier, $contenu, $ecrire_quand_meme = false, $truncate = true) {
	if (!str_ends_with($fichier, '.php')) {
		spip_logger()->info('Erreur de programmation: ' . $fichier . ' doit finir par .php');
	}
	$contenu = '<' . "?php die ('Acces interdit'); ?" . ">\n" . $contenu;

	return ecrire_fichier($fichier, $contenu, $ecrire_quand_meme, $truncate);
}
