
$(document).ready(function(){	
	//CHARGEMENT DE L'ENTETE DES DOSSIERS AVEC L'INFORMATIONS DES ETABS
	//Affichage et affectation des différents champs !
	//Empeche la touche entrée de valider le formulaire
	/*
	$("#newDossier").bind("keypress",
		function(e){
			if(e.keyCode == 13)
				return false;
		}
	);
	
	$("#editDossier").bind("keypress",
		function(e){
			if(e.keyCode == 13)
				return false;
		}
	);
	*/

	//JQuery UI Date picker
	$('.date').live('click', function() {
		$(this).datepicker({showOn:'focus', dateFormat: 'dd/mm/yy', firstDay: 1}).focus();
	});
	
	//Pour les heures
	$('.time').live('focus', function() {
		$(this).timeEntry($.timeEntry.regional['fr']);
	});
	
			
	$("#DATEINSERT_INPUT").mask("99/99/9999",{placeholder:" "});
	
	
	//Jquery UI Buttons
	$("#creationDossier").button({
		icons: {
			primary: 'ui-icon-circle-plus'
		},
		text: true
	});
	
	$("#addNumDoc").button({
		icons: {
			primary: 'ui-icon-circle-plus'
		},
		text: false
	});
	
	$("#modificationDossier").button({
		icons: {
			primary: 'ui-icon-pencil'
		},
		text: true
	});
	
	$("#validModification").button({
		icons: {
			primary: 'ui-icon-circle-check'
		},
		text: true
	});
	
	$(".today").button({
		icons: {
			primary: 'ui-icon-calendar'
		},
		text: false
	});
	
	$(".changeCommission").button({
		icons: {
			primary: 'ui-icon-close'
		},
		text: false
	});
	
	$(".showCalendar").button({
		icons: {
			primary: 'ui-icon-triangle-1-s'
		},
		text: false
	});
	
	$(".hideCalendar").button({
		icons: {
			primary: 'ui-icon-triangle-1-n'
		},
		text: false
	});
	
	$(".generation").button({
		icons: {
			primary: 'ui-icon-refresh'
		},
		text: true
	});

/*
	( function($) { 
		//Gestion de l'affichage du calendrier pour la selection de la date de commission
		$.fn.afficheCalendar = function() {
			
			var calendar = $('#calendar').fullCalendar( {
				header: {
					left: 'prev,next today',
					center: 'title',
					right: 'month,agendaWeek,agendaDay'
				},
				columnFormat : {
					month: 'dddd',
					week: 'dddd dd',
					day: 'dddd dd MMMM yyyy'
				},
				defaultView: 'month',
				timeFormat:'H:mm{ - H:mm}',
				axisFormat: 'H(:mm)',
				slotMinutes: 5, //Permet d'afficher un interval de 10 minutes au lieu de 30 par défaut
				selectHelper: true,
				firstDay: 1,
				weekends: false,
				monthNames : ['Janvier','F\u00e9vrier','Mars','Avril','Mai','Juin','Juillet','Ao\u00fbt','Septembre','Octobre','Novembre','D\u00e9cembre'],
				monthAbbrevs : ['Jan','Fev','Mar','Avr','Mai','Juin','Juil','Aout','Sep','Oct','Nov','Dec'],
				dayNames : ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
				dayNamesShort : ['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'],
				lazyFetching: false, //permet de ne pas réafficher les évenements en cache
				minTime: 8,
				maxTime: 19,
				editable: true,
				disableResizing: false,
				selectable: true,
				droppable: true,
				theme: true,
				aspectRatio: 2,
				events: function(start, end, callback) {
					$.ajax({
						url: '/calendrier-des-commissions/recupevenement?start='+start.getTime()+"&end="+end.getTime(),
						data: {
							format: 'json',
							idComm: $("#COMMISSION_DOSSIER").val(),
						},
						success: function( result ) {
							var events = [];
							for( var x in result["items"] ) {
								events.push({
									id: result["items"][x]["id"],
									title: result["items"][x]["title"],
									start: result["items"][x]["start"],
									end: result["items"][x]["end"],
									allDay: result["items"][x]["allDay"],
									url: result["items"][x]["url"]
								});
							}
							callback(events);
						}
					});
				},
				select: function(start, end, allDay) {
					//DANS LE CAS D'UN SELECT : PERMETTRE LA CREATION DE COMMISSION
					// Lorsque l'on selectionne une ou plusieurs dates on ouvre la boite de dialogue correspondante
					var dateSelected = new Date(start);						
					// On empêche la selection d'un samedi ou d'un dimanche
					if( dateSelected.getDay() != 6 && dateSelected.getDay() != 0) {
						// Boite de dialogue en ajoutant en callback le traitement du formulaire
						my_dialog("/calendrier-des-commissions/dialogcomm", "do=newComm&idComm="+$("#COMMISSION_DOSSIER").val()+"&dateD="+start+"&dateF="+end, function() {	
							$("#dialogComm").dialog("option", "buttons", {
								'Enregistrer dans le calendrier': function() {
									// Lors de la validation de la boite de dialogue
									$.ajax({
										data: $("#formDateComm").serialize() + "&idComm=" + $("#COMMISSION_DOSSIER").val(),
										url: "/calendrier-des-commissions/adddates",
										type:"POST",
										success: function(affichageResultat) {
											//En cas de succes il faut inserer tous les évenements dans le calendrier et la BD
											//Récupération du Json et affichage de celui-ci
											var rez = eval("("+affichageResultat+")");
											for( var i in rez ) {
												calendar.fullCalendar('renderEvent',{
													id: rez[i]['id'],
													title: rez[i]['title'],
													className: rez[i]['className'],
													start: rez[i]['start'],
													end: rez[i]['end'],
													allDay: false,
													//url: "/calendrier-des-commissions/view/id="+rez[i]['id']												
													url: "/commission/id/"+rez[i]['id']												
												});
											}
											
											$("#dialogComm").dialog('close').html('');
										}
									});
								},
								'Fermer la fenêtre d\'édition': function() {

									$("#dialogComm").dialog('close').html('');
								}
							});
						});
					}
					return false;						
				},
				eventClick: function( event, jsEvent, view ) {
					//Lorsque l'on clique sur une commission la date s'ajoute automatiquement dans l'input en dessous
					//VOIS ICI SI BESOIN DE GERER L'EDITION DES COMMISSIONS DIRECTEMENT ICI
					var hrefTab = $(this).attr('href').split('/');
					var idDateComm= hrefTab[hrefTab.length - 1];
					
					$("#ID_AFFECTATION_DOSSIER").val(idDateComm);
					//alert($("#ID_AFFECTATION_DOSSIER").val());
					var date = new Date(event.end);
					dd = date.getUTCDate();
					mm = date.getUTCMonth()+1;
					aa = date.getUTCFullYear();
					if(dd<10)
						dd='0'+dd						
					if(mm<10)
						mm='0'+mm
					
					$("#DATECOMM_INPUT").val(dd+"/"+mm+"/"+aa);
					$(".hideCalendar").click();
					
					return false;
				},
				eventDrop: function( event, dayDelta, minuteDelta, allDay, revertFunc, jsEvent, ui, view ) {
					
					//Lorsque l'on déplace un élément
					var tabDateSelect = event.end;
					tabDateSelect = tabDateSelect.toString();
					tabDateSplited = tabDateSelect.split(" ");

					if(tabDateSplited[0] == "Sun" || tabDateSplited[0] == "Sat"){
						$("#calendar").fullCalendar('refetchEvents');
					}else{
						$.ajax({
							data: "idComm="+event.id+"&debut="+event.start+"&fin="+event.end,
							url: "/calendrier-des-commissions/deplacecommissiondate",
							type:"POST",
							success: function(affichageResultat){
								//$("#calendar").html(affichageResultat);
							},
						});
					}
				},
				eventResize: function( event, jsEvent, ui, view ) {
				
					//heure de fin change uniquement fonctionne uniquement sur la vue semaine et jour. Pas sur mois
					var view = $("#calendar").fullCalendar('getView');
					var vueInfo = view.title;
					vueInfo = vueInfo.split(" ");
					if(vueInfo.length == 2){
						//permet d'empecher la modification en mode mois (Ajouter message d'indication si besoin)
						$("#calendar").fullCalendar('refetchEvents');
						return false;
					}
					$.ajax({
						data: "idComm="+event.id+"&fin="+event.end,
						url: "/calendrier-des-commissions/resizecommissiondate",
						type:"POST",
					});
				}
			});
		};
	})(jQuery);
*/
	$("#addNumDoc").live('click',function(){
		if($("#NUM_DOCURBA").val() != ''){
			$("#listeDocUrba").append("<div class='docurba' style=''><input type='hidden' name='docUrba[]' value='"+$("#NUM_DOCURBA").val()+"' id='urba_"+$("#NUM_DOCURBA").val()+"'/>"+$("#NUM_DOCURBA").val()+" <a href='' idDocurba='"+$("#selectNature").val()+"'class='suppDocUrba'>&times;</a></div>");
			$("#NUM_DOCURBA").val('');
		}
		return false;
	});
	
	$(".suppDocUrba").live('click',function(){
		$(this).parent().remove();
	});
	
	/*
	//Gestion de l'ajout de documents d'urbanisme
	$("#addNumDoc").live('click',function(){
		if($("#NUM_DOCURBA").val() != ''){
			if($("#do").val() == 'edit'){
				$.ajax({
					url: "/dossier/fonction",
					data: "do=addDocUrba&numDoc="+$("#NUM_DOCURBA").val()+"&idDossier="+$("#idDossier").val(),
					type:"POST",
					//async: false,
					beforeSend: function(){
						//$("#"+nomTab[1]).html("<img src='/images/template/load/load.gif' />");
					},
					success: function(affichageResultat){
						//alert(affichageResultat);
					},
					error: function(){
						return false;
					}
				});
			}
			
			$("#listeDocUrba").append("<div class='docurba' style=''><input type='hidden' name='docUrba[]' value='"+$("#NUM_DOCURBA").val()+"' id='urba_"+$("#NUM_DOCURBA").val()+"'/>"+$("#NUM_DOCURBA").val()+" <a href='' idDocurba='"+$("#selectNature").val()+"'class='suppDocUrba'>&times;</a></div>");
			$("#NUM_DOCURBA").val('');
		}
		return false;
	});
	
	//Gestion de la suppression de documents d'urbanisme
	$(".suppDocUrba").live('click',function(){
		$(this).parent().remove();
		if($("#do").val() == 'edit'){
			$.ajax({
				url: "/dossier/fonction",
				data: "do=deleteDocUrba&idNumDoc="+$(this).attr('idDocUrba'),
				type:"POST",
				//async: false,
				beforeSend: function(){
					//$("#"+nomTab[1]).html("<img src='/images/template/load/load.gif' />");
				},
				success: function(affichageResultat){
					//alert(affichageResultat);
				},
				error: function(){
					return false;
				}
			});
		}
		return false;
	});
	*/
	
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
	
	
	
	

