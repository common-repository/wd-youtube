jQuery("document").ready(function () {
  elementor.hooks.addAction('panel/open_editor/widget/ytwd-elementor', function (panel, model, view) {
    var youtube_embed_obj = jQuery('select[data-setting="ytwd_id"]', window.parent.document);
    ytwd_edit_youtube_embed_link(youtube_embed_obj);
  });
  jQuery('body').on('change', 'select[data-setting="ytwd_id"]', window.parent.document, function () {
    ytwd_edit_youtube_embed_link(jQuery(this));
  });  
});

function ytwd_edit_youtube_embed_link(el) {
  var id = el.val();
  var link = el.closest('.elementor-control-content').find('.elementor-control-field-description').find('a');
  var href = 'admin.php?page=youtube_ytwd';
  if ( id != null && id !== '0') {
    href = 'admin.php?page=youtube_ytwd&task=edit&id=' + id;
  }
  link.attr('href', href);
}