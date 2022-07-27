// JavaScript Document

    function GetMessageStep(dati, rec_init, step) {
		var pos=dati.indexOf("--");
		if (pos>=0) {
			var idn=dati.substring(0,pos).valueOf();
			var mess=dati.substr(pos+2);
		} else {
			var idn=dati.substr(0).valueOf();
			var mess='';
		}
        if (idn>=1) {
            jQuery("#risultato #rec_step").append("<b> OK !</b> - "+idn+";");
			if (mess!='') {
            	jQuery("#risultato").append("<p id='err_step-'"+rec_init+" class='notice notice-warning'>Record dal/Step "+rec_init+" - Presenti Errori: "+mess+"</p>").show();
			}
        } else if (idn==0) {
            jQuery("#risultato").append("<p id='err_step-'"+rec_init+" class='notice notice-error'>Record dal/Step "+rec_init+": non elaborati/o! - "+mess+"</p>");
        } else {
            jQuery("#risultato").append("<p id='err_step-'"+rec_init+" class='notice notice-warning'>Record dal/Step "+rec_init+" - Errore: "+mess+"</p>").show();
        }
		StepBar(rec_init+step);
        return false;
    }
    
    function GetMessageInit(rec_init,step) {
		if (typeof(step)=='string') {
			jQuery("#risultato #rec_step").text("Elaborazione Step "+rec_init+" - "+step+" : ");
		} else {
			var rec_step=rec_init+step;
			jQuery("#risultato #rec_step").text("Elaborazione record da "+rec_init+" a "+rec_step+" : ");
		}
    }
    
    function GetMessageFine(total_rec) {
		jQuery("#risultato #end_step").text("Elaborati n. "+total_rec+" record");
		StepBar(total_rec);
    }
    
    function GetMessageStart(total_rec,step) {
		HideBar();
		StripStep();
		var res=document.getElementById("risultato");
		if (! res) {
			SetMessageArea(jQuery(".wrap"));
		}
		jQuery("#risultato").empty();
		jQuery("#risultato").append("<p id='ini_step'> Inizio Elaborazione di n. "+total_rec+" record - Step da "+step+" record.</p>");
		jQuery("#risultato").append("<p id='rec_step'></p>");
		jQuery("#risultato").append("<p id='end_step'></p>");
		ShowBar();
		InitBar(total_rec);
    }
	
	function SetMessageArea(parent) {
		var bar=document.getElementById("progbar");
		var res=document.getElementById("risultato");
		if (! bar) {
			parent.append('<div id="progbar" style="width:95%; display:none;"><div class="progress-label">...</div></div>');
		}
		if (! res) {
			parent.append('<div id="risultato"></div>');
		}
	}
	
	function StripStep() {
		jQuery( "#risultato #rec_step" ).remove();
	}
	
	function HideBar() {
		jQuery( "#progbar" ).hide();
	}
	
	function ShowBar() {
		jQuery( "#progbar" ).show();
	}
	
	function BuildSteps(r_init, step, r_tot){
		var steps = [];
		for (r_init; r_init < r_tot; r_init+=step) {
                steps.push({
                    rec_init: r_init,
                    step: step,
                    total_rec: r_tot
					});
		}
		return steps;
	}
	
	function InitBar(maxval) {
		jQuery( "#progbar" ).progressbar({
			value: false,
			max: maxval,
			change: function() {
				var nv=jQuery( "#progbar" ).progressbar( "value" )/jQuery( "#progbar" ).progressbar( "option","max" )*100;
				jQuery( ".progress-label" ).text( nv.toFixed() + "%" );
				},
			complete: function() {
				jQuery( ".progress-label" ).text( "Complete!" );
				}
	  		});
	}
	
	function StepBar(stepval) {
		jQuery( "#progbar" ).progressbar("value",stepval);
	}
	
	function execStep(rec_init,step,action) {
		var text, act;
		switch(action) {
			case "prod":
				text = 'includes/ajax/woowow-prodotti.php';
				act='dbimport_product';
				break;
			case "attr1":
				text = 'includes/ajax/woowow-attr-genere.php';
				act='dbimport_attr1';
				break;
			case "attr2":
				text = 'includes/ajax/woowow-attr-autore.php';
				act='dbimport_attr2';
				break;
			case "attr3":
				text = 'includes/ajax/woowow-attr-editore.php';
				act='dbimport_attr3';
				break;
			case "attr4":
				text = 'includes/ajax/woowow-attr-collana.php';
				act='dbimport_attr4';
				break;
			case "cust":
				text = 'includes/ajax/woowow-clienti.php';
				act='dbimport_customer';
				break;
			default:
				text = "";
				act = "";
				return false;
		}
		console.log(act);
		return jQuery.ajax({
					method:'GET',					
//					url: document.WOW_MANAGER_IMPORT_URI+text,
					url: ajaxurl,
					beforeSend: function( xhr ) { 
						GetMessageInit(rec_init,step ); 
						return true; 
						},
					data: {
						action: act,
						rec_init: rec_init,
						step: step 
						},
					success: function(dati) {
						GetMessageStep(dati,rec_init,step);
						}
					});
	}
	
	function msgImport(passo) {
		var m;
		switch(passo) {
			case 0:
				m = 'Normalizzazione Campo TITOLO';
				break;
			case 1:
				m = 'Normalizzazione Campo TITOLO';
				break;
			case 2:
				m = 'Normalizzazione Campo AUTORE';
				break;
			case 3:
				m = 'Normalizzazione Campo EDITORE';
				break;
			case 4:
				m = 'Normalizzazione Campo SETTORE';
				break;
			case 5:
				m = 'Normalizzazione Campo COLLANA';
				break;
			case 6:
				m = 'Normalizzazione Campo CODICE';
				break;
			case 7:
				m = 'Normalizzazione Campo Prezzo';
				break;
			case 8:
				m = 'Normalizzazione Campo Data_Pubb';
				break;
			case 9:
				m = 'Normalizzazione Copia tabella in titoli_disp';
				break;
			case 10:
				m = 'Normalizzazione Campo NOVITA';
				break;
			case 'dati':
				m = 'Creazione/Aggiornamento Prodotti in Woocommerce';
				break;
			default:
				m = "Non Valido !";
		}
		return m;
	}
	
	function execImport(rec_init,step,action, dataS) {
		var mess;
		switch(action) {
			case "file":
			dataS.passo=rec_init;
			dataS.step=step;
			dataS.action='wowfile_upload_import_step';
//				text = 'includes/file_import/woowow-import-step.php?passo='+rec_init+'&step='+step;
				mess=msgImport(rec_init);
				break;
			case "dati":
			dataS.rec_init=rec_init;
			dataS.step=step;
			dataS.action='wowfile_upload_import_product';
//				text = 'includes/file_import/woowow-import-product.php?rec_init='+rec_init+'&step='+step;
				mess=msgImport(action);
				break;
			default:
//				text = "";
				mess=msgImport(rec_init);
				return false;
		}
		return jQuery.ajax({
					method:'POST',					
//					url: document.WOW_MANAGER_IMPORT_URI+text,
					url: ajaxurl,
					beforeSend: function( xhr ) { 
						GetMessageInit(rec_init,mess ); 
						return true; 
						},
					data: dataS,
					success: function(dati,s,x) {
						var d = dati.valueOf();
						if (d>0) {
							GetMessageStep(dati,rec_init,step);
						} else {
							GetMessageStep(dati,rec_init,"Operazione non eseguita !");
							return false;
						}
						}
					});
	}

