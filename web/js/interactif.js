$(document).ready(function(){
	var v1 = $('input[name="interactif[url_interactif_type]"]:checked').val();
	
	$('input[name="interactif[url_interactif_type]"]').click(function(){
		if($(this).val() != 0)
			$('div.sf_admin_form_field_url_interactif_choice').hide();
		else
			$('div.sf_admin_form_field_url_interactif_choice').show();
		
	});
	
	$('input[name="interactif[is_logvisite_needed]"], input[name="interactif[is_logvisite_verbose_needed]"]').click(function(){
		
		if(!$('input[name="interactif[is_logvisite_needed]"]:checked').val() && !$('input[name="interactif[is_logvisite_verbose_needed]"]:checked').val()){
			$('div.sf_admin_form_field_url_interactif_type, div.sf_admin_form_field_url_interactif_choice, div.sf_admin_form_field_url_visiteur_type, div.sf_admin_form_field_url_start_at, div.sf_admin_form_field_url_start_at_type, div.sf_admin_form_field_url_end_at, div.sf_admin_form_field_url_end_at_type').hide();
		}else{
			$('div.sf_admin_form_field_url_interactif_type, div.sf_admin_form_field_url_interactif_choice, div.sf_admin_form_field_url_visiteur_type, div.sf_admin_form_field_url_start_at, div.sf_admin_form_field_url_start_at_type, div.sf_admin_form_field_url_end_at, div.sf_admin_form_field_url_end_at_type').show();
		
			if($('input[name="interactif[url_interactif_type]"]:checked').val() != 0){
				$('div.sf_admin_form_field_url_interactif_choice').hide();
			}else{
				$('div.sf_admin_form_field_url_interactif_choice').show();
			}
		}
		
	});
	
	var c1 = $('input[name="interactif[is_logvisite_needed]"]:checked').val();
	var c2 = $('input[name="interactif[is_logvisite_verbose_needed]"]:checked').val();
	if($('input[name="interactif[url_interactif_type]"]:checked').val() != 0){
		$('div.sf_admin_form_field_url_interactif_choice').hide();
	}else{
		$('div.sf_admin_form_field_url_interactif_choice').show();
	}
	if(!c1 && !c2){
		$('div.sf_admin_form_field_url_interactif_type, div.sf_admin_form_field_url_interactif_choice, div.sf_admin_form_field_url_visiteur_type, div.sf_admin_form_field_url_start_at, div.sf_admin_form_field_url_start_at_type, div.sf_admin_form_field_url_end_at, div.sf_admin_form_field_url_end_at_type').hide();
	}

    function getSourceTypeField(){
        if($('#interactif_source_type').val() == 'natif'){
            $('.sf_admin_form_field_synopsis, .sf_admin_form_field_markets, .sf_admin_form_field_url_market_ios, .sf_admin_form_field_url_market_android, .sf_admin_form_field_url_market_windows, .sf_admin_form_field_url_scheme').show();
            $('.sf_admin_form_field_file, .sf_admin_form_field_refresh_deploiement, .sf_admin_form_field_url_fichier_interactif').hide();
        }else{
            $('.sf_admin_form_field_synopsis, .sf_admin_form_field_markets, .sf_admin_form_field_url_market_ios, .sf_admin_form_field_url_market_android, .sf_admin_form_field_url_market_windows, .sf_admin_form_field_url_scheme').hide();
            $('.sf_admin_form_field_file, .sf_admin_form_field_refresh_deploiement, .sf_admin_form_field_url_fichier_interactif').show();
        }
    }

    getSourceTypeField();
    $('#interactif_source_type').click(getSourceTypeField);

	
	
});