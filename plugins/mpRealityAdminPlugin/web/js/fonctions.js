$(function() {

//gestion des tooltips
$(".sf_admin_list  a, .advance_search").tipsy({gravity: $.fn.tipsy.autoNS, live: true, fade: true});
$(".cancel_search").tipsy({gravity: 'e' , live: true, fade: true});
//$(".sf_admin_list  a").tipsy({gravity: 's', live: true, fade: true});


//fermeture des notifications
$('#container .notice, #container .error').live('click', function() {
	$(this).fadeOut(200, function() {
	$(this).hide();
	});
});

//mp: pagination et tri
if ($(".sf_admin_pagination a, .sf_admin_list thead th[class*=sf_admin_list_th_] a").length > 0) {
    $(".sf_admin_pagination a, .sf_admin_list thead th[class*=sf_admin_list_th_] a").live('click',function(event) { 
      return executeHTMLResponse('GET', this.href, {}, $('.sf_admin_list'));
    });
}

//nombre d'éléments par page
if ($('.select_max_per_page').length > 0) {
  $('.select_max_per_page').live('change', function(e) {
    var url = location.protocol + '//' + location.host + location.pathname+ '?maxPerPage=' + this.value;
    return executeHTMLResponse('GET', url, {}, $('.sf_admin_list'));
  });
}

//mp: filtres + reset
if ($('#blocFilter').length > 0) {
  //bouton filtrer de la dialogbox
  $('#blocFilter').find('input[type=submit]').live('click', function(e) {
    $('#blocFilter').dialog('close');  
    return executeHTMLResponse('POST', $('#blocFilter').find('form').attr('action'), $('#blocFilter').find('form').serialize(), $('.sf_admin_list'));
  });
  
  //lien effacer filtre de la dialogbox
  $('#blocFilter a.blocFilter_reset').live('click', function(e) {
    $('#blocFilter').dialog('close');  
    return executeHTMLResponse('GET', this.href, {}, $('.sf_admin_list'), $('#blocFilter .sf_admin_filter form'));
  });

  //mp: lien réinitialiser les filtres
  if ($('#reset').length > 0) {
    $( "#reset" ).live('click', function(e) {
      $('#blocFilter').dialog('close'); 
      return executeHTMLResponse('GET', $('#blocFilter a.blocFilter_reset').attr('href'), {}, $('.sf_admin_list'), $('#blocFilter .sf_admin_filter form'));
    });
  }
}

  //mp: search 
  if ($('.module_search').length > 0) { 
    $('#button_module_search').live('click', function() {
      var textSearch = $('#module_search_input').val();
      if ( textSearch != "") {
        return executeHTMLResponse('GET',$(this).parent().attr('action'), {search: textSearch}, $('.sf_admin_list'), $('.module_search'));      
      }    
    });
  }

  //mp: toggle booleans
  $("#sf_admin_container").find('td.sf_admin_boolean a').live('click', function() {
    $(this).toggleClass('bool_tick bool_cross');
    $.ajax({
          url:      $("#sf_admin_container").find('tbody').metadata().toggle_url,
          data:     {
            pk:     $(this).parent().parent().metadata().pk,
			field:  $(this).metadata().field
          },
          success:  function(data) {
            $(this).toggleClass('bool_tick', '1' == data).toggleClass('bool_cross', '0' == data);
          }
        });
  });  
});

  function showLoading()
  {
    $.blockUI({
      message: '<h1><img src="/mpRealityAdminPlugin/images/busy.gif" /> Chargement en cours...</h1>',
	  css: {
      border: 'none',
      padding: '8px',
      backgroundColor: '#FFF',
      '-webkit-border-radius': '10px',
      '-moz-border-radius': '10px',
      opacity: .9,
      color: '#000'
     }
 
    });
  }

  function hideLoading()
  {
    $(document).ajaxStop($.unblockUI);
  }
  
  function executeJSONResponse(type, url, data, jeton) {
  $.ajax({
    type:type,
    dataType: 'json',
    url: url,
    data:data,
    beforeSend:function() {showLoading();},
    success: function(response)
      {
	    if (response.type == 'notice') 
		{
		   if (jeton == undefined || jeton.length == 0) window.location.href = response.redirectToUrl; //provient de la page edit
		   
			$.ajax({
			  url: response.redirectToUrl,
			  data: {},
			  beforeSend:function() { },
			  success: function(response)
			  {
				$('.sf_admin_list').html(response);	   
			  }
			});
			hideLoading();
		}	 
      },
    error:function(XMLHttpRequest, textStatus, errorThrown) { console.log(textStatus); console.log(errorThrown); console.log(XMLHttpRequest);}
  });
  return false;
}


function executeHTMLResponse(type, url, data, liste, filtre) {
  $.ajax({
    type:type,
    url: url,
    data:data,
    beforeSend:function() {showLoading()},
    success:function(response, textStatus) {
      var r = response.split('#__filter__#');     
      if (r.length > 1 && filtre != undefined && filtre.length > 0) {
        filtre.is("form") ? filtre[0].reset() : filtre.html(r[1]);        
        //filtre[0].reset();
      }
      
      liste.html(r[0]);
	    $('input').customInput();		
      $('select#dropdownStyle').selectmenu({
        style:'dropdown',
        width:'200px'
    });
	    hideLoading();
    },    
	error: function(XMLHttpRequest, textStatus, errorThrown)
      {
        if (XMLHttpRequest.status == '401')
        {
          hideLoading();
          location.reload(); //forcer redirection vers page login
        }
      }
  });
  return false;
}

