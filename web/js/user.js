  $(document).ready(function(){

  if ($('input[name="sf_guard_user[permissions]"]:checked').val() == 1 || !$('input[name="sf_guard_user[permissions]"]:checked').val()) {
    $('.sf_admin_form_field_exposition_list').hide();
  }

  $('input[name="sf_guard_user[permissions]"]').change(function(){
    if ($(this).val() == 1) {
      $('.sf_admin_form_field_exposition_list').hide();
    }
    else {
      $('.sf_admin_form_field_exposition_list').show();
    }
  });
});

