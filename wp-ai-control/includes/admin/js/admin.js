// WP AI Control Admin JS
jQuery(document).ready(function($) {
  // Copy API key to clipboard
  $('.copy-api-key').on('click', function(e) {
    e.preventDefault();
    var key = $(this).data('key');
    var tempInput = $('<input>');
    $('body').append(tempInput);
    tempInput.val(key).select();
    document.execCommand('copy');
    tempInput.remove();
    alert('API key copied to clipboard!');
  });
});
