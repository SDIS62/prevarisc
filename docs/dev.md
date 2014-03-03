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

	<!-- etablissement/:id -->
	<tr>
		<td>GET etablissement/:id</td>
		<td>
			Retourne un seul établissement identifié par le paramètre id.<br>
			
			<strong>URL </strong>/api/1.0/etablissement<br>

			<strong>Paramètres</strong>
			<ul>
				<li>id (requis) : L'ID numérique de l'établissement désiré.</li>
			</ul>
		</td>
	</tr>

	<!-- etablissement/historique/:id -->
	<tr>
		<td>GET etablissement/historique/:id</td>
		<td>
			Retourne l'historique complet d'un établissement identifié par le paramètre id.<br>
			
			<strong>URL </strong>/api/1.0/etablissement/historique<br>

			<strong>Paramètres</strong>
			<ul>
				<li>id (requis) : L'ID numérique de l'établissement désiré.</li>
			</ul>
		</td>
	</tr>

	<!-- etablissement/descriptifs/:id -->
	<tr>
		<td>GET etablissement/descriptifs/:id</td>
		<td>
			Retourne les descriptifs de l'établissement identifié par le paramètre id.<br>
			
			<strong>URL </strong>/api/1.0/etablissement/descriptifs<br>

			<strong>Paramètres</strong>
			<ul>
				<li>id (requis) : L'ID numérique de l'établissement désiré.</li>
			</ul>
		</td>
	</tr>

	<!-- etablissement/textes_applicables/:id -->
	<tr>
		<td>GET etablissement/textes_applicables/:id</td>
		<td>
			Retourne les textes applicables de l'établissement identifié par le paramètre id.<br>
			
			<strong>URL </strong>/api/1.0/etablissement/textes_applicables<br>

			<strong>Paramètres</strong>
			<ul>
				<li>id (requis) : L'ID numérique de l'établissement désiré.</li>
			</ul>
		</td>
	</tr>

	<!-- etablissement/pieces_jointes/:id -->
	<tr>
		<td>GET etablissement/pieces_jointes/:id</td>
		<td>
			Retourne les pièces jointes de l'établissement identifié par le paramètre id.<br>
			
			<strong>URL </strong>/api/1.0/etablissement/pieces_jointes<br>

			<strong>Paramètres</strong>
			<ul>
				<li>id (requis) : L'ID numérique de l'établissement désiré.</li>
			</ul>
		</td>
	</tr>

	<!-- etablissement/contacts/:id -->
	<tr>
		<td>GET etablissement/contacts/:id</td>
		<td>
			Retourne les contacts de l'établissement identifié par le paramètre id.<br>
			
			<strong>URL </strong>/api/1.0/etablissement/contacts<br>

			<strong>Paramètres</strong>
			<ul>
				<li>id (requis) : L'ID numérique de l'établissement désiré.</li>
			</ul>
		</td>
	</tr>

	<!-- etablissement/dossiers/:id -->
	<tr>
		<td>GET etablissement/dossiers/:id</td>
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
				<li>categorie : Catégorie de l'établissement. (pour un genre etablissement ou cellule)</li>
				<li>local_sommeil : Vaut true si l'établissement possède un ou des locaux à sommeil. (pour un genre etablissement)</li>
				<li>classe : Classe de l'établissement. (pour un genre igh)</li>
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
					<li>genre : Genres des établissements recherchés.</li>
					<li>categorie : Catégories des établissements recherchés.</li>
					<li>classe : Catégories des établissements recherchés.</li>
					<li>famille : Familles des établissements recherchés.</li>
					<li>type : Type des établissements recherchés.</li>
					<li>avis_favorable : Inclure uniquement les établissements favorables quand le paramètre avis_favorable vaut true.</li>
					<li>statut : Statuts des établissements recherchés</li>
					<li>local_sommeil : Inclure uniquement les établissements contenants des locaux à sommeil quand le paramètre local_sommeil vaut true.</li>
					<li>count : Nombre maximum d'établissements dans le résultat. Défaut : 10, Maximum : 100.</li>
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