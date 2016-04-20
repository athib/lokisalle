<?php

class statistiquesController extends adminController
{
	/**
	 * 1/ Sélection des 5 meilleures notes moyennes de toutes les salles en éliminant les doublons
	 * 2/ Sélection de la plus petite de ces 5 notes
	 * 3/ Sélection de toutes les salles ayant une note moyenne >= à cette note minimale
	 *
	 * Cette méthode permet de récupérer toutes les salles dans le top 5, y compris les exaequo 
	 */
	public function getTop5NotesSalles()
	{
		$requete = "SELECT id_salle, titre, ville, ROUND(AVG(note), 2) as note_moyenne
					FROM salle
					NATURAL JOIN avis
					GROUP BY id_salle
					HAVING note_moyenne >= (
						SELECT MIN(note_moyenne)
						FROM (
							SELECT DISTINCT(ROUND(AVG(note), 2)) AS note_moyenne
							FROM avis
							GROUP BY id_salle
							ORDER BY note_moyenne DESC
							LIMIT 5
						) AS max_avis
					)
					ORDER BY note_moyenne DESC";

		$stmt = $this->session->getDatabase()->executeRequete($requete);

		return $this->session->getDatabase()->getAllResults($stmt);
	}


	public function getTop5VentesSalles()
	{
		$requete = "SELECT id_salle, titre, ville, COUNT(*) AS nb_ventes
					FROM details_commande
					NATURAL JOIN produit
						NATURAL JOIN salle
					GROUP BY id_salle
					HAVING nb_ventes >= (
						SELECT MIN(nb_ventes)
						FROM (
							SELECT DISTINCT(COUNT(*)) AS nb_ventes
							FROM details_commande
							NATURAL JOIN produit
							GROUP BY id_salle
							LIMIT 5
						) AS ventes
					)
					ORDER BY nb_ventes DESC";

		$stmt = $this->session->getDatabase()->executeRequete($requete);

		return $this->session->getDatabase()->getAllResults($stmt);
	}

	public function getTop5MembresAcheteursQuantite()
	{
		$requete = "SELECT id_membre, pseudo, COUNT(*) AS nb_commandes
					FROM commande
						NATURAL JOIN membre
					GROUP BY id_membre
					HAVING nb_commandes >= (
						SELECT MIN(nb_commandes)
						FROM (
							SELECT DISTINCT(COUNT(*)) AS nb_commandes
							FROM commande
							GROUP BY id_membre
							ORDER BY nb_commandes DESC
						   ) as cmd
						LIMIT 5
					)
					ORDER BY nb_commandes DESC";

		$stmt = $this->session->getDatabase()->executeRequete($requete);

		return $this->session->getDatabase()->getAllResults($stmt);
	}


	public function getTop5MembresAcheteursPrix()
	{
		$requete = "SELECT id_membre, pseudo, montant
					FROM commande
					NATURAL JOIN membre
					GROUP BY id_membre
					HAVING montant >= (
					  SELECT MIN(DISTINCT(montant)) AS min_montant
					  FROM (
					         SELECT montant
					         FROM commande
					           NATURAL JOIN membre
					         GROUP BY id_membre
					         ORDER BY montant DESC
					       ) AS montants
					  LIMIT 5
					)
					ORDER BY montant DESC";

		$stmt = $this->session->getDatabase()->executeRequete($requete);

		return $this->session->getDatabase()->getAllResults($stmt);
	}
}