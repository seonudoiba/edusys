/*
 * Every Bootstrap modal gets a way out at the bottom: when a modal has no
 * Cancel / Close button of its own (the × in the header does not count), one is
 * added to its footer, or a footer is added for it. "Cancel" when the modal is a
 * form, "Close" when it only shows something.
 *
 * Skipped: modals with data-no-cancel (for one that must not be dismissed) and
 * bootbox dialogs. Modals with a static backdrop get one too: that only stops
 * an accidental click outside from closing them.
 * Loaded by layout/footer.php, layout/student/footer.php and the public themes.
 */
(function ($) {
    if (!$) {
        return;
    }
    var WORDS = /^\s*(cancel|close|no|dismiss|back)\s*$/i;

    function ownFooter(content) {
        return content.find('.modal-footer').filter(function () {
            return $(this).closest('.modal-content')[0] === content[0];
        }).last();
    }

    function hasWayOut(content) {
        var found = false;
        content.find('button, a, input[type=button]').each(function () {
            var el = $(this);
            if (el.hasClass('close') || el.closest('.modal-content')[0] !== content[0]) {
                return;
            }
            if (el.is('[data-dismiss="modal"]') || WORDS.test(el.is('input') ? el.val() : el.text())) {
                found = true;
                return false;
            }
        });
        return found;
    }

    function add(modal) {
        if (modal.is('[data-no-cancel], .bootbox')) {
            return;
        }
        var content = modal.find('.modal-content').first();
        if (!content.length || content.find('> .modal-footer > .modal-cancel, .modal-cancel').length || hasWayOut(content)) {
            return;
        }
        var footer = ownFooter(content);
        var acts = content.find('form, button[type=submit], input[type=submit], select, textarea, input:not([type=hidden])').length > 0 ||
            footer.find('button, a.btn, input[type=button]').length > 0;
        var btn = $('<button type="button" class="btn btn-default modal-cancel" data-dismiss="modal"></button>').text(acts ? 'Cancel' : 'Close');
        if (footer.length) {
            footer.prepend(btn, ' ');
        } else {
            content.append($('<div class="modal-footer"></div>').append(btn));
        }
    }

    $(document).on('show.bs.modal shown.bs.modal', '.modal', function (e) {
        if (e.target === this) {
            add($(this));
        }
    });
    // Modals whose body or footer is filled in after opening.
    $(document).ajaxComplete(function () {
        setTimeout(function () {
            $('.modal.in').each(function () {
                add($(this));
            });
        }, 0);
    });
})(window.jQuery);
