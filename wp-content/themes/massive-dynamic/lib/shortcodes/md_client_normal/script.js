function pixflow_clientNormal() {
    'use strict';

    if (!$('.client-normal').length)
        return;

    var
        $clientNormal         = $('.client-normal'),
        clientNormalHeight    = $clientNormal.height(),
        movementStrengthBg    = 200,
        widthBg               = movementStrengthBg    / ($(window).width()/2);

    $clientNormal.each(function() {
        var $this = $(this),
            bgImage = $this.find('.bg-image');

        $this.mousemove(function (e) {
            var pageX = e.pageX - $this.offset().left,
                newvalueBg = widthBg * pageX * -1 - 50;
            bgImage.css({"left": newvalueBg + "px"});
        });
    });
}

document_ready_functions.pixflow_clientNormal = [];