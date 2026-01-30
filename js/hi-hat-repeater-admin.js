(function ($) {
  // Initialize all WYSIWYG editors in the repeater
  function initEditors() {
    var editorSettings = window.tinyMCEPreInit || {};
    var mceInit = editorSettings.mceInit || {};
    var qtInit = editorSettings.qtInit || {};

    // Find all textarea elements in the repeater
    $('.acf-hi-hat-repeater textarea').each(function () {
      var $textarea = $(this);
      var editorId = $textarea.attr('id');

      if (!editorId) return;

      // Create TinyMCE settings for this editor if they don't exist
      if (!mceInit[editorId]) {
        // Use default settings from acf_content or create new ones
        var baseSettings = mceInit.acf_content || mceInit.content || {};
        mceInit[editorId] = $.extend(true, {}, baseSettings, {
          id: editorId,
          selector: '#' + editorId,
          elements: editorId,
        });
      }

      // Create Quicktags settings if they don't exist
      if (!qtInit[editorId]) {
        var baseQtSettings = qtInit.acf_content || qtInit.content || {};
        qtInit[editorId] = $.extend(true, {}, baseQtSettings, {
          id: editorId,
        });
      }

      // Initialize the editor
      if (window.tinymce && !tinymce.get(editorId)) {
        tinymce.init(mceInit[editorId]);
      }

      // Initialize Quicktags
      if (window.quicktags && !window.editorIds) {
        window.editorIds = {};
      }
      if (window.quicktags && !window.editorIds[editorId]) {
        quicktags(qtInit[editorId]);
        window.editorIds[editorId] = true;
      }

      // Setup tab switching
      setupEditorTabs(editorId);
    });
  }

  // Setup Visual/HTML tab switching for an editor
  function setupEditorTabs(editorId) {
    var $editor = $('#' + editorId);
    var $wrap = $editor.closest('.wp-editor-wrap');

    if (!$wrap.length) return;

    var $visualTab = $wrap.find('.wp-switch-editor.switch-tmce');
    var $htmlTab = $wrap.find('.wp-switch-editor.switch-html');

    // Visual tab click handler
    $visualTab.off('click.hihat').on('click.hihat', function (e) {
      e.preventDefault();

      if ($visualTab.hasClass('active')) return;

      // Save content from textarea to TinyMCE
      if (window.tinymce && tinymce.get(editorId)) {
        tinymce.get(editorId).setContent($editor.val(), { format: 'raw' });
      } else if (window.tinymce && window.tinyMCEPreInit) {
        // Initialize TinyMCE if not already done
        tinymce.init(window.tinyMCEPreInit.mceInit[editorId]);
      }

      $wrap.removeClass('html-active').addClass('tmce-active');
      $visualTab.addClass('active');
      $htmlTab.removeClass('active');
      $editor.hide();
    });

    // HTML tab click handler
    $htmlTab.off('click.hihat').on('click.hihat', function (e) {
      e.preventDefault();

      if ($htmlTab.hasClass('active')) return;

      // Get content from TinyMCE and put it in textarea
      if (window.tinymce && tinymce.get(editorId)) {
        tinymce.get(editorId).save();
      }

      $wrap.removeClass('tmce-active').addClass('html-active');
      $htmlTab.addClass('active');
      $visualTab.removeClass('active');
      $editor.show();
    });
  }

  // Initialize on document ready
  $(document).ready(function () {
    setTimeout(function () {
      initEditors();
    }, 200);
  });

  // Re-initialize when items are added to the repeater
  $(document).on('click', '.hi-hat-repeater-add-button', function () {
    setTimeout(function () {
      initEditors();
    }, 300);
  });
})(jQuery);
