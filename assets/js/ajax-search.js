jQuery(function ($) {

    let timer = null;

    $('.mc-search-form[data-ajax-search="1"] input[name="keys"]').on('keyup', function () {

        const term = $(this).val();
        const resultsBox = $(this).siblings('.mc-ajax-results');

        if (term.length < 2) {
            resultsBox.empty().hide();
            return;
        }

        clearTimeout(timer);

        timer = setTimeout(function () {
            $.get(MC_Search.ajaxurl, {
                action: 'mc_ajax_search',
                term: term,
                nonce: MC_Search.nonce
            }, function (res) {

                if (!res.length) {
                    resultsBox.html('<div class="mc-no-ajax">No results found</div>').show();
                    return;
                }

                let html = '<ul class="mc-ajax-list">';

                res.forEach(item => {
                    html += `
                        <li>
                            <a href="${item.url}">
                                <img src="${item.img}" />
                                <div>
                                    <strong>${item.title}</strong>
                                    <p>${item.desc}</p>
                                </div>
                            </a>
                        </li>`;
                });

                html += '</ul>';

                resultsBox.html(html).show();
            });
        }, 300);
    });
});