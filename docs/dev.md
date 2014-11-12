# Développeurs

- [Ressources sur l'API REST 1.0](#api)

<a name="api"></a>
## Documentation de l'API REST 1.0

### Établissements

<table class='table table-bordered table-condensed'>
	<thead>
		<tr>
			<th>Ressource</th>
			<th>Description</th>
		</tr>
	</thead>
	<tbody>

	<!-- etablissement -->
	<tr>
		<td>GET etablissement</td>
		<td>
			Retourne un seul établissement identifié par le paramètre id.<br>

			<strong>URL </strong>/api/1.0/etablissement<br>

			<strong>Paramètres</strong>
			<ul>
				<li>id (requis) : L'ID numérique de l'établissement désiré.</li>
			</ul>
		</td>
	</tr>

	<!-- etablissement/historique -->
	<tr>
		<td>GET etablissement/historique</td>
		<td>
			Retourne l'historique complet d'un établissement identifié par le paramètre id.<br>

			<strong>URL </strong>/api/1.0/etablissement/historique<br>

			<strong>Paramètres</strong>
			<ul>
				<li>id (requis) : L'ID numérique de l'établissement désiré.</li>
			</ul>
		</td>
	</tr>

	<!-- etablissement/descriptifs -->
	<tr>
		<td>GET etablissement/descriptifs</td>
		<td>
			Retourne les descriptifs de l'établissement identifié par le paramètre id.<br>

			<strong>URL </strong>/api/1.0/etablissement/descriptifs<br>

			<strong>Paramètres</strong>
			<ul>
				<li>id (requis) : L'ID numérique de l'établissement désiré.</li>
			</ul>
		</td>
	</tr>

	<!-- etablissement/textes_applicables -->
	<tr>
		<td>GET etablissement/textes_applicables</td>
		<td>
			Retourne les textes applicables de l'établissement identifié par le paramètre id.<br>

			<strong>URL </strong>/api/1.0/etablissement/textes_applicables<br>

			<strong>Paramètres</strong>
			<ul>
				<li>id (requis) : L'ID numérique de l'établissement désiré.</li>
			</ul>
		</td>
	</tr>

	<!-- etablissement/pieces_jointes -->
	<tr>
		<td>GET etablissement/pieces_jointes</td>
		<td>
			Retourne les pièces jointes de l'établissement identifié par le paramètre id.<br>

			<strong>URL </strong>/api/1.0/etablissement/pieces_jointes<br>

			<strong>Paramètres</strong>
			<ul>
				<li>id (requis) : L'ID numérique de l'établissement désiré.</li>
			</ul>
		</td>
	</tr>

	<!-- etablissement/contacts -->
	<tr>
		<td>GET etablissement/contacts</td>
		<td>
			Retourne les contacts de l'établissement identifié par le paramètre id.<br>

			<strong>URL </strong>/api/1.0/etablissement/contacts<br>

			<strong>Paramètres</strong>
			<ul>
				<li>id (requis) : L'ID numérique de l'établissement désiré.</li>
			</ul>
		</td>
	</tr>

	<!-- etablissement/dossiers -->
	<tr>
		<td>GET etablissement/dossiers</td>
		<td>
			Retourne une collection de dossiers appartenants à l'établissement identifié par le paramètre id.<br>

			<strong>URL </strong>/api/1.0/etablissement/dossiers<br>

			<strong>Paramètres</strong>
			<ul>
				<li>id (requis) : L'ID numérique de l'établissement désiré.</li>
			</ul>
		</td>
	</tr>

	<!-- etablissement/defaults_values -->
	<tr>
		<td>GET etablissement/defaults_values</td>
		<td>
			Retourne les valeurs par défauts (périodicité, commission, préventionnistes) pour un établissement en fonction des paramètres données.<br>

			<strong>URL </strong>/api/1.0/defaults_values<br>

			<strong>Paramètres</strong>
			<ul>
				<li>genre (requis) : Le genre de l'établissement.</li>
				<li>numinsee : Numéro insee de la commune de l'établissement.</li>
				<li>type : Type d'activité de l'établissement.</li>
				<li>categorie : Catégorie de l'établissement. (pour un genre etablissement ou cellule)</li>
				<li>local_sommeil : Vaut true si l'établissement possède un ou des locaux à sommeil. (pour un genre etablissement)</li>
				<li>classe : Classe de l'établissement. (pour un genre igh)</li>
				<li>id_etablissement_pere : Liste des ids des établissement parents.</li>
				<li>ids_etablissements_enfants : Liste des ids des établissement enfants.</li>
			</ul>
		</td>
	</tr>

	</tbody>
</table>

### Recherche

<table class='table table-bordered table-condensed'>
	<thead>
		<tr>
			<th>Ressource</th>
			<th>Description</th>
		</tr>
	</thead>
	<tbody>
		<!-- search/etablissements -->
		<tr>
			<td>GET search/etablissements</td>
			<td>
				Retourne une collection d'établissements correspondants à la requête.<br>

				<strong>URL </strong>/api/1.0/search/etablissements<br>

				<strong>Paramètres</strong>
				<ul>
					<li>label : Libellé (exact ou non) de l'établissement recherché.</li>
					<li>identifiant : Identifiant de l'établissement.</li>
					<li>genres : Genres des établissements recherchés.</li>
					<li>categories : Catégories des établissements recherchés.</li>
					<li>classes : Catégories des établissements recherchés.</li>
					<li>familles : Familles des établissements recherchés.</li>
					<li>types_activites : Type d'activité des établissements recherchés.</li>
					<li>avis_favorable : Inclure uniquement les établissements favorables quand le paramètre avis_favorable vaut true.</li>
					<li>statuts : Statuts des établissements recherchés</li>
					<li>local_sommeil : Inclure uniquement les établissements contenants des locaux à sommeil quand le paramètre local_sommeil vaut true.</li>
					<li>lon : Longitude approximative de l'emplacement de l'établissement (A utiliser conjointement avec le paramètre lat).</li>
					<li>lat : Latitude approximative de l'emplacement de l'établissement (A utiliser conjointement avec le paramètre lon).</li>
					<li>count : Nombre maximum d'établissements dans le résultat. Défaut : 10, Maximum : 1000.</li>
					<li>page : Numéro de page à afficher.</li>
				</ul>
			</td>
		</tr>

		<!-- search/dossiers -->
		<tr>
			<td>GET search/dossiers</td>
			<td>
				Retourne une collection de dossiers correspondants à la requête.<br>

				<strong>URL </strong>/api/1.0/search/dossiers<br>

				<strong>Paramètres</strong>
				<ul>
					<li>types : Types des dossiers recherchés</li>
					<li>objet : Objet du document.</li>
					<li>num_doc_urba : Numéro de document d'urbanisme présent dans le dossier recherché.</li>
					<li>parent : Parent des dossiers recherchés.</li>
					<li>avis_differe : Dossiers ayant un avis différé.</li>
					<li>count : Nombre maximum de dossiers dans le résultat. Défaut : 10, Maximum : 100.</li>
					<li>page : Numéro de page à afficher.</li>
				</ul>
			</td>
		</tr>

		<!-- search/users -->
		<tr>
			<td>GET search/users</td>
			<td>
				Retourne une collection d'utilisateurs correspondants à la requête.<br>

				<strong>URL </strong>/api/1.0/search/users<br>

				<strong>Paramètres</strong>
				<ul>
					<li>name : Nom approximatif de l'utilisateur.</li>
					<li>fonctions : Ids des fonctions des utilisateurs à rechercher.</li>
					<li>groups : Ids des groupes d'utilisateurs à rechercher.</li>
					<li>actif : Inclure uniquement les utilisateurs actifs.</li>
					<li>count : Nombre maximum d'établissements dans le résultat. Défaut : 10, Maximum : 100.</li>
					<li>page : Numéro de page à afficher.</li>
				</ul>
			</td>
		</tr>
	</tbody>
</table>

### Contact

<table class='table table-bordered table-condensed'>
	<thead>
		<tr>
			<th>Ressource</th>
			<th>Description</th>
		</tr>
	</thead>
	<tbody>
		<!-- contacts -->
		<tr>
			<td>GET contacts</td>
			<td>
				Récupération de l'ensemble des contacts.<br>

				<strong>URL </strong>/api/1.0/contacts<br>

				<strong>Paramètres</strong>
				<ul>
					<li>name (requis) : Nom du contact.</li>
				</ul>
			</td>
		</tr>
	</tbody>
</table>

### Adresse

<table class='table table-bordered table-condensed'>
	<thead>
		<tr>
			<th>Ressource</th>
			<th>Description</th>
		</tr>
	</thead>
	<tbody>
		<!-- adresse/get_communes -->
		<tr>
			<td>GET adresse/get_communes</td>
			<td>
				Récupération des communes via le nom ou le code postal.

				<strong>URL </strong>/api/1.0/adresse/get_communes<br>

				<strong>Paramètres</strong>
				<ul>
					<li>q (requis) : Code postal ou nom d'une commune.</li>
				</ul>
			</td>
		</tr>

		<!-- adresse/get_types_voie_par_ville -->
		<tr>
			<td>GET adresse/get_types_voie_par_ville</td>
			<td>
				Retourne les types de voie d'une commune identifiée par son code insee.

				<strong>URL </strong>/api/1.0/adresse/get_types_voie_par_ville<br>

				<strong>Paramètres</strong>
				<ul>
					<li>code_insee (requis) : Code insee de la commune.</li>
				</ul>
			</td>
		</tr>

		<!-- adresse/get_voies -->
		<tr>
			<td>GET adresse/get_voies</td>
			<td>
				Retourne les voies par rapport à une ville.

				<strong>URL </strong>/api/1.0/adresse/get_voies<br>

				<strong>Paramètres</strong>
				<ul>
					<li>code_insee (requis) : Code insee de la commune.</li>
					<li>q : Nom de la voie.</li>
				</ul>
			</td>
		</tr>
	</tbody>
</table>

### Tests

<table class='table table-bordered table-condensed'>
	<thead>
		<tr>
			<th>Ressource</th>
			<th>Description</th>
		</tr>
	</thead>
	<tbody>
		<!-- test -->
		<tr>
			<td>GET test</td>
			<td>
				Retourne un résultat test.<br>

				<strong>URL </strong>/api/1.0/test<br>
			</td>
		</tr>
	</tbody>
</table>
