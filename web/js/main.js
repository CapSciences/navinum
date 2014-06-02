$(document).ready(function(){

  $('#synchro_manuelle').click(function(){
		$("#synchro_result").html(''); //Clear the Packet list
		$.post('/default/sync',
		function(data, status) /* On Request Complete */
		{
		$('#synchro_result').html(data); // put all the data in there
		$("#state").html(status); // update status
			alert("stream closed");
		},
		function(packet,status,fulldata, xhr) /* If the third argument is a function it is used as the OnDataRecieved callback */
		{
		$("#len").html(fulldata.length); // total how much was recieved so far
		$("#state").html(status); // status (can be any ajax state or "stream"
		var data = $("#synchro_result").html(); // get text of what we received so far
		data += packet; // append the last packet we got
		$("#synchro_result").html(data); // update the div
		}
		);
  })
  
  $('#sf_admin_container h1').appendTo('div.content-box-header');
  
  $('a.blocFilter_reset').each(function(){
	  this.href = this.href.replace('action?', '?');
  })
  
});

//date picker http://jqueryui.com/demos/datepicker
if(typeof $.widget != 'undefined'){
(function($) {
    $.widget('ui.majaxdateselector', {
            version: '1.0.0',
            eventPrefix: 'majax.dateselector',
            options: {
                    datepicker_opts: {
    					dayNamesMin: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'],
    					firstDay: 1,
    					gotoCurrent: true,
    					monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Décembre']
                    }
            },
            _create: function() {
                    this.options['id'] = $(this.element).attr('id');
                    this._hide_real_ctrls();
                    this._build_facade();
                    return this;
            },
            _build_facade: function() {
                    $(this.element).html('<input size="10" type="text" class="date_display" id="'+this.options['id']+'_display" autocomplete="off" />');
                    var tfDisplayUpdate = function(widget) {
                            return function() {
                            	    if(this.value=='')
                            	    	widget._clear_display();
                            	    else	
                            	    	widget._update_ctrls(this.value);
                            }
                    }

                    $('#'+this.options['id']+'_display').change(tfDisplayUpdate(this));
                    	
                    var m, d, y;
                    m = $('#'+this.options['id']+'_month').val();
                    d = $('#'+this.options['id']+'_day').val();
                    y = $('#'+this.options['id']+'_year').val();
                    $('#'+this.options['id']+'_display').datepicker(this.options['datepicker_opts']);
                    $('#'+this.options['id']+'_display').datepicker('option', 'defaultDate', m+'/'+d+'/'+ y.substr(2,4));
                    if (parseInt(m) > 0 && parseInt(d) > 0 && parseInt(y) > 0)
                    {
                    	$('#'+this.options['id']+'_display').val(this._zero_pad(d, 2)+'/'+this._zero_pad(m, 2)+'/'+ y);
                    	//$('#'+this.options['id']+'_display').val(this._zero_pad(m, 2)+'/'+this._zero_pad(d, 2)+'/'+ y);
                    }
          
            },
            _zero_pad: function(num,count)
            {
                    var numZeropad = num + '';
                    while(numZeropad.length < count) {
                            numZeropad = "0" + numZeropad;
                    }
                    return numZeropad;
            },
            _clear_display: function() {
                    $('#'+this.options['id']+'_display').val('');
                    $('#'+this.options['id']+'_month').val('');
                    $('#'+this.options['id']+'_day').val('');
                    $('#'+this.options['id']+'_year').val('');
            },
            _update_ctrls: function(val) {
                    var vals = val.split('/');
                    if ((val == '' || vals.length != 3) && this.options['can_be_empty'])
                    {
                            $('#'+this.options['id']+'_month').val('');
                            $('#'+this.options['id']+'_day').val('');
                            $('#'+this.options['id']+'_year').val('');
                    }

                    var m, d, y;
                    m = vals[0];
                    d = vals[1];
                    y = vals[2];

                    if (parseInt(m) > 0 && parseInt(d) > 0 && parseInt(y) > 0)
                    {
                            $('#'+this.options['id']+'_month').val(parseInt(m));
                            $('#'+this.options['id']+'_day').val(parseInt(d));
                            $('#'+this.options['id']+'_year').val(parseInt(y));
                    }
                    $('#'+this.options['id']+'_display').val(d + '/' + m + '/' + y);
            },
            _hide_real_ctrls: function() {
                    $('#'+this.options['id']+'_ctrls').css('display', 'none');
            },
            _show_real_ctrls: function() {
                    $('#'+this.options['id']+'_ctrls').css('display', null);
            },
            destroy: function() {
                    this._show_real_ctrls();
                    ('#'+this.options['id']).html('');
                    $.Widget.prototype.destroy.call(this);
                    return this;
            }
    });
})(jQuery);
}
