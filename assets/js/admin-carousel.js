(function ($) {
  $(function () {
    var $input = $('#feicoop_banner_carousel_ids');
    var $preview = $('#feicoop_banner_carousel_preview');
    var frame = null;

    function renderPreview(attachments) {
      $preview.empty();

      attachments.forEach(function (attachment) {
        var thumb = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
        $('<li>', {
          'data-id': attachment.id
        }).append($('<img>', {
          src: thumb,
          alt: attachment.alt || attachment.title || ''
        })).appendTo($preview);
      });
    }

    function getSelectedIds() {
      return $input.val().split(',').map(function (value) {
        return parseInt(value, 10);
      }).filter(function (value) {
        return !Number.isNaN(value) && value > 0;
      });
    }

    function preselectAttachments(selection) {
      var ids = getSelectedIds();

      if (!ids.length) {
        selection.reset();
        return;
      }

      selection.reset();

      ids.forEach(function (id) {
        var attachment = wp.media.attachment(id);

        attachment.fetch().always(function () {
          selection.add(attachment);
        });
      });
    }

    $('#feicoop_banner_carousel_select').on('click', function (event) {
      event.preventDefault();

      if (!frame) {
        frame = wp.media({
          title: 'Selecionar imagens do carrossel',
          button: {
            text: 'Usar imagens'
          },
          multiple: true
        });

        frame.on('open', function () {
          preselectAttachments(frame.state().get('selection'));
        });

        frame.on('select', function () {
          var attachments = frame.state().get('selection').toJSON();
          $input.val(attachments.map(function (attachment) {
            return attachment.id;
          }).join(','));
          renderPreview(attachments);
        });
      }

      frame.open();
    });

    $('#feicoop_banner_carousel_clear').on('click', function (event) {
      event.preventDefault();
      $input.val('');
      $preview.empty();
    });
  });
})(jQuery);
