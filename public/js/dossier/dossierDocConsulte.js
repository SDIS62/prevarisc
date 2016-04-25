$(document).ready(function(){

	$('.date').live('click', function() {
		$(this).datepicker({showOn:'focus', dateFormat: 'dd/mm/yy', firstDay: 1}).focus();
	});
	

	$(".cancelDoc").live('click',function(){
		var nomTab = $(this).parent().attr('id').split('_');
		if(nomTab.length == 3){
			var nom = nomTab[1]+"_"+nomTab[2];
		}else{
			var nom = nomTab[1]+"_"+nomTab[2]+"_aj";
		}	
		switch($("#tmp").val()){
			case "new":
				$("#div_input_"+nom).fadeOut();
				$("#div_edit_"+nom).fadeIn();
				$("#check_"+nom).removeAttr('checked');
				$("#ref_"+nom).attr('readonly','true').attr('value','');
				$("#date_"+nom).attr('readonly','true').attr('value','');
			break;
			case "edit":
				$("#modif_"+nom).fadeIn();
				$("#valid_"+nom).hide();
				$("#date_"+nom).attr('readonly','true').attr('disabled','disabled').attr('value',$("#tmpDate").val());
				$("#ref_"+nom).attr('readonly','true').attr('value',$("#tmpRef").val());
			break;
			case "ajoutDoc":
				$("#formNewDoc").fadeOut(function(){
					$("#docAjout").fadeIn();						
				});
				$("#libelleNewDoc").attr('value','');
			break;
		}
		$("#dossier_Pdroite").showModif(nom);
		$("#dossier_Pdroite").activeCheck(nom);
		$("#tmpRef").attr('value','');
		$("#tmpDate").attr('value','');
		$("#tmp").attr('value','');
		return false;
		//return false;
	});
	
	//déclaration de la boite de dialog pour l'ajout d'un document ne faisant pas parti de la liste de base
	$("#dialogDocConsulte").dialog({
		resizable: false,
		height:300,
		width:900,
		autoOpen: false,
		modal: true,
		title: 'Ajouter un document consulté',
		buttons: {
			'Enregistrer le document': function() {
				if($("#libelleNewDoc").val() == ''){
					$("#libelleNewDoc").focus();
					return false;
				}else{
					$(this).ajoutDocDialog($("#natureDocAjout").val());
					$(this).dialog('close');
				}
				$(this).dialog('close');
				//ici sauvegarder et afficher en ajax le doc qui a été ajouté
			},
			'Annuler': function() {
				$(this).dialog('close');
				//ici réinitialiser les champs à vide
			}
		},
		close: function(event, ui){
			
			$("body").css('overflow','auto');
			$("#libelleNewDoc").val('');
			$("#natureDocAjout").val('');
		}
	});
	
	//déclaration de la boite de dialog permettant la confirmation de la suppression d'un document ajouté de faisant parti de la liste de base
	$("#dialogConfirmSuppDoc").dialog({
		resizable: false,
		height:200,
		width:450,
		autoOpen: false,
		modal: true,
		title: 'Voulez vous vraiment supprimer ce document ?',
		buttons: {
			'Supprimer': function() {
				//ici on supprime dans la base de données le document lié puis on reinitialise la ligne
				$.ajax({
					url: "/dossier/suppdoc",
					data: "docInfos="+$("#docInfos").val()+"&idDossier="+$("#idDossier").val(),
					type:"POST",			
					beforeSend: function(){
						//VERIFICATION SUR L'integrité des données
					},
					success: function(affichageResultat){
						//$("#"+$("#docInfos").val());
						var tabInfos = $("#docInfos").val().split('_');
						if(tabInfos.length == 2){
							//doc de base
							$("#ref_"+$("#docInfos").val()).val('');
							$("#date_"+$("#docInfos").val()).val('');

							$("#div_input_"+$("#docInfos").val()).hide();
							$("#check_"+$("#docInfos").val()).removeAttr('checked');
							$("#check_"+$("#docInfos").val()).removeAttr('disabled');
							$("#dossier_Pdroite").activeCheck('');
							$("#dossier_Pdroite").showModif('');
							
							
							
						}else{
							//doc ajouté
							$("#"+$("#docInfos").val()).remove();
							$("#dossier_Pdroite").showModif('.');
							$("#dossier_Pdroite").activeCheck('');
						}	
						return false;
					},
					error: function(){
						return false;
					}
				});
				
				$(this).dialog('close');
		
			},
			'Annuler': function() {
				$(this).dialog('close');
				//ici réinitialiser les champs à vide
			}
		},
		close: function(event, ui){
			$("body").css('overflow','auto');
		}
	});
	
	$(".docAjout").live('click',function(){
		//permet d'afficher la boite de dialogue pour l'ajouter de documents consultés
		var tabNature= $(this).attr('id').split('_');
		var idNature = tabNature[1];
		$("#natureDocAjout").val(idNature);
		$("#dialogDocConsulte").dialog('open');
		return false;
	});
	
	//Fonction javascript lorsque l'on clique sur modifier
	/*
	$("button[name=annulation]").live('click',function(){
		var nomTab = $(this).attr('id').split('_');
		if(nomTab.length == 2){
			var nom = nomTab[1];
		}else{
			var nom = nomTab[1]+"_aj";
		}
		switch($("#tmp").val()){
			case "new":
				$("#div_input_"+nom).fadeOut();
				$("#div_edit_"+nom).fadeIn();
				$("#check_"+nom).attr('checked','');
				$("#ref_"+nom).attr('readonly','true').attr('value','');
				$("#date_"+nom).attr('readonly','true').attr('value','');
			break;
			case "edit":
				$("#modif_"+nom).fadeIn();
				$("#valid_"+nom).hide();
				$("#date_"+nom).attr('readonly','true').attr('disabled','disabled').attr('value',$("#tmpDate").val());
				$("#ref_"+nom).attr('readonly','true').attr('value',$("#tmpRef").val());
			break;
			case "ajoutDoc":
				$("#formNewDoc").fadeOut(function(){
					$("#docAjout").fadeIn();						
				});
				$("#libelleNewDoc").attr('value','');
			break;
		}
		$("#dossier_Pdroite").showModif(nom);
		$("#dossier_Pdroite").activeCheck(nom);
		$("#tmpRef").attr('value','');
		$("#tmpDate").attr('value','');
		$("#tmp").attr('value','');
		return false;
	});
	*/
	
	//utilisé lorsque l'on ajoute un document à la liste des docs.
	$("#AjoutDocValid").click(function(){
		//alert('');
		if($("#libelleNewDoc").val() == ''){
			$("#libelleNewDoc").focus();
			return false;
		}

		$.ajax({
			url: "/dossier/fonction",
			data: "do=ajoutDocValid&libelledoc="+$("#libelleNewDoc").val()+"&idDossier="+$("#idDossier").val()+"&natureDocAjout"+$("#natureDocAjout").val(),
			type:"POST",			
			beforeSend: function(){
				//VERIFICATION SUR L'integrité des données
			},
			success: function(affichageResultat){
				//alert($(".divDoc:last").attr('id'));
				$("#listeDocs").append(affichageResultat);
				//affichageResultat.insertAfter(".divDoc:last");
				$("#libelleNewDoc").attr('value','');
				$("#dossier_Pdroite").activeCheck('qsd');

				return false;
			},
			error: function(){
				return false;
			}
		});
		return false;
	});

	
	$(".editDoc").live('click',function(){
		var nomTab = $(this).parent().attr('id').split('_');
		//alert(nomTab.length);
		var nature = nomTab[1];
		if(nomTab.length == 3){
			var nom = nomTab[2];
		}else{
			var nom = nomTab[2]+"_aj";
		}
		//alert(nom);
		nom = nature+"_"+nom;
		$("#tmpRef").attr('value',$("#ref_"+nom).val());
		$("#tmpDate").attr('value',$("#date_"+nom).val());
		$("#tmp").attr('value','edit');
		
		$("#ref_"+nom).removeAttr('readonly');
		$("#date_"+nom).removeAttr('readonly').removeAttr('disabled');
		 
		$("#modif_"+nom).hide();
		$("#valid_"+nom).fadeIn();

		$("#libelleView_"+nom).hide();
		$("#libelle_"+nom).show();

		$("#dossier_Pdroite").hideModif(nom);
		$("#dossier_Pdroite").blockCheck(nom); 
		return false;
	});

	//gestion de la suppression des documents consultés
	$(".deleteDoc").live('click',function(){
		var idDoc = $(this).attr('name');
		$('#docInfos').val(idDoc);
		$("#dialogConfirmSuppDoc").dialog('open');
		$("#affichageDocSupp").html($("#"+idDoc).children('.libelle').html());
		$("#refDocSupp").html($("#ref_"+idDoc).val());
		$("#dateDocSupp").html($("#date_"+idDoc).val());
		return false;
	});
/* à modifier le next car déplacé dans le div... plus utilisé pour le moment à voir si on le réintegre
	$(".hideDocConsulte").click(function(){
		var Div = $(this).next();

		if(Div.is(":hidden")){
			Div.show();
		}else{
			Div.hide();
		}
	});
*/	





});