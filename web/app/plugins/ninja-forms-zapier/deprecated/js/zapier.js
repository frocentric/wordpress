/* JS that allows inline editing of Zaps*/
jQuery(document).ready(function($){

  // Sync form data with Zapier
  $( document ).on( 'click', '#ninja_forms_zapier_sync button', function(e) {

    e.preventDefault();

    $.ajax({
      type: "POST",
      url: ajaxurl,
      data: {
        'action': 'ninja-forms-zapier-sync',
        'form_id': $(this).attr('data-form-id')
      },
      dataType: 'json'
    }).done(function(response) {
      if ( response.success == true ) {
        $("#ninja_forms_zapier_sync").hide();
      }
    });
  });

  //---------------------------------------------------------------------------

  // Add New
  $('.zap-metabox-1 .add-new-h2').on('click', function() {
    // Show / Hide
    $('.zap-metabox-1').each(function(index) {
      $(this).hide();
    });
    $('.zap-metabox-2').show();

    // Reset
    $('.zap-metabox-2 #zap_id').val('');
    $('.zap-metabox-2 #zap_name').val('');
    $('.zap-metabox-2 #zap_webhook_url').val('');
    $('.zap-metabox-2 #zap_status').prop('checked', true);

    // Prevent default action
    return false;
  });

  //---------------------------------------------------------------------------

  // Cancel
  $("#zap_cancel").on('click', function(){
    $('.zap-metabox-2').hide();
    $('.zap-metabox-1').each(function(index) {
      $(this).show();
    });
  });

  //---------------------------------------------------------------------------

  // Save
  $('#zap_submit').on('click', function()
  {
    // Ajax
    $.ajax({
      type: "POST",
      url: ajaxurl,
      data: {
        'action': 'ninja-forms-zapier-save',
        'raw': $("#ninja_forms_admin").serialize()
      },
      dataType: 'json'
    }).done(function(response) {

      // Update html
      if (response.success == true) {
        $('.zap-list').html(response.table);
        $('.zap-metabox-0').html(response.metabox);
      }

      // Show / hide
      $('.zap-metabox-2').hide();
      $('.zap-metabox-1').each(function(index) {
        $(this).show();
      });
    });

    return false;
  });

  //---------------------------------------------------------------------------

  // Delete
  $(document).on('click', '.zap-list-table .trash a', function(){

    $.ajax({
      type: "POST",
      url: $(this).attr('href'),
      data: {
        'raw': $("#ninja_forms_admin").serialize()
      },
      dataType: 'json'
    }).done(function(response) {
      // Update html
      if (response.success == true) {
        $('.zap-list').html(response.table);
        $('.zap-metabox-0').html(response.metabox);
      }
    });

    return false;
  });

  //---------------------------------------------------------------------------

  // Edit
  $(document).on('click', '.zap-list-table .edit a', function(){

    $.ajax({
      type: "POST",
      url: $(this).attr('href'),
      data: {
        'raw': $("#ninja_forms_admin").serialize()
      },
      dataType: 'json'
    }).done(function(response) {
      if (response.success == true) {
        // Set data
        $('.zap-metabox-2 #zap_id').val(response.zap_id);
        $('.zap-metabox-2 #zap_name').val(response.zap_name);
        $('.zap-metabox-2 #zap_webhook_url').val(response.zap_webhook_url);
        if (response.zap_status == "true") {
          $('.zap-metabox-2 #zap_status').prop('checked', true);
        } else {
          $('.zap-metabox-2 #zap_status').prop('checked', false);
        }

        // Show / hide
        $('.zap-metabox-2').show();
        $('.zap-metabox-1').each(function(index) {
          $(this).hide();
        });
      }
    });

    // Prevent default action
    return false;
  });

});
