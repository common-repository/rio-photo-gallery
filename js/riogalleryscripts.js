

jQuery( document ).ready(function() {
    jQuery('#example').DataTable();
} );

jQuery( document ).ready(function() {
jQuery('.rionumbertext').bind('input propertychange', function () {
  var start = this.selectionStart,end = this.selectionEnd;
  jQuery(this).val(jQuery(this).val().replace(/[^0-9 + \- \(\)]/g, ''));
  this.setSelectionRange(start, end);
});
});
