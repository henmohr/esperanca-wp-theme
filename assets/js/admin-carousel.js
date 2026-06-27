(function ($) {
  $(function () {
    var $input = $('#feicoop_banner_carousel_ids');
    var $preview = $('#feicoop_banner_carousel_preview');
    var frame = null;

    function syncInputFromPreview() {
      var ids = [];

      $preview.find('li[data-id]').each(function () {
        var id = parseInt(this.getAttribute('data-id'), 10);

        if (!Number.isNaN(id) && id > 0) {
          ids.push(id);
        }
      });

      $input.val(ids.join(','));
    }

    function renderPreview(attachments) {
      $preview.empty();

      attachments.forEach(function (attachment) {
        var thumb = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
        var $item = $('<li>', {
          'data-id': attachment.id
        });

        $item.append($('<img>', {
          src: thumb,
          alt: attachment.alt || attachment.title || ''
        }));

        $item.append($('<button>', {
          type: 'button',
          class: 'button-link-delete feicoop-banner-carousel-remove',
          text: 'Remover',
          'data-remove-item': '1'
        }));

        $item.appendTo($preview);
      });

      syncInputFromPreview();
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
          title: 'Enviar ou selecionar imagens dos patrocinadores',
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

    $(document).on('click', '#feicoop_banner_carousel_preview [data-remove-item]', function (event) {
      event.preventDefault();
      event.stopPropagation();
      $(this).closest('li').remove();
      syncInputFromPreview();
    });
  });
})(jQuery);
