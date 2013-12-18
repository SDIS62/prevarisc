
$(document).ready(function(){	
	//CHARGEMENT DE L'ENTETE DES DOSSIERS AVEC L'INFORMATIONS DES ETABS
	//Affichage et affectation des différents champs !
	//Empeche la touche entrée de valider le formulaire

	//JQuery UI Date picker
	$('.date').live('click', function() {
		$(this).datepicker({showOn:'focus', dateFormat: 'dd/mm/yy', firstDay: 1}).focus();
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
	/*
		$("#calendar").hide();
		$("#calendar").parent().hide();
		$(this).hide();
		$(".showCalendar").show();	
	*/
		return false;
	});

	
	$("#AVIS_DOSSIER").live('change',function(){
		//alert($(this).val());
		if($(this).val() == 3){
			$("#ANOMALIE").show();
			$("#ANOMALIE_DOSSIER").val('');
		}else{
			$("#ANOMALIE").hide();
			$("#ANOMALIE_DOSSIER").val('');
		}
	});
	
	
	
	
	
	/*
		GESTION DES PRESCRIPTIONS MOTIVANT UN AVIS DEFAVORABLE
	*/
	var dialogMAD = $('<div style="display:none"></div>').appendTo('body');
	
	dialogMAD.dialog({
		title: "Motive avis défavorable",
		modal: true,
		autoOpen: false,
		buttons: {
			"Valider": function() {
				/*
				$.ajax({
					type: "POST",
					data: "format=html&"+$("#formPhone").serialize(),
					url: "/admin/savephone",
					beforeSend: function(){
						
					},
					success: function(msg){
						dialogMAD.dialog("close");
						
						$("#formPhone").remove();
						$("#adminPhone").click();
						return false;
					},
					error: function(){
						return false;
					}
				});
				*/
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
			/*
			$(".motiveAvisDef").each(function(){
				$(this).attr('disabled','disabled');
			});
			$(this).attr('disabled','');
			*/
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
			/*
			$(".motiveAvisDef").each(function(){
				$(this).attr('disabled','');
			});
			*/
		}
	});
	
	
}); //FIN DOCUMENT READY FUNCTION
	
	
	
	

