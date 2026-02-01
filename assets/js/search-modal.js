jQuery(function ($) {
    const $body  = $('body');
    const $modal = $('.mc-wp-search-any-modal');

    function openModal() {
        $modal.addClass('is-open');
        $body.addClass('sdw-modal-open');
        setTimeout(() => {
            $modal.find('.mc-wp-search-any-input').focus();
        }, 200);
    }

    function closeModal() {
        $modal.removeClass('is-open');
        $body.removeClass('sdw-modal-open');
    }

    // Open modal
    $(document).on('click', '.mc-wp-search-any-open', function (e) {
        e.preventDefault();
        openModal();
    });

    // Close (overlay / button)
    $(document).on('click', '.mc-wp-search-any-close, .mc-wp-search-any-modal-overlay', function () {
        closeModal();
    });

    // ESC key
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape' && $modal.hasClass('is-open')) {
            closeModal();
        }
    });
});