jQuery(function($) {

    var progressbar = jQuery( "#progbar" ),
      progressLabel = jQuery( ".progress-label" );
	$.datepicker.regional['it'] = {
		closeText: 'Chiudi',
		prevText: '&#x3c;Prec',
		nextText: 'Succ&#x3e;',
		currentText: 'Oggi',
		monthNames: ['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno',
			'Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'],
		monthNamesShort: ['Gen','Feb','Mar','Apr','Mag','Giu',
			'Lug','Ago','Set','Ott','Nov','Dic'],
		dayNames: ['Domenica','Luned&#236','Marted&#236','Mercoled&#236','Gioved&#236','Venerd&#236','Sabato'],
		dayNamesShort: ['Dom','Lun','Mar','Mer','Gio','Ven','Sab'],
		dayNamesMin: ['Do','Lu','Ma','Me','Gi','Ve','Sa'],
		weekHeader: 'Sm',
		dateFormat: 'dd/mm/yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};

	jQuery(document).on('click','button[id=bt_importa]',function(event) {
		// Stop form from submitting normally
		event.preventDefault();
		var act=jQuery( "input:radio[name=dtype]:checked" ).val();
		var prod=jQuery( "#cprod" ).val();
		if (act) {
//			var dataString = jQuery("#form_mainpage").serialize();
			var dataString={
				action: 'dbimport_actions',
				dtype: act,
				cprod: prod
			};
			if (confirm("Confermi l'operazione?")){
				jQuery("#risultato").empty();
				progressLabel.text('Starting...');
				jQuery.post(ajaxurl,dataString, function(dati) { 
						if (dati!=0) {
							jQuery("#risultato").append(dati);
						} else {
							jQuery("#risultato").append(dati);
						}
				}, "html")
				.error( function(err) {
						jQuery("#risultato").append(err);
				});
	//			jQuery.unblockUI();
			}
		} else {
			alert("Selezionare un'opzione di importazione !");
		}
		return false;
	});
	
});
