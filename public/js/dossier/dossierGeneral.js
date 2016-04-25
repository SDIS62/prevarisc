
$(document).ready(function(){	
	//CHARGEMENT DE L'ENTETE DES DOSSIERS AVEC L'INFORMATIONS DES ETABS
	//Affichage et affectation des différents champs !
	//Empeche la touche entrée de valider le formulaire

	//JQuery UI Date picker
	$('.date').live('click', function() {
		$(this).datepicker({showOn:'focus'}).focus();
	});
	
	//Pour les heures
	$('.time').live('focus', function() {
		$(this).timeEntry($.timeEntry.regional['fr']);
	});
			
	$("#DATEINSERT_INPUT").mask("99/99/9999",{placeholder:" "});

	$("#addNumDoc").live('click',function(){
		if($("#NUM_DOCURBA").val() != ''){
			$("#listeDocUrba").append("<div class='docurba' style=''><input type='hidden' name='docUrba[]' value='"+$("#docurbaVal").html()+$("#NUM_DOCURBA").val()+"' id='urba_"+$("#docurbaVal").html()+$("#NUM_DOCURBA").val()+"'/>"+$("#docurbaVal").html()+$("#NUM_DOCURBA").val()+" <a href='' idDocurba='"+$("#docurbaVal").html()+$("#selectNature").val()+"'class='suppDocUrba'>&times;</a></div>");
			$("#NUM_DOCURBA").val('');
		}
		return false;
	});
	
	$(".suppDocUrba").live('click',function(){
		$(this).parent().remove();
		return false;
	});
	
	
	$("#OBJET_DOSSIER").blur(function(){
		if($("#OBJET_DOSSIER").val() != ''){
			$("#OBJET_DOSSIER").css("border-color","black");
		}
	});	
	
	//Permet de vider un input d'une date pour que celle-ci ne s'affiche plus
	$(".suppDate").live('click',function(){
		$(this).prev('.date').attr('value','');			
		return false;
	});

	$(".hideCalendar").live('click',function(){
		return false;
	});

	/*
	$("#AVIS_DOSSIER").live('change',function(){
		alert('');
		var genre = $('#genreInfo').val();
		var typeDossier = $("#TYPE_DOSSIER").val();
		var natureDossier = $("#selectNature").val();
		
		if($(this).val() == 2){
			if(genre == 2 && (typeDossier == 2 || typeDossier == 3))
			{
				if(natureDossier == 21 || natureDossier == 23 || natureDossier == 24 || natureDossier == 26 || natureDossier == 28 || natureDossier == 29 )
				{
					$("#FACTDANGE").show();
				}		
			}
		}else{
			$("#FACTDANGE").hide();
		}
	});
		GESTION DES PRESCRIPTIONS MOTIVANT UN AVIS DEFAVORABLE
	*/
	/*
	var dialogMAD = $('<div style="display:none"></div>').appendTo('body');
	
	dialogMAD.dialog({
		title: "Motive avis défavorable",
		modal: true,
		autoOpen: false,
		buttons: {
			"Valider": function() {
				dialogMAD.dialog("close");
				return false;
			},
			"annuler": function() {
				dialogMAD.dialog("close");
			}
		},
		close: function(event, ui){
			
		}
	});
	
	
	$(".motiveAvisDef").live('click',function(){
		//motive avis défavorable clickage de check
		if($(this).attr('checked')){
			//aficher une boite dialogue en disant que ça changera l'avis du dossier en défavo
			//puis faire apparaitre croix de suppression ou alors avec la modif ?
			var numPresc = $(this).val();
			
			$.ajax({
				type: "POST",
				url: "/dossier/fonction?do=showMadContent&numPresc="+numPresc,
				success: function(msg){				
					dialogMAD.html(msg);
					dialogMAD.dialog("open");
				}
			});
			
		}else{

		}
	});
	*/
	
}); //FIN DOCUMENT READY FUNCTION
	
	
	
	

