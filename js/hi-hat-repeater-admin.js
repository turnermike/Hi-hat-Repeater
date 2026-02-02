(function ($) {
  function updateGroupRowOrder($repeater) {
    $repeater.find('tbody tr.acf-row').not('.acf-clone').each(function (index) {
      $(this).find('.acf-row-handle.order span').text(index + 1);
    });
  }

  function updateGroupRowCount($repeater) {
    var $countInput = $repeater.find('> .acf-input > input[type="hidden"]').first();
    if (!$countInput.length) {
      return;
    }

    var rowCount = $repeater.find('tbody tr.acf-row').not('.acf-clone').length;
    $countInput.val(rowCount);
  }

  function addGroupRow($repeater) {
    var $tbody = $repeater.find('tbody');
    var template = $repeater.find('script.acf-clone').html();

    if (!template) {
      return;
    }

    var index = $tbody.find('tr.acf-row').not('.acf-clone').length;
    var html = template.replace(/acfcloneindex/g, index);
    var $row = $(html);

    $tbody.append($row);
    $repeater.removeClass('-empty');
    updateGroupRowOrder($repeater);
    updateGroupRowCount($repeater);

    if (window.acf && typeof acf.doAction === 'function') {
      acf.doAction('append', $row);
    }

    if (window.acf && typeof acf.getFields === 'function') {
      $row.find('.acf-field').each(function () {
        acf.getField($(this));
      });
    }
  }

  function resetRepeaterItem($item) {
    $item.find('textarea').val('');
    $item.find('input[type="hidden"]').val('');
    $item.find('[id]').removeAttr('id');
    $item.find('.hi-hat-repeater-image-preview').hide().find('img').remove();
    $item.find('.hi-hat-repeater-image-remove-button').hide();
    $item.find('.hi-hat-repeater-image-select-button').text('Select Image');
  }

  function getMediaFrame() {
    return wp.media({
      title: 'Select Image',
      button: { text: 'Use image' },
      multiple: false,
    });
  }

  $(document).on('click', '.acf-repeater [data-event="add-row"]', function (e) {
    e.preventDefault();
    var $repeater = $(this).closest('.acf-repeater');
    addGroupRow($repeater);
  });

  $(document).on('click', '.acf-repeater [data-event="remove-row"]', function (e) {
    e.preventDefault();
    var $repeater = $(this).closest('.acf-repeater');
    var $row = $(this).closest('tr');
    var $tbody = $repeater.find('tbody');

    $row.remove();

    if ($tbody.find('tr.acf-row').not('.acf-clone').length === 0) {
      $repeater.addClass('-empty');
      addGroupRow($repeater);
    }

    updateGroupRowOrder($repeater);
    updateGroupRowCount($repeater);
  });

  $(document).on('click', '.acf-hi-hat-repeater .hi-hat-repeater-add-button', function (e) {
    e.preventDefault();
    var $repeater = $(this).closest('.acf-hi-hat-repeater');
    var $itemsWrap = $repeater.find('.hi-hat-repeater-items-wrap');
    var $lastItem = $itemsWrap.find('.hi-hat-repeater-item').last();
    var $newItem = $lastItem.clone(false, false);
    resetRepeaterItem($newItem);
    $itemsWrap.append($newItem);
  });

  $(document).on('click', '.acf-hi-hat-repeater .hi-hat-repeater-remove-button', function (e) {
    e.preventDefault();
    var $repeater = $(this).closest('.acf-hi-hat-repeater');
    var $items = $repeater.find('.hi-hat-repeater-item');
    var $item = $(this).closest('.hi-hat-repeater-item');

    if ($items.length <= 1) {
      resetRepeaterItem($item);
      return;
    }

    $item.remove();
  });

  $(document).on('click', '.acf-hi-hat-repeater .hi-hat-repeater-image-select-button', function (e) {
    e.preventDefault();

    if (!window.wp || !wp.media) {
      return;
    }

    var $item = $(this).closest('.hi-hat-repeater-item');
    var $input = $item.find('.hi-hat-repeater-image-input');
    var $preview = $item.find('.hi-hat-repeater-image-preview');
    var $removeButton = $item.find('.hi-hat-repeater-image-remove-button');
    var $selectButton = $(this);

    var frame = getMediaFrame();
    frame.on('select', function () {
      var attachment = frame.state().get('selection').first().toJSON();
      if (!attachment || !attachment.id) {
        return;
      }

      $input.val(attachment.id);
      var previewUrl = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
      $preview.html('<img src="' + previewUrl + '" alt="" style="max-width: 150px; height: auto; display: block; margin-bottom: 10px;" />');
      $preview.show();
      $removeButton.show();
      $selectButton.text('Change Image');
    });

    frame.open();
  });

  $(document).on('click', '.acf-hi-hat-repeater .hi-hat-repeater-image-remove-button', function (e) {
    e.preventDefault();
    var $item = $(this).closest('.hi-hat-repeater-item');
    var $input = $item.find('.hi-hat-repeater-image-input');
    var $preview = $item.find('.hi-hat-repeater-image-preview');
    var $selectButton = $item.find('.hi-hat-repeater-image-select-button');

    $input.val('');
    $preview.hide().find('img').remove();
    $(this).hide();
    $selectButton.text('Select Image');
  });
})(jQuery);
