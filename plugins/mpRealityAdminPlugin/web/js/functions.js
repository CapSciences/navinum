/* Toggle (slide up slide down) */

//$(function() {
  //  $('.content-box-header').click(function(){
    //    $('.sf_admin_table_content').toggle();

    //});
//});

/*Modal box */
$.fx.speeds._default = 1000;
$(function() {
    $( "#blocFilter" ).dialog({
        width: 480,
        autoOpen: false
    });

    $( ".advance_search" ).live('click', function() {
        $( "#blocFilter" ).dialog( "open" );
        return false;
    });
});

//custom select menu
$(function()
{
    $('input').customInput();
    $('select#dropdownStyle').selectmenu({
        style:'dropdown',
        width:'200px'
    });
});

// mouseover et click sur une ligne du tableau
$(function()
{
/*
$('.sf_admin_list table tbody tr').live('mouseover mouseout click', function(event) {
  if (event.type == 'mouseover') {
	$(this).addClass('ui-state-hover')
  }
  else if (event.type == 'mouseout') {
            $(this).removeClass('ui-state-hover');
  } 
  else {

			// modifie la couleur de la ligne
        $(this).toggleClass('ui-state-highlight');
        // modifie le statut du checkbox
        var chx = $(this).find('input:checkbox');
        $(chx).attr('checked', $(this).hasClass('ui-state-highlight'));
        $(chx).trigger("updateState");         
  }

});*/

/*
$('.sf_admin_list table tbody tr').live({
        mouseover:
           function() 
		   {
				$(this).addClass('ui-state-hover');
		   },
        function() {
            $(this).removeClass('ui-state-hover');
        },
        click:
           function()
           {
			// modifie la couleur de la ligne
        $(this).toggleClass('ui-state-highlight');
        // modifie le statut du checkbox
        var chx = $(this).find('input:checkbox');
        $(chx).attr('checked', $(this).hasClass('ui-state-highlight'));
        $(chx).trigger("updateState");
           }
       });

   /* $('.sf_admin_list table tbody tr').live(
    'hover',
        function() {
            $(this).addClass('ui-state-hover');
        },
        function() {
            $(this).removeClass('ui-state-hover');
        }
        )
    .click(function(e) {
        // modifie la couleur de la ligne
        $(this).toggleClass('ui-state-highlight');
        // modifie le statut du checkbox
        var chx = $(this).find('input:checkbox');
        $(chx).attr('checked', $(this).hasClass('ui-state-highlight'));
        $(chx).trigger("updateState");
    });*/
})
  
/*Navigation*/

$(function()
{
    $("#navigation li.current").children("ul").css("left", "0px").show();
    $("#navigation li.current").children(":first-child").css("color", "#202020");

    $("#navigation li").hover(function(){
        if(this.className.indexOf("current") == -1)  {
            getCurrent = $(this).parent().children("li.current:eq(0)");
          
            if (getCurrent = 1 ) {
                $(this).parent().children("li.current:eq(0)").children("ul").hide();
            }
            $(this).children("ul:eq(0)").css("left", "0px").show();
        }
    },function(){
        if(this.className.indexOf("current") == -1)  {
            getCurrent = $(this).parent().children("li.current:eq(0)");
          
            if (getCurrent = 1 ) {
                $(this).parent().children("li.current:eq(0)").children("ul").show();
            }
            $(this).children("ul:eq(0)").css("left", "-99999px")
            .hide();
        }
    });
});





