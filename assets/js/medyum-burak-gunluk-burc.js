jQuery(function($) {
    $(".burc_container > .burc_item > a").click(function(e) {
        e.preventDefault();
        var slug = $(this).attr("data-slug");
        $.magnificPopup.open({
            items: {
                src: '/?load_burc=' + slug
            },
            type: 'ajax'
        });
        return false;
    });
});