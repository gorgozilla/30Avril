// @TODO: refactor

function pixflow_splitBox(window_resized, oreintation_changed) {
    "use strict";
    var $splitBox = $('.splitBox-holder'), flag = false;

    if (!$splitBox.length)
        return;

    if ($(window).width() < 1280) {
        flag = true;
    }

    $splitBox.each(function () {
        var $this = $(this),
            $parent = $this.parent('.md-splitBox'),
            $textHolder = $this.find('.text-holder'),
            $imageHolder = $this.find('.image-holder'),
            $arrowRight = $this.find('.arrow-right'),
            $textWidth = $this.find('.fixed-width'),
            heightValue, leftFlag;

        if (flag & window_resized) {
            var parentCol = $this.closest('.wpb_column'), cls = parentCol.attr('class');
            parentCol.attr('data-classes', cls);

        }


        if (flag) {
            var re1 = '(vc_col)';	// Variable Name 1
            var re2 = '(-)';	// Any Single Character 1
            var re3 = '(sm)';	// Word 1
            var re4 = '(-)';	// Any Single Character 2
            var re5 = '(\\d+)';	// Integer Number 1
            // var re6='( )';	// White Space 1

            var p = new RegExp(re1 + re2 + re3 + re4 + re5, ["i"]);
            var m = p.exec(cls);
            if (m != null) {
                var var1 = m[1];
                var c1 = m[2];
                var word1 = m[3];
                var c2 = m[4];
                var int1 = m[5];
                // var ws1=m[6];
                if (parseInt(m[5]) < 12) {
                    var tempClass = var1 + c1 + word1 + c2 + int1;
                    parentCol.removeClass(tempClass).addClass('col-sm-12');
                }
            }

        }
        else {
            var parentCol = $this.closest('.wpb_column')
            cls = parentCol.attr('data-classes');
            if (oreintation_changed) {
                parentCol.attr('class', cls);
            }

        }

        leftFlag = ($parent.hasClass('sb-left')) ? true : false;

        $textWidth.css({width: $textHolder.width() * .9});
        var wWidth = $(window).width();
        var wHeight = $(window).height();

        if (wWidth >= 1280) {
            heightValue = $parent.attr("data-height");
            $this.css({'height': heightValue});
        }
        else {

            if (wWidth > 500 && wWidth <= 800) {
                $imageHolder.css({'height': wHeight});
                $arrowRight.css({'top': wHeight});
                heightValue = $textWidth.outerHeight(true);
                $textHolder.css({'height': heightValue + 150});
                $textHolder.css({'align-item': 'center'});
                $this.css({'height': wHeight + heightValue + 150});
            }
            else {
                if ($imageHolder.outerHeight(true) == 0) {
                    var imgheight = 300;
                }
                else {
                    var imgheight = $imageHolder.outerHeight(true);
                }


                heightValue = $textWidth.outerHeight(true);
                heightValue = (flag && $(window).width() < 768 ) ? $textWidth.outerHeight(true) + imgheight + 100 : heightValue + 205;


                if (wWidth < 767) {

                    $textHolder.css({'height': heightValue + 200});
                    $textHolder.css({'align-item': 'center'});
                }
                $this.css({'height': heightValue});
            }
        }

        if (wWidth >= 960 & wWidth <= 1280) {
            if ($parent.closest('.vc_column_container').hasClass('col-sm-6')) {
                heightValue = $parent.attr("data-height");
                $this.css({'height': heightValue});
                $textWidth.css({width: $textHolder.width()});
            }
            $textHolder.css({'height': $this.outerHeight(true)});
            $arrowRight.css({'top': '50%'});
        }

        if (isMobile() == false) {
            if ($('body.compose-mode').hasClass('responsive-mode')) {
                $(this).unbind('mouseenter mouseleave');
                return;
            }
            $this.hover(function () {
                $textHolder.css('width', 'calc( 50% + 50px )');
                if (leftFlag) {
                    $arrowRight.css('left', 'calc( 50% + 50px )');
                } else {
                    $arrowRight.css('left', 'calc( 50% - 50px )');
                }
            }, function () {
                $textHolder.css('width', '50%');
                $arrowRight.css('left', '50%');
            });
        }
    });
}
responsive_functions.pixflow_splitBox = [true,true];
orientation_change_functions.pixflow_splitBox = [];