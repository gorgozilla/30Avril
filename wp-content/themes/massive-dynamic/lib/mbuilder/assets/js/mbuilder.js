/**
 * *********************
 * * mBuilder Composer *
 * *********************
 * mBuilder is a visual editor for shortcodes and makes working with shortcodes more easier and fun.
 * It is added as a part of Massive Dynamic since V3.0.0 and designed to work with customizer. Enjoy Editing ;)
 *
 * @summary mBuilder provides some functionality for editing shortcodes in customizer.
 *
 * @author PixFlow
 *
 * @version 1.0.0
 * @requires jQuery, jQuery.ui
 *
 * @class
 * @classdesc initialize all of the mBuilder features.
 */
$(window).load(function () {
});
var mBuilder = function(){
    // All shortcode attributes and contents stored in models, and should update after editing
    if(typeof mBuilderModels == 'undefined'){
        mBuilderModels = {};
        mBuilderModels.models = {}
    }
    this.models = mBuilderModels;
    this.lock = false;
    // All available shortcodes
    this.shortcodes = mBuilderShortcodes;

    this.settingPanel = null;

    // Defines droppable areas for drop shortcodes
    this.droppables = '' +
        '.vc_column_container,' +
        '.wpb_accordion_content,' +
        '.wpb_toggle_content,' +
        '.wpb_tour_tabs_wrapper,'+
        '.wpb_tab';

    // Container shortcodes
    this.containers = {
        'md_accordion_tab': '> .wpb_accordion_section > .wpb_accordion_content',
        'md_toggle_tab': '> .wpb_accordion_section > .wpb_toggle_content',
        'md_toggle_tab2': '> .wpb_accordion_section > .wpb_toggle_content',
        'md_tab': '> .wpb_tab',
        'md_tabs':"> .wpb_tabs > .wpb_wrapper",
        'md_modernTab':"> .wpb_tab",
        'md_modernTabs':"> .wpb_tabs > .wpb_wrapper",
        'vc_column': '> .wpb_column > .vc_column-inner> .wpb_wrapper',
        'vc_column_inner': '> .wpb_column > .vc_column-inner> .wpb_wrapper',
        'md_hor_tab':"> .wpb_tab",
        'md_hor_tabs':"> .wpb_tabs > .wpb_wrapper",
        'md_hor_tab2':"> .wpb_tab",
        'md_hor_tabs2':"> .wpb_tabs > .wpb_wrapper",
    };

    //Tab Shortcodes
    this.tabs = {
        'md_tabs':['md_tab','<li data-model="md_tabs"><a href="#"><i class="left-icon icon-cog"></i><span>Tab</span></a></li>'],
        'md_modernTabs':['md_modernTab','<li data-model="md_modernTabs"><a href="#"><i class="left-icon icon-cog"></i><div class="modernTabTitle">Tab</div></a></li>'],
        'md_hor_tabs':['md_hor_tab','<li data-model="md_hor_tabs"><a href="#"><i class="right-icon icon-cog"></i><div class="horTabTitle">Tab</div><i class="right-icon icon-angle-right"></i></a></li>'],
        'md_hor_tabs2':['md_hor_tab2','<li data-model="md_hor_tabs2"><a href="#"><i class="right-icon icon-cog"></i><div class="horTabTitle">Tab</div></a></li>'],
    };



    //Full shortcodes
    this.fullShortcodes = [
        'md_team_member_classic',
        'vc_empty_space',
        'md_button',
        'md_call_to_action',
        'md_imagebox_full',
        'md_portfolio_multisize',
        'md_showcase',
        'md_blog',
        'md_blog_carousel',
        'md_client_normal',
        'md_instagram',
        'md_blog_masonry',
        'md_process_steps',
        'md_teammember2',
        'pixflow_subscribe',
        'md_pricetabel',
        'md_google_map',
        'md_masterslider',
        'md_rev_slider',
        'md_blog_classic',
        'vc_facebook',
        'vc_tweetmeme',
        'vc_pinterest',
        'vc_gmaps',
        'vc_round_chart',
        'vc_line_chart',
        'md_product_categories',
        'md_products',
        'md_textbox',
        'md_full_button',
        'md_testimonial_classic',
        'md_client_carousel',
        'md_fancy_text',
        'md_iconbox_side',
        'md_iconbox_side2',
        'md_slider',
        'md_testimonial_carousel',
        'md_modern_subscribe',
        'md_double_slider',
        'md_skill_style2',
        'md_slider_carousel',
        'md_slider',
        'md_text_box'
    ];

    //used in shortcodeTag method
    this.compiledTags = [];


    var isLocal = $.ui.tabs.prototype._isLocal;
    $.ui.tabs.prototype._isLocal = function ( anchor ) {
        return true;
    };

    this.renderControls();
    this.addEvents();
    this.setSortable();
    this.mediaPanel();
    this.multiMediaPanel();
    this.googleFontPanel();
    this.renderShortcodePanel();
};


/**
 * @summary makes shortcodes sortable.
 *
 * @since 1.0.0
 */
mBuilder.prototype.setSortable = function(){
    var t = this;
    var lastObj = null;
    var fly = null;
    $('.mBuilder-overlay').remove();
    var d = $('<div style="position: absolute; height: 5px; z-index: 999999;"></div>').appendTo('body'),
        overlay = $('<div class="mBuilder-overlay" style="position: fixed; height: 100%; z-index: 9999999; width: 100%; top: 0px; left: 0px; display: none;"></div>').appendTo('body'),
        direction = 'down',
        overEmpty = false,
        overs=$,
        helper;
    overlay.click(function(){
        d.css('width','');
        overlay.css('display','none');
    })

    $('.mBuilder-element:not(.vc_row,.mBuilder-vc_column)').draggable({
        zIndex:999999,
        helper:'clone',
        appendTo:'body',
        items: ":not(.disable-sort)",
        start: function( event, ui ) {
            ui.helper.css({
                width:$(this).width(),
                height:$(this).height()
            });

            clearInterval(fly);
            var that = this;

            if($(this).hasClass("mBuilder-md_portfolio_multisize")){
                ui.helper.addClass("portfolio-draged");
            }


            setTimeout(function(){
                overs = $('.mBuilder-element:not(.vc_row,.mBuilder-vc_column),.vc_empty-element')
                    .not(ui.helper)
                    .not(ui.helper.find('.mBuilder-element:not(.vc_row,.mBuilder-vc_column),.vc_empty-element'))
                    .not($(that).find('.mBuilder-element:not(.vc_row,.mBuilder-vc_column),.vc_empty-element'));
            },100);
            $(this).addClass('ui-sortable-helper');
            overlay.css('display','block');
        },
        drag: function( event, ui ) {
            clearInterval(fly);
            if(event.clientY < 100){
                fly = setInterval(function(){
                    $(window).scrollTop($(window).scrollTop()-50)
                },50);
            }else if(event.clientY > $(window).height()-50){
                fly = setInterval(function() {
                    $(window).scrollTop($(window).scrollTop() + 50)
                },50);
            }
            var el = null;
            overs.each(function(){
                if(
                    $(this).css('display') != 'none' &&
                    event.pageY > $(this).offset().top && event.pageY < $(this).offset().top + $(this).outerHeight() &&
                    event.pageX > $(this).offset().left && event.pageX < $(this).offset().left + $(this).outerWidth()
                ){
                    el = this;
                }
            });

            if(el){

                overEmpty = false;
                var obj = $(el);
                if(el != this && obj.length && !obj.hasClass('vc_empty-element')){

                    if(t.containers[obj.attr('data-mbuilder-el')] && !obj.find('.mBuilder-element').length){
                        overEmpty = true;
                    }else {
                        d.css({border: '', borderTop: '4px solid #8fcbff'});
                    }
                }else{
                    overEmpty = true;

                }
                var objTop = obj.offset().top,
                    objLeft = obj.offset().left,
                    objHeight = obj.outerHeight(),
                    objWidth = obj.outerWidth(),
                    objHalf = objTop + objHeight/2;
                if(lastObj){
                    lastObj.css({'transform': ''})
                }
                if(!overEmpty) {
                    if (event.pageY  < objHalf) {
                        obj.not('.vc_row').css({'transform': 'translateY(5px)'});
                        d.css({'top': objTop, 'left': objLeft, width: objWidth,height:5,background:''});
                        direction = 'up';
                    } else {
                        obj.not('.vc_row').css({'transform': 'translateY(-5px)'});
                        d.css({'top': objTop + objHeight, 'left': objLeft, width: objWidth,height:5,background:''});
                        direction = 'down';
                    }
                }else{
                    d.css({'top': objTop, 'left': objLeft,height:objHeight,width:objWidth,background:'rgba(136,206,255,0.4)',border:'solid 2px #8fcbff'});
                }
                lastObj = obj;
            }else{
                if(lastObj){
                    lastObj.css({'transform': ''})
                }
                lastObj = null;
                d.css({width:'',border:''});
            }
        },
        stop: function(event, ui){
            try{

                if(ui.helper.hasClass("portfolio-draged")){
                    ui.helper.removeClass("portfolio-draged");
                }

                clearInterval(fly);
                $(this).removeClass('ui-sortable-helper');
                if(!lastObj || !lastObj.length){
                    d.css({'width':'',border:''});
                    setTimeout(function(){overlay.css('display','none');},300);
                    return;
                }
                if(direction=='up') {
                    if(lastObj.hasClass('vc_empty-element')) {
                        var p = lastObj.find('.wpb_wrapper');
                    }else if(t.containers[lastObj.attr('data-mbuilder-el')] && overEmpty){
                        var p = lastObj.find(t.containers[lastObj.attr('data-mbuilder-el')]);
                    }else{
                        var p = lastObj.prev('.insert-between-placeholder');
                        if(!p.length){
                            var p = lastObj.parent().closest('.mBuilder-element').prev('.insert-between-placeholder');
                        }
                    }
                }else{
                    if(lastObj.hasClass('vc_empty-element')) {
                        var p = lastObj.find('.wpb_wrapper');
                    }else if(t.containers[lastObj.attr('data-mbuilder-el')] && overEmpty){
                        var p = lastObj.find(t.containers[lastObj.attr('data-mbuilder-el')]);
                    }else{
                        var p = lastObj.next('.insert-between-placeholder');
                        if(!p.length){
                            var p = lastObj.parent().closest('.mBuilder-element').next('.insert-between-placeholder');
                        }
                    }
                }
                placeholder =  p.get(0);
                if(placeholder != null) {
                    if($(this).closest('.vc_column_container').find('.mBuilder-element').not($(this).find('.mBuilder-element')).length<2 && lastObj.get(0) != this){
                        $(this).closest('.vc_column_container').addClass('vc_empty-element');
                    }
                    if(lastObj.hasClass('vc_empty-element')){
                        $(this).appendTo(placeholder);
                        lastObj.removeClass('vc_empty-element')
                    }else{
                        if(!$(this).find(placeholder).length) {
                            if(t.containers[lastObj.attr('data-mbuilder-el')] && overEmpty) {
                                p.html('');
                                $(this).appendTo(placeholder);
                            }else{
                                $(this).insertAfter(placeholder);
                            }
                        }
                    }
                    setTimeout(function(){
                        t.createPlaceholders();
                    },100)
                }
                d.css({'width':'',border:''});
                setTimeout(function(){overlay.css('display','none');},300);
            }catch(e){
                console.log(e);
                d.css({'width':'',border:''});
                setTimeout(function(){overlay.css('display','none');},300);
            }
        }
    })

    // Row movement
    $( ".content-container" ).sortable({
        cursor: "move",
        delay: 100,
        cancel: ".disable-sort",
        handle: ".mBuilder_row_move",
        update: function( event, ui ) {
            $('body').addClass('changed');
            t.createPlaceholders();
        }
    });
    $( ".content-container" ).disableSelection();
};


/**
 * @summary add shortcode controllers for edit,delete,clone and etc.
 *
 * @since 1.0.0
 */
mBuilder.prototype.renderControls = function(){
    var t = this;
	var countTiny = 0 ;
    tinymce.init({
        selector: '.inline-editor',
    	theme_advanced_toolbar_location: "top",
        forced_root_block: 'p',
		force_p_newlines : false,
		theme_advanced_resizing: false,
    	theme_advanced_resizing_use_cookie : false,
        force_br_newlines: false,
        tabfocus_elements: ":next" ,
		toolbar_items_size: 'small',
		menubar: false,
		block_formats: 'Header 1=h1;Header 2=h2;Header 3=h3;Header 4=h4;Header 5=h5;Header 6=h6',
        inline: true,
		setup: function(editor) {
			editor.on('focus' , function(e){
                e.stopPropagation();
                $('#' + editor.id).addClass('do-save-text');
			setTimeout(function(){
			$('.mce-tinymce').each(function(){
				 $(this).find('.mce-txt').eq(0).html('') ; 
				 $(this).find('.mce-txt').eq(1).html('') ; 
				 });
			} , 100 );
			});
            editor.on('blur' , function(e){
                var $doSaveText = $('.do-save-text');
                if($doSaveText.length) {
                    var id = $doSaveText.parents('.ui-draggable').data('mbuilder-id');
                    if($doSaveText.text().trim() == ''){
                        var $newContent = '';
                        $('div[data-mbuilder-id=' + id + ']').addClass('no-text');
                    }else{
                        var $newContent = $doSaveText.html();
                        $('div[data-mbuilder-id=' + id + ']').removeClass('no-text');
                    }
                    t.models.models[id].content = $newContent;
                    if ( $doSaveText.closest('.no-text').length){
                        $doSaveText.closest('.no-text').find('.mBuilder_controls').append('<a href="javascript:void(0)" class="add-content"> + content </a>');
                    }
                    $doSaveText.removeClass('do-save-text');
                    document.getSelection().removeAllRanges();
                }
			});
			editor.addButton('mybutton', {
                text: 'More Option',
                icon: false ,
                onclick: function () {
                    $('.do-save-text').blur();
                    $('.do-save-text').closest('.gizmo-container').find(' > .mBuilder_controls .sc-setting').click();
                }
            });
		},
        fontsize_formats: '8pt 10pt 12pt 14pt 18pt 24pt 36pt' ,
        toolbar: 'formatselect fontsizeselect forecolor | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link | undo redo | mybutton' ,
           plugins: 'textcolor link tabfocus'
    });

    tinymce.init({
        selector: '.inline-editor-title',
        theme_advanced_toolbar_location: "top",
        forced_root_block: 'div',
		force_p_newlines : false,
		theme_advanced_resizing: false,
    	theme_advanced_resizing_use_cookie : false,
        force_br_newlines: false,
		toolbar_items_size: 'small',
        tabfocus_elements: ":next" ,
		block_formats: 'Header 1=h1;Header 2=h2;Header 3=h3;Header 4=h4;Header 5=h5;Header 6=h6',
        inline: true,
        setup: function (editor) {
			editor.on('focus' , function(){
			setTimeout(function(){
			$('.mce-tinymce').each(function(){
				 $(this).find('.mce-txt').eq(0).html('') ; 
				 $(this).find('.mce-txt').eq(1).html('') ; 
				 });
			} , 100 );
			});
            editor.on('blur', function () {
                var $doSave = $('.do-save');
                if ($doSave.length) {
                    var id = $doSave.closest('.ui-draggable').data('mbuilder-id');
                    if($doSave.text().trim() == ''){
                        var $newTitle = '';
                        $('div[data-mbuilder-id=' + id + ']').addClass('no-title');
                    }else{
                        var $newTitle = $doSave.html();
                        $newTitle =  $newTitle.replace(new RegExp('"', 'g') ,  ' pixFLow_editor ');
                        $('div[data-mbuilder-id=' + id + ']').removeClass('no-title');
                    }
                    if ( $('.do-save').closest('.no-title').length){
                        $('.do-save').closest('.no-title').find('.mBuilder_controls').append('<a href="javascript:void(0)" class="add-title"> + title </a>');
                    }
                    $('.inline-editor-title').removeClass('do-save');
                    t.setModelattr(id, 'md_text_title1', $newTitle);
                    // set it to compatible with VC backend editor
                    t.setModelattr(id, 'md_text_use_title_slider', 'yes');
                    document.getSelection().removeAllRanges();

                }
            });
			
            editor.addButton('mybutton', {
                text: 'More Option',
                icon: false,
                onclick: function () {
                    $('.do-save').blur();
                    $('.do-save').closest('.gizmo-container').find(' > .mBuilder_controls .sc-setting').click();
                }
            });
        },
        fontsize_formats: '8pt 10pt 12pt 14pt 18pt 24pt 36pt',
        toolbar: 'formatselect fontsizeselect forecolor | bold italic | alignleft aligncenter alignright alignjustify | link | undo redo | mybutton',
        menubar: false,
        plugins: 'textcolor link tabfocus'
    });

    $('body').addClass('compose-mode');

    $('body').on('click','a',function(){
        if($(this).attr('href') == undefined){
            $(this).attr('href','#');
        }
    });

    var settingSvg = '<span class="mdb-settingsvg" ></span>',

        duplicateSvg = '<span class="mdb-duplicatesvg" ></span>',

        deleteSvg = '<span class="mdb-deletesvg" ></span>',

        leftAlignSvg = '<span class="mdb-leftalignsvg" ></span>',

        centerAlignSvg = '<span class="mdb-centeralignsvg" ></span>',

        rightAlignSvg = '<span class="mdb-rightalignsvg" ></span>',

        optionSvg = '<span class="mdb-optionsvg" ></span>',

        col1_1Svg = '<span class="mdb-col1-1svg" ></span>',

        col1_2Svg = '<span class="mdb-col1-2svg" ></span>',

        col1_3Svg = '<span class="mdb-col1-3svg" ></span>',

        col1_4Svg = '<span class="mdb-col1-4svg" ></span>',

        col2_4Svg = '<span class="mdb-col2-4svg" ></span>',

        col3_4Svg = '<span class="mdb-col3-4svg" ></span>',

        col3_9Svg = '<span class="mdb-col3-9svg" ></span>',

        layoutSvg = '<span class="mdb-layoutsvg" ></span>',

        moveSvg = '<span class="mdb-movesvg" ></span>',

        rowSettingSvg = '<span class="mdb-rowsettingsvg" ></span>' ;



    $('.mBuilder-element').not('.vc_row, .vc_row_inner,.mBuilder-vc_row_inner,.mBuilder-vc_column,.mBuilder-vc_column_inner').each(function(){

        var $this = $(this);

        if(!$this.find('.mBuilder_controls').first().length) {
            if ($this.hasClass('mBuilder-md_tabs') || $this.hasClass('mBuilder-md_toggle') ||
                $this.hasClass('mBuilder-md_accordion') || $this.hasClass('mBuilder-md_modernTabs') ||
                $this.hasClass('mBuilder-md_hor_tabs') || $this.hasClass('mBuilder-md_toggle2')||
                $this.hasClass('mBuilder-md_hor_tabs2')){

                var html = '<div class="mBuilder_controls tabs-family sc-control " >' +
                    '<div class="mBuilder_move">'+moveSvg+'</div>'+
                    '<div class="sc-setting setting">' + settingSvg + '</div>' +
                    '<div class="sc-delete">' + deleteSvg + '</div>' +
                    '</div>';
                $this.append(html);

            }else if($this.hasClass('mBuilder-md_tab')||$this.hasClass('mBuilder-md_toggle_tab')||
                $this.hasClass('mBuilder-md_accordion_tab')||$this.hasClass('mBuilder-md_modernTab') ||
                $this.hasClass('mBuilder-md_hor_tab')|| $this.hasClass('mBuilder-md_toggle_tab2') ||
                $this.hasClass('mBuilder-md_hor_tab2') ) {
                var html = '<div class="mBuilder_controls tab sc-control " >' +
                    '<div class="sc-setting setting">' + settingSvg + '</div>' +
                    '<div class="sc-delete">' + deleteSvg + '</div>';
                /*if ($this.hasClass('mBuilder-md_accordion_tab') ||
                 $this.hasClass('mBuilder-md_toggle_tab2') || $this.hasClass('mBuilder-md_toggle_tab')) {
                 html += '<div class="sc-duplicate">' + duplicateSvg + '</div>';
                 }*/
                html += '</div>';
                $this.append(html);

            }else {
                var el = $this.attr('data-mbuilder-el'),
                    fullClass = '';
                if(t.fullShortcodes.indexOf(el) != -1){
                    fullClass = 'md-full-shortcode-gizmo';
                }
                var $elem = $this;
                if($this.find('.gizmo-container').length){
                    $elem = $this.find('.gizmo-container').first();
                }
                if($this.hasClass('mBuilder-md_button')|| $this.hasClass('mBuilder-vc_empty_space')){
                    $elem.append('<div class="mBuilder_controls sc-control " >' +
                        '<a href="javascript:void(0)" class="sc-delete"><span>x</span></a>' +
                        '<div class="settings-holder">' +
                        '<div class="sc-setting setting">' + settingSvg + '</div>' +
                        '</div>' +
                        '</div>');

                }else {
                    var html = '<div class="mBuilder_controls sc-control " >' +
                        '<span class="handel top-left"></span>' +
                        '<span class="handel top-right"></span>' +
                        '<span class="handel bottom-left"></span>' +
                        '<span class="handel bottom-right"></span>' +
                        '<div class="settings-holder">' +
                        '<div class="sc-setting setting">' + settingSvg + '</div>' +
                        '<div class="sc-option">' +
                        '<div class="options-holder ' + fullClass + '">' +
                        '<a href="javascript:void(0)" class="column-setting">' + mBuilderValues.columnText + '</a>' +
                        '<a href="javascript:void(0)" class="sc-duplicate">' + duplicateSvg + '<span>' + mBuilderValues.duplicateText + '</span></a>' +
                        '<a href="javascript:void(0)" class="sc-delete">' + deleteSvg + '<span>' + mBuilderValues.deleteText + '</span></a>' +
                        '<a href="javascript:void(0)" class="sc-alignment">' +
                        '<span class="left">' + leftAlignSvg + '</span>' +
                        '<span class="center">' + centerAlignSvg + '</span>' +
                        '<span class="right">' + rightAlignSvg + '</span>' +
                        '</a>' +
                        '</div>' +
                        '<a href="javascript:void(0)" class="setting options-button">' + optionSvg + '</a>' +
                        '</div>' +
                        '</div>' ;

                    if ($this.hasClass('mBuilder-md_text')){
                        if($this.hasClass('no-title')){
                            html += '<a href="javascript:void(0)" class="add-title"> + title </a>';
                        }
                        if($this.hasClass('no-text')){
                            html += '<a href="javascript:void(0)" class="add-content"> + content </a>';
                        }
                    }
                    html += '</div>'
                    $elem.append(html);
                }
            }
        }

        if(t.shortcodes[$this.attr('data-mbuilder-el')] && t.shortcodes[$this.attr('data-mbuilder-el')].as_parent){
            if(!$this.find(' > .mBuilder_controls [data-control="add_section"]').length) {
                var btn = $('<span class="vc_btn-content"><span class="icon"></span></span>');
                var link = $('<a class="vc_control-btn" title="Add new Section" data-control="add_section" href="#" target="_blank"></a>');
                link.append(btn);
                $this.find(' > .mBuilder_controls').append(link);
                var child = t.shortcodes[$(this).attr('data-mbuilder-el')].as_parent['only'];
                btn.click(function () {
                    t.buildShortcode(this, child);
                })
            }
        }
    });


    $('.mBuilder-element.vc_row,.vc_row.vc_inner').each(function(){
        var $this = $(this);
        if(!$this.find('> .mBuilder_row_controls ').length) {
            $this.find('>.wrap').after('\
                <div class="mBuilder_row_controls">\
                    <div href="javascript:void(0)" class="mBuilder_row_move">'+moveSvg+'</div>\
                    <div class="mBuilder_setting_panel">\
                        <a href="javascript:void(0)" class="title">'+rowSettingSvg+'<span>'+mBuilderValues.rowText+'</span></a>\
                        <div class="mBuilder_container">\
                        <a href="javascript:void(0)" class="mBuilder_row_setting">'+settingSvg+'<span>'+mBuilderValues.settingText+'</span></a>\
                        <a href="javascript:void(0)" class="mBuilder_row_delete">'+deleteSvg+'<span>'+mBuilderValues.deleteText+'</span></a>\
                        <a href="javascript:void(0)" class="mBuilder_row_duplicate">'+duplicateSvg+'<span>'+mBuilderValues.duplicateText+'</span></a>\
                        </div>\
                    </div>\
                    <div class="mBuilder_row_layout">\
                        <a href="javascript:void(0)" class="title">'+layoutSvg+'<span>'+mBuilderValues.layoutText+'</span></a>\
                        <div class="mBuilder_container"><div class="holder">\
                            <span class="col" data-colSize="12/12">'+col1_1Svg+'</span><span class="separator"></span> \
                            <span class="col" data-colSize="6/12+6/12">'+col1_2Svg+'</span><span class="separator"></span> \
                            <span class="col" data-colSize="4/12+4/12+4/12">'+col1_3Svg+'</span><span class="separator"></span>\
                            <span class="col" data-colSize="3/12+3/12+3/12+3/12">'+col1_4Svg+'</span><span class="separator"></span>\
                            <span class="col" data-colSize="2/12+8/12+2/12">'+col2_4Svg+'</span><span class="separator"></span>\
                            <span class="col" data-colSize="10/12+2/12">'+col3_4Svg+'</span><span class="separator"></span>\
                            <span class="col" data-colSize = "3/12+9/12">'+col3_9Svg+'</span>\
                            <hr>\
                            <label>'+mBuilderValues.customColText+'</label><input placeholder="12/12" name="cols" value=""><span class="submit">→</span>\
                        </div></div>\
                    </div>\
                </div>\
            ');
        }

        if(!$this.hasClass('vc_inner')){
            if (! $this.find('> .row_border ').length ){
                $this.append('<div class="row_border top"></div><div class="row_border right"></div><div class="row_border left"></div>');
            }
        }
    });

    $('.mBuilder-vc_column,.mBuilder-vc_column_inner').each(function(){
        var itemClass = ($(this).hasClass('mBuilder-vc_column'))?'element-vc_column':'element-vc_column_inner';

        if(!$(this).find('> .vc_column_container > .element-vc_column').length) {
            $(this).find(' > .vc_column_container').append('<div class=\"vc_element vc_active '+ itemClass +'\" >' +
                '<a class="vc_control-btn vc_element-name vc_element-move vc_move-vc_column" title="Drag to move Column" target="_blank">' +
                '<span class="vc_btn-content">Column</span>' +
                '</a><span class="vc_advanced">' +
                '<a class="vc_control-btn vc_control-btn-edit" href="#" title="Edit Column" target="_blank">' +
                '<span class="vc_btn-content">' +
                '<span class="icon"></span>' +
                '</span></a>' +
                '<a class="vc_control-btn vc_control-btn-prepend" href="#" title="Prepend to Column" target="_blank">' +
                '<span class="vc_btn-content">' +
                '<span class="icon"></span>' +
                '</span></a>' +
                '<a class="vc_control-btn vc_control-btn-delete" href="#" title="Delete Column" target="_blank">' +
                '<span class="vc_btn-content">' +
                '<span class="icon"></span>' +
                '</span></a></span>' +
                '<a class="vc_control-btn vc_control-btn-switcher" title="Show Column controls" target="_blank">' +
                '<span class="vc_btn-content"><span class="icon"></span></span>' +
                '</a>' +
                '</div>'
            );
        }
    });

    for(var i in this.fullShortcodes){
        $('.mBuilder-element[data-mbuilder-el="'+this.fullShortcodes[i]+'"]').find('.vc_control-btn-align').remove();
        $('.mBuilder-element[data-mbuilder-el="'+this.fullShortcodes[i]+'"]').find('.vc_control-btn-edit').css('left',-99);
        $('.mBuilder-element[data-mbuilder-el="'+this.fullShortcodes[i]+'"]').find('.vc_control-btn-clone').css('left',3);
        $('.mBuilder-element[data-mbuilder-el="'+this.fullShortcodes[i]+'"]').find('.vc_control-btn-delete').css('left',105);
    }

    try {
        window.top.$('.shortcodes-panel').getNiceScroll().resize();
    }catch(e){}
    this.createPlaceholders();
};


/**
 * @summary add event to shortcode controllers for edit,delete,clone and etc.
 *
 * @since 1.0.0
 */

mBuilder.prototype.addEvents = function(){
    var t = this,
        $bodyGizmoOff = $('body:not(.gizmo-off)'),
        $body = $('body');

	// Inline Editor For Title

    $bodyGizmoOff.on('mouseover' , '.inline-editor-title , .inline-editor' , function(e){
        $(".content-container").enableSelection();
        $('body:not(.gizmo-off) .ui-draggable').draggable("option", "disabled", true);
    });

    $bodyGizmoOff.on('mouseleave' , '.inline-editor-title , .inline-editor' , function(e){
        $(".content-container").disableSelection();
        $('body:not(.gizmo-off) .ui-draggable').draggable("enable");
    });

    $(window).on('scroll' , function(){
        $('.inline-editor-title , .inline-editor').trigger('blur');
    });

    $body.on('click' , '.inline-editor-title' , function(e){
		e.stopPropagation();
		closeAll();
		$(this).addClass('do-save');
	});

    $body.on('click','.add-title',function(){
       var $this = $(this);
        $this.closest('.no-title').removeClass('no-title');
        $this.closest('.md-text').find('.without-title').removeClass('without-title');
    });

    $body.on('click','.add-content',function(){
        var $this = $(this);
        $this.closest('.no-text').removeClass('no-text');
        $this.closest('.md-text').find('.without-content').removeClass('without-content');
    });

    $(document).click(function(e){
        closeAll();
    });

    function closeAll(notMe){
        var $activeElems = $('.active-gizmo').not(notMe);
        $activeElems.each(function(){
            var $this = $(this),
                $innerRow = $this.closest('.mBuilder-vc_row_inner');

            $this.removeClass('active-gizmo');

            TweenMax.to($this.find('.options-holder'),.2,
                {
                    scale: .9, opacity: 0, delay:.4, onComplete: function () {
                    TweenMax.set($this.find('>.mBuilder_container, .options-holder'), {height: 0, zIndex: -333});
                }
                });
            $this.closest('div[class*=mBuilder-vc_column]').removeClass('upper_zIndex');

            if ($this.hasClass('mBuilder_setting_panel') || $this.hasClass('mBuilder_row_layout')) {
                $this.closest('div[class*=vc_row]').removeClass('upper_zIndex');
            }

            $this.find('>.mBuilder_container, .options-holder').removeClass('open');

            if ($this.hasClass('mBuilder_row_layout')) {
                $this.find('input').focus();
            }

            if ($innerRow.length) {
                $innerRow.removeClass('upper_inner_row_zIndex');
                $innerRow.parents('.vc_row').removeClass('upper_inner_row_zIndex');
                $innerRow.siblings('.mBuilder-element').removeClass('lower_inner_row_zIndex')
            }

        })
    }

    // Row Layout
    $body.on('click','.mBuilder-element .mBuilder_row_layout .col,.mBuilder-element .mBuilder_row_layout .submit',function(e){
        e.stopPropagation();
        var row = $(this).closest('.vc_row'),
            value =  $(this).attr('data-colSize');
        if ($(this).hasClass('submit')){
            value =  $(this).prev().val();
        }
        $(this).closest('.mBuilder_row_layout').find('input[name="cols"]').val(value);
        t.changeRowLayout(value,row);

    });

    //Column Setting
    $body.on('click','.mBuilder-element .column-setting',function(){
        var $btn = $(this).closest('.wpb_column').find('.vc_control-btn-edit'),
            i = 0;
        if($btn.length>1){
            i = $btn.length-1;
        }
        $btn.eq(i).click();
    });

    // Edit Element
    $body.on('click','.mBuilder-element .vc_control-btn-edit,.mBuilder-element .mBuilder_row_setting,.mBuilder-element .sc-setting',function(e){
        e.stopPropagation();
        var params = t.getModelParams($(this).closest('.mBuilder-element').attr('data-mBuilder-id')),
            el_id = $(this).closest('.mBuilder-element').attr('data-mBuilder-id');

        if(params==null){
            params = [];
            params['attr'] = '';
            params['content'] = '';
            params['type'] = $(this).closest('.mBuilder-element').attr('data-mbuilder-el');
        }
        t.mBuilder_shortcodeSetting(t.shortcodes[params['type']].name + ' Settings','','<div class="mbuilder-spinner"></div>','Update',function(){},'Close',function(){});
        $.ajax({
            type: 'post',
            url: mBuilderValues.ajax_url,
            data: {
                action: 'mBuilder_settingPanel',
                nonce: mBuilderValues.ajax_nonce,
                attr: params['attr'],
                content : params['content'],
                type: params['type'],
                mbuilder_editor: true
            },
            success: function (response) {
                t.mBuilder_shortcodeSetting(t.shortcodes[params['type']].name + ' Settings','dont-show',response,'Update',function(){
                        if(params['type'] == 'vc_column' || params['type'] == 'vc_column_inner'){
                            var css = '{';
                            $('#mBuilder-form #mBuilderDesignOptions .column-design-css input').each(function(){
                                if($(this).closest('.column-design-css').hasClass('column-design-prefix-px')){
                                    var prefix = 'px';
                                }else{
                                    var prefix = '';
                                }
                                if($(this).parent().hasClass('mBuilder-upload-img')){
                                    if($(this).val() != '' && $(this).val() != 'undefined'){
                                        var val = $(this).parent().css('background-image');
                                    }
                                }else{
                                    var val = $(this).val()
                                }
                                css += $(this).attr('name').replace(/_/g,'-')+':'+val+prefix+';';
                            });
                            $('#mBuilder-form #mBuilderDesignOptions .column-design-css select').each(function(){
                                css += $(this).attr('name').replace(/_/g,'-')+':'+$(this).val()+';';
                            });
                            css +='}';
                            css = css.replace(/["]/g,'``');
                            var cssInput = $('<input type="hidden" name="css">');
                            cssInput.val(css).appendTo($('#mBuilder-form #mBuilderDesignOptions'));
                        }
                        $.fn.serializeObject = function()
                        {
                            var o = {};
                            var a = this.serializeArray();
                            $.each(a, function() {
                                if(this.value == '' && !$('input[name="'+this.name+'"]').hasClass('simple-textbox') && $('[name="'+this.name+'"]').prop('tagName') != 'TEXTAREA'){
                                    return true;
                                }
                                if(this.value == 'Array'){
                                    this.value = '';
                                }
                                if (o[this.name] !== undefined) {
                                    if (!o[this.name].push) {
                                        o[this.name] = [o[this.name]];
                                    }
                                    o[this.name].push(this.value || '');
                                } else {
                                    o[this.name] = this.value || '';
                                }
                            });
                            return o;
                        };
                        var formData = $('#mBuilder-form').serializeObject();
                        var regex = /align="(.*?)"/;
                        if(t.models.models[el_id].attr) {
                            var res = t.models.models[el_id].attr.match(regex);
                        }else{
                            var res = null;
                        }
                        if(res != null){
                            formData.align = res[1];
                        }
                        var isTab = false;
                        if(t.tabs[params['type']] || params['type']=='vc_row_inner') isTab = true;

                        t.updateShortcode(el_id,params['type'],formData,undefined,isTab);
						cssInput && cssInput.remove();
						//t.check_time();
                    },
                    'Close',function(){
                        $('.setting-panel-close').click();
                    }
                );
                var isLocal = $.ui.tabs.prototype._isLocal;
                $.ui.tabs.prototype._isLocal = function ( anchor ) {
                    return true;
                };
                $('#mBuilderTabs').tabs();
                $('.setting-panel-wrapper .setting-panel-container').removeClass('dont-show');
                setTimeout(function(){t.dependencyInjection();},1);

            }
        });
    });

    // Delete Element
    $body.on('click','.mBuilder-element .mBuilder_row_delete,.mBuilder-element .sc-delete',function(e){
        var el_id = $(this).closest('.mBuilder-element').attr('data-mBuilder-id');
        $(this).parents('.mBuilder_controls').addClass('active-gizmo');
        e.stopPropagation();

        var $elem = $('div[data-mbuilder-id=' + el_id + ']');
        if ($elem.hasClass('mBuilder-md_button')||$elem.hasClass('mBuilder-vc_empty_space')){
            deleteFunc(el_id);
        } else if (!$(this).closest('.mBuilder_controls').find('.deleteMessage').length) {

            //close option panel on click
            var $this = $(this),
                $optionsHolder = $(this).closest('.mBuilder_container, .options-holder');

            var deleteBox = '<div class="deleteMessage"><p>' + mBuilderValues.deleteDescText + '</p><a class="deleteBtn">' + mBuilderValues.deleteText + '</a></div>';

            TweenMax.to($optionsHolder, .2, {
                scale: .9, opacity: 0, onComplete: function () {
                    TweenMax.set($optionsHolder, {height: 0, zIndex: -333});
                    //add delete alertBox

                    var $parent = $this.closest('.mBuilder_controls.sc-control,.mBuilder_row_controls');

                    $parent.after(deleteBox);

                    var $deleteMsgBox = $parent.siblings('.deleteMessage'),
                        $deleteBtn = $deleteMsgBox.find('.deleteBtn');



                    //deletBox Animation
                    // for tab
                    if ($elem.hasClass('mBuilder-md_tab') || $elem.hasClass('mBuilder-md_modernTab')) {

                        var left = parseInt($elem.find(' > .mBuilder_controls.tab ').css('left'));
                        left += 44 ;
                        $elem.find(' > .deleteMessage').css({'left':left})

                    }else if ($elem.hasClass('mBuilder-md_hor_tab') || $elem.hasClass('mBuilder-md_hor_tab2')){
                        var top = parseInt($elem.find(' > .mBuilder_controls.tab ').css('top'));
                        top += 44 ;
                        $elem.find(' > .deleteMessage').css({'top':top})
                    }else if ($elem.hasClass('vc_row') ){
                        if ( $('body .vc_row').first().attr('id') == $elem.attr('id') ){
                            top='40%';
                            $elem.find(' > .deleteMessage').css({'top':top})
                         }
                    }

                    TweenMax.to($deleteMsgBox, .2, {opacity: 1, bottom: '30px'});

                    if ($parent.hasClass('sc-control')){
                        $parent.addClass('deleteEffect')
                    }else{
                        $parent.siblings('.wrap,.sc-control').addClass('deleteEffect')
                    }

                    $deleteBtn.click(function () {
                        deleteFunc(el_id);
                    })

                    $(document).click(function(e){
                        e.stopPropagation();
                        TweenMax.to($deleteMsgBox, .3, {
                            opacity: 0, bottom: '20px', onComplete: function () {
                                $deleteMsgBox.remove();
                            }
                        });
                        $deleteMsgBox.parents('.mBuilder_controls').removeClass('active-gizmo');

                        if ($parent.hasClass('sc-control')){
                            $parent.removeClass('deleteEffect')
                        }else{
                            $deleteMsgBox.siblings('.wrap').removeClass('deleteEffect');

                        }
                    });

                }
            });
            $optionsHolder.removeClass('open');
            toggle = -1;

        }

        function deleteFunc(el_id){
            t.deleteModel(el_id);

            var p = $('div[data-mbuilder-id=' + el_id + ']').parent().closest('.mBuilder-element');

            // for tab
            var $elem = $('div[data-mbuilder-id=' + el_id + ']');
            if ($elem.hasClass('mBuilder-md_tab') || $elem.hasClass('mBuilder-md_modernTab')
                || $elem.hasClass('mBuilder-md_hor_tab') || $elem.hasClass('mBuilder-md_hor_tab2')) {
                var id = $elem.children('.wpb_tab ').attr('id');
                $('a[href="#' + id + '"]').parent().remove();
                $(window).resize();

            }

            $('div[data-mbuilder-id=' + el_id + ']').remove();
            if (p.attr('data-mbuilder-el') == 'vc_column') {
                if (!p.find('.mBuilder-element').length) {
                    p.find('.wpb_column').addClass('vc_empty-element');
                }
            }

            t.createPlaceholders();
        }
    });

    // Copy Element
    $body.on('click','.mBuilder_row_duplicate,.mBuilder-element .sc-duplicate,.tab .sc-duplicate',function(e){
        e.stopPropagation();

        TweenMax.set($(this).closest('.options-holder'), {height: 0, zIndex: -333,scale: .9, opacity: 0});
        closeAll();

        var t = builder;
        var el = $(this).closest('.mBuilder-element'),
            el_id = el.attr('data-mBuilder-id');
        do {
            var newID = Math.floor(100+(Math.random() * 300) + 1);
        }
        while (t.models.models.hasOwnProperty(newID));
        t.models.models[newID] = JSON.parse(JSON.stringify(t.models.models[el_id]));
        var $container = $('div[data-mbuilder-id='+el_id+']'),
            $containerPlaceholder = $container.next('.insert-between-placeholder,.insert-after-row-placeholder'),
            $newContainer = $container.clone().attr('data-mbuilder-id',newID),
            $newContainerPlaceholder = $containerPlaceholder.clone();
        $containerPlaceholder.after($newContainer);
        $newContainer.after($newContainerPlaceholder);
        if($(this).hasClass('mBuilder_row_duplicate')){
            var el = $('div[data-mBuilder-id='+newID+']');
            el.find('.mBuilder-element').each(function(){
                var child_id = $(this).attr('data-mBuilder-id');
                do {
                    var newID = Math.floor(100+(Math.random() * 300) + 1);
                }
                while (t.models.models.hasOwnProperty(newID));
                t.models.models[newID] = JSON.parse(JSON.stringify(t.models.models[child_id]));
                $(this).attr('data-mBuilder-id',newID);
            });
        }
        t.renderControls();
        t.setSortable();

    });

    // Element Alignments
    $body.on('click','.mBuilder-element .sc-alignment span',function(e){
        e.preventDefault();
        e.stopPropagation();

        var element = $(this).closest('.mBuilder-element');
        var id = element.attr('data-mbuilder-id');

        var regex = /(align=".*?")/g;
        t.models.models[id].attr = t.models.models[id].attr.replace(regex,'');
        if($(this).hasClass('left')){
            e.preventDefault();
            t.models.models[id].attr += ' align="left"' ;
            element.find('[class *= "md-align-"]')
                .removeClass('md-align-right')
                .removeClass('md-align-center')
                .addClass('md-align-left')
        }
        if($(this).hasClass('center')){
            e.preventDefault();
            t.models.models[id].attr += ' align="center"' ;
            element.find('[class *= "md-align-"]')
                .removeClass('md-align-right')
                .removeClass('md-align-left')
                .addClass('md-align-center')
        }
        if($(this).hasClass('right')){
            e.preventDefault();
            t.models.models[id].attr += ' align="right"' ;
            element.find('[class *= "md-align-"]')
                .removeClass('md-align-center')
                .removeClass('md-align-left')
                .addClass('md-align-right')
        }
    });

    // Hover on delete shortcode button
    $body.on({
        mouseenter:function(){
            $(this).closest('.mBuilder_controls').addClass('delete_hover');
        },
        mouseleave:function(){
            $(this).closest('.mBuilder_controls').removeClass('delete_hover');
        }},'.mBuilder-element .sc-delete');

    // open and close setting drop down menu
    $body.on('click','.mBuilder_row_controls .mBuilder_setting_panel,.mBuilder_row_layout,.sc-option',function(e){
        e.stopPropagation();
        var $this = $(this),
            $innerRow = $this.closest('.mBuilder-vc_row_inner');

        if (!$this.find('>.mBuilder_container, > .options-holder').hasClass('open') ){
            closeAll(this);

            if ($this.closest('.gizmo-container').length){
                $this.closest('.gizmo-container').addClass('active-gizmo');

            }else if ($this.hasClass('sc-option')){
                $this.closest('.mBuilder_controls').addClass('active-gizmo');
            }

            TweenMax.set($this.find('>.mBuilder_container, > .options-holder'), {height:'auto',zIndex:333});
            TweenMax.to($this.find('>.mBuilder_container, > .options-holder'),.2,
                {scale:1,opacity:1});

            $this.closest('div[class*=mBuilder-vc_column]').addClass('upper_zIndex');

            if ($this.hasClass('mBuilder_setting_panel')||$this.hasClass('mBuilder_row_layout')) {
                $this.closest('div[class*=vc_row]').addClass('upper_zIndex');
            }

            $this.find('>.mBuilder_container, > .options-holder').addClass('open');

            /* inner Row */
            if ($innerRow.length){
                $innerRow.addClass('upper_inner_row_zIndex');
                $innerRow.parents('.vc_row').addClass('upper_inner_row_zIndex');
                $innerRow.siblings('.mBuilder-element').addClass('lower_inner_row_zIndex')
            }

        }else{
            closeAll();
        }

        if($this.hasClass('mBuilder_row_layout')){
            $this.find('input').focus();
        }

    });

    $body.on('mouseleave','.mBuilder_row_controls .mBuilder_setting_panel,.mBuilder_row_layout',function(e){
        var $this = $(this),
            $innerRow = $(this).closest('.mBuilder-vc_row_inner');

        TweenMax.to($this.find('>.mBuilder_container, > .options-holder'),.2,
            {scale:.9,opacity:0,delay:.4,onComplete:function(){
                TweenMax.set($this.find('>.mBuilder_container, > .options-holder'), {height:0,zIndex:-333});
            }});
        $this.closest('div[class*=mBuilder-vc_column]').removeClass('upper_zIndex');

        if ($this.hasClass('mBuilder_setting_panel')||$this.hasClass('mBuilder_row_layout')){
            $this.closest('div[class*=vc_row]').removeClass('upper_zIndex');
        }

        $this.find('>.mBuilder_container, > .options-holder').removeClass('open');

        if($this.hasClass('mBuilder_row_layout')){
            $this.find('input').focus();
        }

        if ($innerRow.length){
            $innerRow.removeClass('upper_inner_row_zIndex');
            $innerRow.parents('.vc_row').removeClass('upper_inner_row_zIndex');
            $innerRow.siblings('.mBuilder-element').removeClass('lower_inner_row_zIndex')
        }

        toggle = -1;
    });

    // open shortcode setting panel on double click
    $body.on('dblclick','.sc-control',function(){
        $(this).find('.sc-setting').click();

    });
}


/**
 * @summary creates placeholders and droppable areas.
 *
 * @since 1.0.0
 */

mBuilder.prototype.createPlaceholders = function() {
    $('.insert-between-placeholder').remove();
    $('.insert-after-row-placeholder').remove();
    var containers = '';
    for (i in this.shortcodes){
        if(this.shortcodes[i].as_parent && this.shortcodes[i].as_parent.only)
            containers += "[data-mbuilder-el='"+this.shortcodes[i].as_parent.only+"'],";
    }
    containers = containers.slice(0,-1);
    $('<div/>').addClass('insert-between-placeholder').insertAfter($('.mBuilder-element').not('.vc_row').not(containers));
    $('.mBuilder-vc_column').each(function(){
        $('<div/>').addClass('insert-between-placeholder').insertBefore($(this).find('.wpb_wrapper:first-of-type .mBuilder-element:first-of-type').not(containers));
    });

    $('.insert-between-placeholder').each(function(){
        $(this).attr('data-index',$('div').index(this));
    });

    var rows = $('.vc_row').not('.vc_inner');
    $('<div/>').addClass('insert-after-row-placeholder').insertAfter(rows);
    $('<div/>').addClass('insert-after-row-placeholder').prependTo('.content-container');

    if(!$('.mBuilder-element').length){
        var content = $('<div><p>This page is empty. Drag a shortcode here.</p></div>'),
            btn = $('<span id="p-btn-addshortcode">Add Shortcode</span>');
        $('.insert-after-row-placeholder').html(content);
        content.append(btn);
        btn.click(function(){

            if (pixflow_customizerObj().$('.shortcodes-panel').css('display') != 'block' && pixflow_customizerObj().$('.shortcodes-panel').css('opacity') == 1) {
                pixflow_customizerObj().$('.shortcodes-panel-button').click();
            }

            if (pixflow_customizerObj().$('div.shortcodes-panel').css('display') != 'block') {
                pixflow_customizerObj().$('.shortcodes-panel-button').click();
            }else{
                pixflow_customizerObj().$('div.shortcodes-panel').css({'background-color':'#f77705'});
                pixflow_customizerObj().$('div.shortcodes-panel').stop().animate({'background-color':'#f1f1f1'},1000);

            }

        });

        $('.insert-after-row-placeholder').first().addClass('blank-page');
    }else{
        $('.insert-after-row-placeholder').first().removeClass('blank-page').off('click');
    }

    pixflow_footerPosition();
};


/**
 * @summary remove from models object.
 *
 * @param {integer} id
 * @since 1.0.0
 */

mBuilder.prototype.deleteModel = function(id) {

    pixflow_footerPosition();

    var t = this;
    for(var index in t.models.models) {
        var $el = $('div[data-mBuilder-id='+index+']'),
            $parent = $el.parent().closest('.mBuilder-element');
        if($parent.length){
            var parentId = $parent.attr('data-mBuilder-id');
            t.models.models[index].parentId = parentId;
        }
    }
    delete t.models.models[id];
    $.each(t.models.models, function(index, value) {
        if(value['parentId'] == id ){
            t.deleteModel(index);
        }
    });
    $('body').addClass('changed');
};


/**
 * @summary apply row layout changes.
 *
 * @param {string} exp - layout expression example: (3/12)+(3/12)+(3/12)+(3/12)
 * @param {object} row - jQuery Object
 * @since 1.0.0
 */

mBuilder.prototype.changeRowLayout = function(exp,row) {
    var t = this;
    if(exp.match(/([0-9]+)\/12/g)) {
        var columns = exp.match(/([0-9]+)\/12/g);
        var sum = 0;
        for (i in columns) {
            var size = parseInt(columns[i].replace('/12', ''));
            sum += size;
        }
        if (sum > 12) {
            alert('Sum of all columns is greater than 12 columns.');
            return;
        }else if(sum < 12){
            alert('Sum of all columns is less than 12 columns.');
            return;
        }
        var i = 0;
        row.find('[data-mbuilder-el="vc_column"],[data-mbuilder-el="vc_column_inner"]').first()
            .siblings('[data-mbuilder-el="vc_column"],[data-mbuilder-el="vc_column_inner"]').addBack().each(function () {
            if (columns[i]) {
                var size = columns[i].replace('/12', '');
                $(this).find('> .vc_column_container').removeClass (function (index, css) {
                    return (css.match (/(^|\s)col-sm-[0-9]+/g) || []).join(' ');
                }).addClass('col-sm-' + size);
                if(t.models.models[$(this).attr('data-mbuilder-id')].attr && t.models.models[$(this).attr('data-mbuilder-id')].attr !='') {
                    t.models.models[$(this).attr('data-mbuilder-id')].attr = t.models.models[$(this).attr('data-mbuilder-id')].attr.replace(/width=["'].*?["']/g, 'width="' + columns[i] + '"');
                }else{
                    t.models.models[$(this).attr('data-mbuilder-id')].attr = ' width="' + columns[i] + '"';
                }
                i++;
            } else {
                var el_id = $(this).attr('data-mbuilder-id'),
                    $el = $('div[data-mBuilder-id='+el_id+']'),
                    $lastCol = row.find('> .wrap > .mBuilder-vc_column, > .wrap > .mBuilder-vc_column_inner').eq(columns.length-1).find('.vc_column-inner > .wpb_wrapper');
                $el.find('.vc_column-inner > .wpb_wrapper > .mBuilder-element').each(function(){
                    var $obj = $(this).appendTo($lastCol);
                    $obj.after('<div class="insert-between-placeholder" data-index=""></div>');
                });
                t.deleteModel(el_id);
                $(this).remove();
            }
        });

        if (i < columns.length) {
            if(!t.lock) {
                var j = i;
                t.lock = true;
                for (i; i < columns.length; i++) {
                    if(row.hasClass('vc_inner')){
                        t.buildShortcode(row, 'vc_column_inner', {width: columns[i]}, function () {
                            j++;
                            if (j == columns.length) {
                                t.lock = false;
                            }
                        });
                    }else{
                        t.buildShortcode(row, 'vc_column', {width: columns[i]}, function () {
                            j++;
                            if (j == columns.length) {
                                t.lock = false;
                            }
                        });
                    }
                }
            }
        }

    }else{
        alert('You entered wrong pattern, try premade patterns instead.');
    }
};

/**
 * @summary open shortcode setting panel.
 *
 * @param {string} title
 * @param {string} customClass
 * @param {string} text
 * @param {string} btn1
 * @param {function} callback1 - optional
 * @param {string} btn2 - optional
 * @param {function} callback2 - optional
 * @param {function} closeCallback - optional
 * @since 1.0.0
 */
mBuilder.prototype.mBuilder_shortcodeSetting = function(title, customClass, text, btn1, callback1, btn2, callback2, closeCallback){
    "use strict";
    var t = this;
    if($('.setting-panel-wrapper').length){
        $('.setting-panel-wrapper .setting-panel-title').html(title);
        $('.setting-panel-wrapper .setting-panel-text').html(text);
        $('.setting-panel-wrapper .setting-panel-container').attr('class','').addClass('setting-panel-container '+customClass);
        $('.setting-panel-wrapper .setting-panel-btn1').html(btn1);
        $('.setting-panel-wrapper .setting-panel-btn2').html(btn2);
        var $messageBox = $('.setting-panel-wrapper'),
            $btn1;
    }else {
        var $messageBox = $('' +
                '<div class="setting-panel-wrapper">' +
                '   <div class="setting-panel-container ' + customClass + '">' +
                '       <div class="setting-panel-close"/>' +
                '       <div class="setting-panel-title">' + title + '</div>' +
                '       <div class="setting-panel-text">' + text + '</div>' +
                '       <button class="setting-panel-btn1">' + btn1 + '</button>' +
                '   </div>' +
                '</div>').appendTo('body'),
            $btn1;
    }
    $messageBox.animate({opacity:1},200);
    $messageBox.find('.setting-panel-container').draggable();
    this.settingPanel = $messageBox;
    $btn1 = $messageBox.find('.setting-panel-btn1');
    $btn1.off('click');
    $btn1.click(function(e){
        e.preventDefault();
        if(typeof callback1 == 'function') {
            callback1();
        }
    });
    if(btn2){
        if($messageBox.find('.setting-panel-btn2').length){
            var $btn2 = $messageBox.find('.setting-panel-btn2');
        }else{
            var $btn2 = $('<button class="setting-panel-btn2">'+ btn2 +'</button>').insertBefore($btn1);
        }
        $btn2.off('click');
        $btn2.click(function(e){
            e.preventDefault();
            if(typeof callback2 == 'function'){
                callback2();
            }
        });
    }

    var $close = $messageBox.find('.setting-panel-close');
    $close.off('click');
    $close.click(function(e){
        e.preventDefault();
        if(typeof closeCallback == 'function'){
            closeCallback();
        }
        t.mBuilder_closeShortcodeSetting();
    });
};


/**
 * @summary close shortcode setting panel.
 *
 * @since 1.0.0
 */

mBuilder.prototype.mBuilder_closeShortcodeSetting = function(){
    "use strict";
    $('.sp-container').remove();
    $('.setting-panel-wrapper').fadeOut(300,function(){
        $(this).remove();
    })
};


/**
 * @summary get Model
 *
 * @param {integer} id - model ID
 *
 * @return {object} - model
 * @since 1.0.0
 */

mBuilder.prototype.getModelParams = function(id){
    return this.models.models[id];
};


/**
 * @summary Add Shortcode Panel to the customizer side
 *
 * @since 1.0.0
 */

mBuilder.prototype.renderShortcodePanel = function(){

    window.top.$("div.shortcodes-panel, .shortcode-button-holder").remove();
    var shortcodesPanel = $('<div class="shortcodes-panel"></div>');
    var shortcodesPanelButton =$('<div class="shortcode-button-holder"> <div class="shortcodes-panel-button">SHORTCODES</div></div>');
    var shortcodeContainer = $('<div class="shortcodes-container"></div>');
    var search = $('<input class="qsearch" name="qsearch" placeholder="search" value=""/>');
    var searchResult =  $('<div class="search-result"></div>');

    shortcodesPanel.append(shortcodeContainer);
    shortcodeContainer.append(search);
    search.after(searchResult);

    window.top.$('#customize-controls').append(shortcodesPanel);
    window.top.$('#customize-controls').append(shortcodesPanelButton);

    var typingTimer;                //timer identifier
    var doneTypingInterval = 500,
        firstVal = "";

    search.keyup(function(e){

        var searchVal = $(this).val().toLowerCase();

        if (firstVal != searchVal ) {
            window.top.$('.category-container .shortcodes').removeClass('active');
            window.top.$('.shortcodes-panel .category-container').removeClass('show');

            clearTimeout(typingTimer);
            typingTimer = setTimeout(function () {
                if (searchVal != "") {
                    window.top.$('.category-container .shortcodes[data-name*="' + searchVal + '"]').addClass('active');
                    window.top.$('.category-container .shortcodes[data-name*="' + searchVal + '"]').parents('.category-container').addClass('show')
                } else {
                    //searchResult;
                    window.top.$('.shortcodes').addClass('active');
                    window.top.$('.shortcodes-panel .category-container').addClass('show');
                }
            }, doneTypingInterval);
        }
        firstVal = searchVal;
    });

    shortcodesPanel.niceScroll({
        horizrailenabled: false,
        cursorcolor: "rgba(204, 204, 204, 0.2)",
        cursorborder: "1px solid rgba(204, 204, 204, 0.2)",
        cursorwidth: "2px",
        enablescrollonselection: false
    });
	

    if ($('body').is('.blog') || $('body').is('.woocommerce-page')){
        shortcodesPanel.html('<div class="no-shortcode"><div class="tip-image"></div>' +
            '<div class="heading">You Don\'t Need Shortcodes</div>There\'s no need to use shortcodes in blog and shop pages, because they have their own templates. To add contents to these pages, you should use post or product in WordPress dashboard.</div>');
    }else{


        var categoryList = new Array();
        var allowed_shortcodes = ['vc_row','vc_empty_space'];

        for(var i in this.shortcodes) {
            var base =  this.shortcodes[i].base;
            if(base != undefined){
                if (base.substr(0, 3) != 'md_' && allowed_shortcodes.indexOf(base) == -1) {
                    this.shortcodes[i].display = 'none';
                }
            }
            if (this.shortcodes[i] && this.shortcodes[i].as_parent) {
                if (this.shortcodes[this.shortcodes[i].as_parent['only']]) {
                    this.shortcodes[this.shortcodes[i].as_parent['only']].display = 'none';
                }
            }
        }

        var num=0;
        var regex = /(http.*)x=([0-9-center]+)[|]y=([0-9-center]+)/i;

        //create shortcodes
        for(var i in this.shortcodes) {
            if(i == 'vc_column_text' || (this.shortcodes[i].display && this.shortcodes[i].display=='none')) {
                continue;
            }



            var  category = this.shortcodes[i].category;
            if ($.inArray(category,categoryList) < 0 &&  category !== undefined ){
                categoryList.push(category);
                var catTemp = category.split(' ').join('-').toLowerCase();
                shortcodeContainer.append('<div class="'+catTemp+' show category-container"><h6>'+category+'</h6></div>');
            }
            if (category === undefined){
                category = "undefined";
            }
            var shortcodeCat = category.split(' ').join('-').toLowerCase(),
                name =this.shortcodes[i].name;
            name = name.toLowerCase();
            $tmpName = name.replace(new RegExp(' ', 'g') ,  '-');
            shortcodeContainer.find('.category-container.'+shortcodeCat).append('<div class="shortcodes active ' + category + '" id="' + this.shortcodes[i].base + '" data-name="'+name+'"><div class="inner-container" ><span class="icon mdb-' + $tmpName +'"></span>' + this.shortcodes[i].name + '</div></div>');

        }

    }

    //shortcode panel button click
    var toggled = 1,
        shortcodesPanelButtonAnim,
        shortcodesPanelAnim,
        windowTop = window.top;

    windowTop.$('.shortcodes-panel-button').off('click')
    windowTop.$('.shortcodes-panel-button').click(function (e) {
        e.preventDefault();
        if (toggled == 1) {

            try {
                shortcodesPanelAnim.pause();
                shortcodesPanelButtonAnim.pause();

            } catch (e) {

            }

            TweenMax.to(windowTop.$('#customize-controls .wp-full-overlay-sidebar-content,#customize-controls .customizer-header'), .5, {
                scale: 0.7, opacity: 0, onComplete: function () {
                }
            });

            windowTop.$('#customize-controls .divider').css('display', 'block');

            shortcodesPanel.stop().css({
                height: $(window).height() - 30,
                'top': '0',
                //'overflow': 'auto',
                display: 'block',
                transform: 'scale(1.2,1.2)'
            });

            shortcodesPanelAnim = TweenMax.to(shortcodesPanel, .3, {scale: 1, opacity: 1, delay: .2});
            shortcodesPanel.animate({scrollTop: 0}, 600);
            shortcodesPanel.find('input.qsearch').focus();
            windowTop.$('.shortcode-button-holder .shortcodes-panel-button').text('SITE SETTINGS');

            window.setTimeout(function(){
                windowTop.$(".introjs-hint[data-step!='1']").fadeOut('fast');
            },1000);
        }
        else {
            try {
                shortcodesPanelAnim.pause();
                shortcodesPanelButtonAnim.pause();
            } catch (e) {
            }

            shortcodesPanelAnim = TweenMax.to(shortcodesPanel, .4, {
                scale: 1.1, opacity: 0, onComplete: function () {
                    shortcodesPanel.css('display', 'none');
                    windowTop.$('#customize-controls .divider').css('display', 'none');

                }
            });

            TweenMax.to(windowTop.$('#customize-controls .wp-full-overlay-sidebar-content,#customize-controls .customizer-header'), .5, {
                scale: 1, opacity: 1, onComplete: function () {

                }
            });

            shortcodesPanel.find('input.qsearch').val('').keyup().focus();

            windowTop.$('.shortcode-button-holder .shortcodes-panel-button').text('SHORTCODES');

            window.setTimeout(function(){
                windowTop.$(".introjs-hint[data-step!='1']").fadeOut('fast');
            },1000);
        }
        toggled *= -1;
    });

    var windowHeight = $(window).height();
    $(window).resize(function (e) {

        setTimeout(function(){
            // check if event  doesn't call from jquery
            if (e.originalEvent && windowHeight != $(window).height() ){
                toggled = -1;
                windowTop.$('.shortcodes-panel-button').click();
            }
        },500);

    });

    var shortcode = null;
    var placeholder = null;
    var t = this;
    var lastObj = null;
    var fly = null;

    var d = $('<div style="position: absolute; height: 5px; z-index: 999999;"></div>').appendTo('body'),
        direction = 'down',
        overEmpty = false;
    windowTop.$('.shortcodes').draggable({
        iframeFix:true,
        appendTo: "body",
        helper: "clone",
        zIndex:999999,
        cursorAt: { top: 20,left:50},

        start: function( event, ui ) {
            clearInterval(fly);
            shortcode =$(this).attr('id');
            $(this).css('visibility','hidden')

        },
        drag: function( event, ui ) {
            clearInterval(fly);
            if(event.clientY < 100){
                fly = setInterval(function(){
                    $(window).scrollTop($(window).scrollTop()-50)
                },50);
            }else if(event.clientY > $(window).height()-50){
                fly = setInterval(function() {
                    $(window).scrollTop($(window).scrollTop() + 50)
                },50);
            }

            var el = document.elementFromPoint(event.clientX-300,event.clientY-50);
            if(el){
                if(el == d.get(0)) return;
                overEmpty = false;
                var obj = $(el).closest('.mBuilder-element,.vc_inner');
                if(obj.length
                    && !obj.hasClass('vc_row') && !obj.hasClass('mBuilder-vc_column')
                    && !obj.hasClass('mBuilder-vc_column_inner')){
                    if(t.containers[obj.attr('data-mbuilder-el')]){
                        if(!obj.find('.mBuilder-element').length) {
                            overEmpty = true;
                        }else{
                            d.css({border: '', borderTop: '4px solid #8fcbff'});
                        }
                    }else {
                        d.css({border: '', borderTop: '4px solid #8fcbff'});
                    }

                }else{
                    if(obj.hasClass('mBuilder-vc_column') || obj.hasClass('mBuilder-vc_column_inner')) {
                        if (obj.find('> .vc_empty-element').length) {
                            var obj = obj.find('> .vc_empty-element');
                            overEmpty = true;
                        } else {
                            if(!obj.hasClass('mBuilder-vc_column_inner')) {
                                d.css({border: '', borderTop: '4px solid #43dc9d'});
                            }else{
                                d.css({border: '', borderTop: '4px solid #8fcbff'});
                                obj = obj.closest('.vc_inner');
                            }
                        }
                    }else if(obj.hasClass('vc_row')) {
                        if(!obj.hasClass('vc_inner')) {
                            d.css({border: '', borderTop: '4px solid #43dc9d'});
                        }else{
                            d.css({border: '', borderTop: '4px solid #8fcbff'});
                            obj = obj.closest('.vc_inner');
                        }
                    }else {
                        var obj = $(el).closest('.blank-page');
                        if (obj.length) {
                            lastObj = obj;
                            var objTop = obj.offset().top + 100,
                                objLeft = obj.offset().left + 100,
                                objHeight = obj.outerHeight() - 200,
                                objWidth = obj.outerWidth() - 200,
                                objHalf = objTop + objHeight/2;
                            d.css({'top': objTop, 'left': objLeft,height:objHeight,width:objWidth,background:'rgba(136,206,255,0.4)',border:'solid 2px #8fcbff'});
                            return;
                        }
                        if (lastObj) {
                            lastObj.css({'transform': ''})
                        }
                        lastObj = null;
                        d.css({width: '', border:''});
                        return;
                    }

                }

                var objTop = obj.offset().top,
                    objLeft = obj.offset().left,
                    objHeight = obj.outerHeight(),
                    objWidth = obj.outerWidth(),
                    objHalf = objTop + objHeight/2;
                if(lastObj){
                    lastObj.css({'transform': ''})
                }
                if(!overEmpty) {
                    if (event.clientY + $(window).scrollTop() - 50 < objHalf) {
                        obj.not('.vc_row').css({'transform': 'translateY(5px)'});
                        d.css({'top': objTop, 'left': objLeft, width: objWidth,height:5,background:''});
                        direction = 'up';
                    } else {
                        obj.not('.vc_row').css({'transform': 'translateY(-5px)'});
                        d.css({'top': objTop + objHeight, 'left': objLeft, width: objWidth,height:5,background:''});
                        direction = 'down';
                    }
                }else{
                    d.css({'top': objTop, 'left': objLeft,height:objHeight,width:objWidth,background:'rgba(136,206,255,0.4)',border:'solid 2px #8fcbff'});
                }
                lastObj = obj;
            }else{
                if(lastObj){
                    lastObj.css({'transform': ''})
                }
                lastObj = null;
                d.css({width:'',border:''});
            }
        },
        stop:function(event,ui){
            clearInterval(fly);
            try {
                windowTop.$('.shortcodes-panel').getNiceScroll().resize();
            }catch(e){}
            $(this).css('visibility','visible')
            if(!lastObj || !lastObj.length){
                return;
            }
            if(direction=='up') {
                if(lastObj.hasClass('vc_row') && !lastObj.hasClass('vc_inner')) {
                    if(lastObj.prev('.insert-after-row-placeholder').length){
                        var p = lastObj.prev('.insert-after-row-placeholder');
                    }else{
                        var p = lastObj.prev().prev('.insert-after-row-placeholder');
                    }
                }else if(lastObj.hasClass('blank-page')){

                    var p = lastObj;
                }else if(lastObj.hasClass('vc_empty-element')) {
                    var p = lastObj.closest('.vc_column_container');
                }else if(t.containers[lastObj.attr('data-mbuilder-el')] && overEmpty){
                    var p = lastObj.find(t.containers[lastObj.attr('data-mbuilder-el')]);
                }else{
                    var p = lastObj.prev('.insert-between-placeholder');
                    if(!p.length){
                        var p = lastObj.parent().closest('.mBuilder-element').prev('.insert-between-placeholder');
                    }
                }
            }else{

                if(lastObj.hasClass('vc_row') && !lastObj.hasClass('vc_inner')) {
                    var p = lastObj.next('.insert-after-row-placeholder');
                }else if(lastObj.hasClass('blank-page')){
			
                    var p = lastObj;
                }else if(lastObj.hasClass('vc_empty-element')) {
                    var p = lastObj.closest('.vc_column_container');
                }else if(t.containers[lastObj.attr('data-mbuilder-el')] && overEmpty){
                    var p = lastObj.find(t.containers[lastObj.attr('data-mbuilder-el')]);
                }else{
                    var p = lastObj.next('.insert-between-placeholder');
                    if(!p.length){
                        var p = lastObj.parent().closest('.mBuilder-element').next('.insert-between-placeholder');
                    }
                }
            }
            placeholder =  p.get(0);
            d.css({'width':'',border:''});

            if(placeholder != null) {
                if(p.hasClass('insert-after-row-placeholder')){
                    t.buildShortcode(placeholder, 'vc_row',{},function(response){
                        if(shortcode=='vc_row'){
                            return;
                        }
                        t.buildShortcode(response.find('.vc_column_container'),shortcode );
                    });
                }else {
                    if (shortcode == 'vc_row') {
                        shortcode = 'vc_row_inner';
                    }
                    t.buildShortcode(placeholder, shortcode);
                }
            }
        }
    });

};


/**
 * @summary build shortcode in the placeholder that given.
 *
 * @param {object | string} placeholder - placeholder to drop shortcode.
 * @param {string} shortcode - shortcode type
 * @param {Object} atts - attributes of the shortcode
 * @param {function} callback - a callback function to call after build shortcode
 * @since 1.0.0
 */

mBuilder.prototype.buildShortcode = function(placeholder,shortcode,atts,callback) {
    if(placeholder && shortcode) {
        var t = this,
            atts = atts;
        $.ajax({
            type: 'post',
            url: mBuilderValues.ajax_url,
            data: {
                action: 'mBuilder_buildShortcode',
                nonce: mBuilderValues.ajax_nonce,
                shortcode: shortcode,
                act: 'build',
                attrs: JSON.stringify(atts),
                mbuilder_editor: true
            },
            success: function (response) {
                var attrs='';
                $.each(atts, function(index, value) {
                    attrs = attrs + ' ' + index + '="' +value+'"';
                });
                attrs = attrs.trim();
                response = t.setSettings(response,shortcode,placeholder,attrs);
                var id = response['id'];
                response = $(response['shortcode']);
                if($(placeholder).hasClass('vc_column_container') || $(placeholder).hasClass('vc_row') || $(placeholder).hasClass('vc_row_inner') || $(t.droppables).filter($(placeholder)).length) {
                    if($(placeholder).hasClass('vc_row') || $(placeholder).hasClass('vc_row_inner')) {
                        $(placeholder).find('>.wrap').append(response);
                    }else if($(placeholder).find('>.vc_column-inner>.wpb_wrapper').length){
                        $(placeholder).find('>.vc_column-inner>.wpb_wrapper').append(response);
                    }else{
                        if(!$(placeholder).find('.mBuilder-element').length){
                            $(placeholder).html('');
                        }
                        $(placeholder).append(response);
                    }

                    $(placeholder).removeClass('vc_empty-element');
                }else if($(placeholder).hasClass('vc_btn-content')) {
                    if(t.tabs[$(placeholder).closest('.mBuilder-element').attr('data-mbuilder-el')]){
                        var tab = $(t.tabs[$(placeholder).closest('.mBuilder-element').attr('data-mbuilder-el')][1]);
                        $(placeholder).closest('.mBuilder-element').find('ul').first().append(tab);
                        var unique = Math.floor(Math.random()* 1000000);
                        tab.find('a').attr('href','#tab-'+unique);
                        response.find('.wpb_tab').first().attr('id','tab-'+unique);
                    }
                    if($(placeholder).closest('.mBuilder-element').find('ul.px_tabs_nav').parent().length) {
                        $(placeholder).closest('.mBuilder-element').find('ul.px_tabs_nav').first().parent().append(response);
                    }else{
                        $(placeholder).closest('.mBuilder-element').find('.wpb_wrapper').first().append(response);
                    }
                    t.updateShortcode($(placeholder).closest('.mBuilder-element').attr('data-mbuilder-id'),$(placeholder).closest('.mBuilder-element').attr('data-mbuilder-el'), t.models.models[$(placeholder).closest('.mBuilder-element').attr('data-mbuilder-id')].attr,undefined,true);
                }else{
                    $(placeholder).before(response);
                    $(placeholder).siblings('.mBuilder-element').not('.vc_row, .vc_row_inner').each(function () {
                    });
                }
                t.createPlaceholders();
                t.specialShortcodes(shortcode,response);
                t.renderControls();
                t.setSortable();
                $(window).resize();
                if(typeof callback == 'function'){
                    callback(response);
                }
                $('body').addClass('changed');
            }
        })
    }
};


/**
 * @summary create shortcode model and add it to the models object
 *
 * @param {string} response - HTML response after build shortcode
 * @param {string} type - shortcode type
 * @param {string | object} parent - parent selector or jQuery object
 * @param {string} atts - attributes of the shortcode
 * @param {string} content - content of the shortcode
 *
 * @return {object} - model ID and HTML of the shortcode
 * @since 1.0.0
 */

mBuilder.prototype.setSettings = function(response,type,parent,atts,content){
    var rand,
        inModels = true,
        t=this;
    parent = $(parent);
    if(parent.hasClass && parent.hasClass('insert-between-placeholder')){
        parent = parent.closest('.mBuilder-element').attr('data-mbuilder-id');
    }else{
        parent = parent.attr('data-mbuilder-id');
    }
    var istab = false;
    for(var i in t.tabs){
        if(t.tabs[i][0] == type){
            istab = true;
        }
    }
    if(istab){
        var unique = Math.floor(Math.random()* 1000000);
        atts +=' tab_id=\''+unique+'\'';
    }

    if(type== 'md_text' && !content){
        content = $(response).find('.md-text-content').html();
    }
    while(inModels){
        rand = parseInt(Math.random() * 10000);
        if(typeof this.models.models[rand] == 'undefined'){
            t.models.models[rand] = {
                attr: atts,
                content: content,
                parentId: parent,
                type: type
            };
            inModels = false;
        }
    }

    var o = $(response).clone().attr('data-mbuilder-id', rand);
    o.find('.mBuilder-element').each(function(){
        var r = t.setSettings($(this)[0].outerHTML,$(this).attr('data-mBuilder-el'),$(this).parent().closest('.mBuilder-element'));
        $(r['shortcode']).insertAfter($(this));
        $(this).remove();
    });
    var result = [];
    result['shortcode'] = o[0].outerHTML;
    result['id'] = rand;

    o.remove();
    return result;
};


/**
 * @summary update shortcode model and rebuild it after edit
 *
 * @param {integer} id - ID of shortcode model
 * @param {string} shortcode - shortcode type
 * @param {string | object} attr - attributes of the shortcode
 * @param {string} content - content of the shortcode
 * @since 1.0.0
 */

mBuilder.prototype.updateShortcode = function(id,shortcode,attr,content,asParent){
    // Update elems object
    var t = this,
        attrs = '';
    if(typeof attr == 'object') {
        $.each(attr, function (index, value) {
            if (index == 'content') {
                return true;
            }
            value =  value.replace(new RegExp('"', 'g') ,  "'");
            attrs = attrs + index + '=' + '"' + value + '" ';
        });
    }else{
        attrs = attr;
    }
    if(!content) {
        var content = '';
        if (shortcode == 'vc_row') {
            content = $('[data-mbuilder-id="' + id + '"]').find('> .wrap').html();
        } else if (t.shortcodes[shortcode] && t.shortcodes[shortcode].as_parent && !t.tabs[shortcode]) {
            content = $('[data-mbuilder-id="' + id + '"]').find('> .wpb_content_element > .wpb_wrapper').html();
        } else if (t.containers[shortcode]) {
            content = $('[data-mbuilder-id="' + id + '"]').find(t.containers[shortcode]).html();
        } else {
            content = attr['content'];
        }

        t.models.models[id]['content'] = attr['content'];
    }else{
        t.models.models[id]['content'] = content;
    }
    t.models.models[id]['attr'] = attrs;


    attrs = typeof attr == 'object'?JSON.stringify(attr):attr;
    // Build shortcode
    $.ajax({
        type: 'post',
        url: mBuilderValues.ajax_url,
        data: {
            action: 'mBuilder_buildShortcode',
            nonce: mBuilderValues.ajax_nonce,
            shortcode: shortcode,
            act: 'rebuild',
            id: id,
            content:content,
            attrs: attrs,
            mbuilder_editor: true
        },
        success: function (response) {
            var container = $('.mBuilder-element[data-mbuilder-id='+id+']');
            var html = $(response);
            html.attr('data-mbuilder-id',id);
            var parent = container.parent().closest('.mBuilder-element');
            if(asParent || (
                    parent.length &&
                    t.shortcodes[parent.attr('data-mbuilder-el')] &&
                    t.shortcodes[parent.attr('data-mbuilder-el')].as_parent &&
                    t.shortcodes[parent.attr('data-mbuilder-el')].as_parent.only == container.attr('data-mbuilder-el')
                )
            ){
                var parentId = parent.attr('data-mbuilder-id');
                var type = parent.attr('data-mbuilder-el');
                $.ajax({
                    type: 'post',
                    url: mBuilderValues.ajax_url,
                    data: {
                        action: 'mBuilder_doShortcode',
                        nonce: mBuilderValues.ajax_nonce,
                        shortcode: t.shortcodeTag(parent, false),
                        mbuilder_editor: true
                    },
                    success: function (response) {
                        try {
                            parent.replaceWith(response);
                            var id = $(response).find('[data-mbuilder-el="' + shortcode + '"]').first().attr('data-mbuilder-id');
                            t.specialShortcodes(shortcode, $('[data-mbuilder-id="'+ id +'"]'));

                            for(var i in t.shortcodes){
                                if(t.shortcodes[i].as_parent && t.shortcodes[i].as_parent.only == shortcode){
                                    t.specialShortcodes(i, $('[data-mbuilder-id="'+ id +'"]').closest('[data-mbuilder-el="'+ i +'"]'));
                                }
                            }

                            t.renderControls();
                            t.setSortable();
                            $(window).resize();
                        }catch(e){
                            console.log(e);
                        }
                    }
                })
            }else{
                try {
                    container.replaceWith(html);
                    t.specialShortcodes(shortcode,html);
                    t.renderControls();
                    $(window).resize();
                }catch(e){
                    console.log(e);
                }
            }
            t.setSortable();
            $('body').addClass('changed');
//		setTimeout(function(){ 
//		$basecon =	t.models.models[id].content  ;
//		console.log($basecon);
//		$basecon = $basecon.replace(new RegExp('<p>&nbsp;<br></p>', 'g') ,  '');
//					$("div[data-mbuilder-id="+ id +"]").find('.inline-editor').eq(0).html($basecon);	
//					t.models.models[id].content = $basecon;
//						} , 100 ) ;
			
        }
    })

};

/**
 * @summary generate shortcodeTag from DOM element
 *
 * @param {object} obj - DOM element | jQuery element
 * @param {bool} onlyChildren - if true it returns just children shortcodeTags
 * @param {int} depth - used in recursive calls
 *
 * @return {string} - shortcodeTag
 * @since 1.0.0
 */
mBuilder.prototype.shortcodeTag = function(obj,onlyChildren,depth){
    var t = this,
        el = $(obj),
        id = el.attr('data-mbuilder-id');

    if(!el.length){
        return '';
    }
    if(!depth){
        depth = 0;
    }
    var model = t.models.models[id];
    model.attr = model.attr!=undefined?model.attr:'';
    model.content = model.content!=undefined?model.content:'';
    if(!onlyChildren) {
        var tag = '[' + model.type + ' ' + model.attr + ' mbuilder-id="'+id+'"]' + model.content;
    }
    depth++;

    el.find('.mBuilder-element').each(function(){
        for(var i in t.compiledTags){
            if(t.compiledTags[i] == this) return;
        }
        tag += t.shortcodeTag(this,false,depth);
    });
    t.compiledTags.push(el.get(0));
    depth--;

    if(!onlyChildren) {
        tag += '[/' + model.type + ']';
    }
    if(depth == 0){
        t.compiledTags = [];
    }
    return tag;
};

/**
 * @summary save contents and shortcodes to the database
 *
 * @since 1.0.0
 */

mBuilder.prototype.saveContent = function(){
    var t = this;
    $('body').addClass('content-saving');
    for(var index in t.models.models) {
        var $el = $('div[data-mBuilder-id='+index+']'),
            $parent = $el.parent().closest('.mBuilder-element');
        if(!$el.length){
            delete(t.models.models[index]);
        }
        if($parent.length){
            var parentId = $parent.attr('data-mBuilder-id');
            t.models.models[index].parentId = parentId;
        }
    }
    // Calculate orders
    $('.mBuilder-element').each(function(){
        var $el = $(this),
            id = $el.attr('data-mBuilder-id');

        var order = 1;
        $el.siblings( ".mBuilder-element" ).addBack().each(function(){
            t.models.models[$(this).attr('data-mbuilder-id')]['order']=order++;
        });
    });
    $.ajax({
        type: 'post',
        url: mBuilderValues.ajax_url,
        data: {
            action: 'mBuilder_saveContent',
            nonce: mBuilderValues.ajax_nonce,
            models:  JSON.stringify( t.models['models'] ),
            id:  $('meta[name="post-id"]').attr('content'),
            mbuilder_editor: true
        },
        success: function (response) {
            //console.log(response);
            $('body').removeClass('content-saving changed');
        }
    });
};


/**
 * @summary Apply dependencies to the shortcode setting panel
 *
 * @since 1.0.0
 */

mBuilder.prototype.dependencyInjection = function(){
    var tabs = this.settingPanel.find('#mBuilderTabs > ul li');
    this.settingPanel.find('[data-mBuilder-dependency]').each(function(){
        var json = JSON.parse($(this).attr('data-mBuilder-dependency'));
        var el = $(this);
        var depend = $('[name='+json.element+']');
        if(depend.attr('type') != 'hidden') {
            depend.change(function () {
                if(typeof json.value != 'object'){
                    json.value = [json.value];
                }
                if ($.inArray($(this).val(), json.value) != -1 && $(this).closest('.vc_column').css('display') == 'block') {
                    el.css('display', 'block');
                } else {
                    el.css('display', 'none');
                }
                el.find('select,input').trigger('change');
                tabs.each(function(){
                    var id = $(this).attr('aria-controls');
                    var result = false;
                    var element = document.getElementById(id);
                    $(element).find('>.vc_column').each(function(){
                        if($(this).css('display')=='block'){
                            result=true;
                            return false;
                        }
                    });
                    if(!result){
                        $(this).css('display','none')
                    }else{
                        $(this).css('display','block')
                    }
                });
            }).trigger('change');
        }else{
            depend.siblings('[data-name='+depend.attr('name')+']').change(function(){
                if(typeof json.value != 'object'){
                    json.value = [json.value];
                }
                if ($.inArray(depend.val(), json.value) != -1 && depend.closest('.vc_column').css('display') == 'block') {
                    el.css('display', 'block');
                } else {
                    el.css('display', 'none');
                }
                el.find('select,input').trigger('change');
                tabs.each(function(){
                    var id = $(this).attr('aria-controls');
                    var result = false;
                    var element = document.getElementById(id);
                    $(element).find('>.vc_column').each(function(){
                        if($(this).css('display')=='block'){
                            result=true;
                            return false;
                        }
                    });
                    if(!result){
                        $(this).css('display','none')
                    }else{
                        $(this).css('display','block')
                    }
                });
            }).trigger('change');
        }
    })
};


/**
 * @summary media panel for the image controller in the shortcode setting panel
 *
 * @since 1.0.0
 */

mBuilder.prototype.mediaPanel = function() {
    // Set all variables to be used in scope
    var frame;

    // ADD IMAGE LINK
    $('body').on('click', '.mBuilder-upload-img.single', function (event) {

        event.preventDefault();
        $(this).addClass('active-upload');
        // If the media frame already exists, reopen it.
        if (frame) {
            frame.open();
            return;
        }

        // Create a new media frame
        frame = window.top.wp.media({
            title: 'Select or Upload Media Of Your Chosen Persuasion',
            button: {
                text: 'Use this media'
            },
            multiple: false  // Set to true to allow multiple files to be selected
        });

        var t = this;
        // When an image is selected in the media frame...
        frame.on('select', function () {
            var $this = $('.mBuilder-upload-img.single.active-upload');
            // Get media attachment details from the frame state
            var attachment = frame.state().get('selection').first().toJSON();

            // Send the attachment URL to our custom image input field.
            $this.css('background-image', 'url("' + attachment.url + '")').css('background-size', 'contain');

            // Send the attachment id to our hidden input
            $this.find('input').val(attachment.id);

            $this.find('.remove-img').removeClass('mBuilder-hidden');
            $('.mBuilder-upload-img.single').removeClass('active-upload');

        });

        // Finally, open the modal on click
        frame.open();
    });

    // DELETE IMAGE LINK
    $('body').on('click', '.mBuilder-upload-img.single .remove-img', function (event) {

        event.preventDefault();
        event.stopPropagation();
        // Clear out the preview image

        $(this).closest('.mBuilder-upload-img').css({'background-image':'','background-size':''});

        $(this).parent().removeClass('has-img');
        $(this).addClass('mBuilder-hidden');

        // Delete the image id from the hidden input
        $(this).siblings('input').val('');

    });
};


/**
 * @summary multi media panel for the multi image controller in the shortcode setting panel
 *
 * @since 1.0.0
 */

mBuilder.prototype.multiMediaPanel = function() {
    // Set all variables to be used in scope
    var frame;

    // ADD IMAGE LINK
    $('body').on('click', '.mBuilder-upload-imgs .mBuilder-upload-img', function (event) {

        event.preventDefault();

        // If the media frame already exists, reopen it.
        /* if (false) {
         frame.open();
         return;
         }*/

        // Create a new media frame
        frame = window.top.wp.media({
            title: 'Select or Upload Media Of Your Chosen Persuasion',
            button: {
                text: 'Use this media'
            },
            multiple: true  // Set to true to allow multiple files to be selected
        });

        var t = this,
            $container = $(t).parent();
        // When an image is selected in the media frame...
        frame.on('select', function () {

            // Get media attachment details from the frame state
            //var attachment = frame.state().get('selection').first().toJSON();
            var attachment = frame.state().get('selection').toJSON();
            var attachments = '';
            $container.find('.mBuilder-upload-img').remove();
            for(var i=0;i<attachment.length;i++){
                attachments = attachments  + attachment[i]['id'] + ',';
                $container.append('<div data-id="'+attachment[i]['id']+'" class="mBuilder-upload-img has-img" style="background-image: url('+ attachment[i].url +')"><span class="remove-img">X</span></div>');
            }
            $container.append('<div class="mBuilder-upload-img"><span class="remove-img mBuilder-hidden">X</span></div>');
            attachments = attachments.slice(0, -1);
            // Send the attachment id to our hidden input
            $container.find('input').val(attachments);
        });

        // Finally, open the modal on click
        frame.open();
    });


    // DELETE IMAGE LINK
    $('body').on('click', '.mBuilder-upload-imgs .mBuilder-upload-img .remove-img', function (event) {
        var t = this,
            $container = $(t).parent().parent(),
            $this = $(this).parent();
        event.preventDefault();
        event.stopPropagation();
        // Delete the image id from the hidden input
        var val = $container.find('input').val(),
            valarr = val.split(","),
            index = valarr.indexOf($this.attr('data-id'));
        if (index > -1) {
            valarr.splice(index, 1);
        }
        $container.find('input').val(valarr.join());
        // Clear out the preview image
        $this.remove();
    });
};


/**
 * @summary Google Font Controller in the Shortcode setting panel
 *
 * @since 1.0.0
 */

mBuilder.prototype.googleFontPanel = function() {
    function generateInputVal(paramName){
        var $fontFamily = $('.google-fonts-families[data-input="'+paramName+'"]'),
            $fontStyle = $('.google-fonts-styles[data-input="'+paramName+'"]'),
            $input = $('input[name="'+paramName+'"]'),
            fontFamily = 'font_family:'+encodeURIComponent($fontFamily.val()),
            fontStyle = 'font_style:'+encodeURIComponent($fontStyle.val());
        $input.val(fontFamily+'|'+fontStyle);
    }
    $('body').on('change', '.google-fonts-families', function (event) {
        // check if event  doesn't call from jquery
        if (!event.originalEvent){
            return;
        }
        var $this = $(this);
        $('.google-fonts-styles[data-input="'+$this.attr("data-input")+'"]').html('<option>Loading...</option>');
        $.ajax({
            type: 'post',
            url: mBuilderValues.ajax_url,
            data: {
                action: 'pixflow_loadFontStyles',
                nonce: mBuilderValues.ajax_nonce,
                fontKey: $this.find(":selected").attr('data-font-id'),
                value: '',
                mbuilder_editor: true
            },
            success: function (response) {
                $('.google-fonts-styles[data-input="'+$this.attr("data-input")+'"]').html(response);
                generateInputVal($this.attr('data-input'));
            }
        });
    });
    $('body').on('change', '.google-fonts-styles', function (event) {
        generateInputVal($(this).attr('data-input'));
    });
};


/**
 * @summary call user functions that sets to call after each shortcode build or rebuild
 *
 * @param {string} type - shortcode type
 * @param {object} obj - jQuery object of shortcode
 * @since 1.0.0
 */

mBuilder.prototype.specialShortcodes = function(type,obj) {
    if(typeof this[type+"Shortcode"] == 'function') {
        this[type+"Shortcode"](obj);
    }
    obj.parents('.mBuilder-element[data-mbuilder-el="md_accordion_tab"]').find('h3.ui-state-active').siblings('.wpb_accordion_content').css('height','');
    obj.parents('.mBuilder-element[data-mbuilder-el="md_toggle_tab"]').find('h3.ui-state-active').siblings('.wpb_toggle_content').css('height','');
    obj.parents('.mBuilder-element[data-mbuilder-el="md_toggle_tab2"]').find('h3.ui-state-active').siblings('.wpb_toggle_content').css('height','');
};

mBuilder.prototype.md_tabsShortcode = function(obj){

    obj.find('.px_tabs_nav > li').click(function(){
        var id = $(this).find('> a').attr('href'),
            num = $(this).position().left;
        $(id).next().css({left:num});
    });

    obj.find('ul.ui-tabs-nav').sortable({
        cursor: "move",
        items: "li:not(.unsortable)",
        delay: 100,
        axis: "x",
        zIndex: 10000,
        tolerance: "intersect",
        update: function( event, ui ) {
            $('body').addClass('changed');
            var prev = ui.item.prev();
            var prevId = prev.find('a').attr('href');
            var id = ui.item.find('a').attr('href');
            if(prevId) {
                $(id).parent().insertAfter($(prevId).parent());
            }else{
                $(id).parent().insertAfter($(id).parent().parent().find('ul').first());
            }
        }
    });
}

mBuilder.prototype.md_modernTabsShortcode = function(obj){
    obj.find('.px_tabs_nav > li').click(function(){
        var id = $(this).find('> a').attr('href'),
            num = $(this).position().left;
        $(id).next().css({left:num});
    });

    setTimeout(function(){
        obj.find('.px_tabs_nav > li').first().click();
    },500);

    obj.find('ul.ui-tabs-nav').sortable({
        cursor: "move",
        items: "li:not(.unsortable)",
        delay: 100,
        axis: "x",
        zIndex: 10000,
        tolerance: "intersect",
        update: function( event, ui ) {
            $('body').addClass('changed');
            var prev = ui.item.prev();
            var prevId = prev.find('a').attr('href');
            var id = ui.item.find('a').attr('href');
            if(prevId) {
                $(id).parent().insertAfter($(prevId).parent());
            }else{
                $(id).parent().insertAfter($(id).parent().parent().find('ul').first());
            }
        }
    })
}

mBuilder.prototype.md_hor_tabsShortcode = function(obj){
    obj.find('.px_tabs_nav > li').click(function(){
        var id = $(this).find('> a').attr('href'),
            num = $(this).position().top+15;
        $(id).next().css({top:num});
    });
    obj.find('ul.ui-tabs-nav').sortable({
        cursor: "move",
        items: "li:not(.unsortable)",
        delay: 100,
        axis: "y",
        zIndex: 10000,
        tolerance: "intersect",
        update: function( event, ui ) {
            $('body').addClass('changed');
            var prev = ui.item.prev();
            var prevId = prev.find('a').attr('href');
            var id = ui.item.find('a').attr('href');
            if(prevId) {
                $(id).parent().insertAfter($(prevId).parent());
            }else{
                $(id).parent().insertAfter($(id).parent().parent().find('ul').first());
            }
        }
    })
}

mBuilder.prototype.md_hor_tabs2Shortcode = function(obj){
    obj.find('.px_tabs_nav > li').click(function(){
        var id = $(this).find('> a').attr('href'),
            num = $(this).position().top+20;
        $(id).next().css({top:num});
    });

    obj.find('ul.ui-tabs-nav').sortable({
        cursor: "move",
        items: "li:not(.unsortable)",
        delay: 100,
        axis: "y",
        zIndex: 10000,
        tolerance: "intersect",
        update: function( event, ui ) {
            $('body').addClass('changed');
            var prev = ui.item.prev();
            var prevId = prev.find('a').attr('href');
            var id = ui.item.find('a').attr('href');
            if(prevId) {
                $(id).parent().insertAfter($(prevId).parent());
            }else{
                $(id).parent().insertAfter($(id).parent().parent().find('ul').first());
            }
        }
    })
}

/**
 * @summary get value attribite from model attributes
 *
 * @since 1.0.0
 */

mBuilder.prototype.getModelattr = function(modelID,attr) {
    var t = this,
        attrs = t.models.models[modelID].attr ;
    var re = new RegExp(attr + '="([.\\s\\S]*?)[^\\\\]"','gm');
    var str = attrs;
    var m;
    if ((m = re.exec(str)) !== null) {
        return m[1];
    }else{
        return false;
    }
};

/**
 * @summary set value to model attribute
 *
 * @since 1.0.0
 */
mBuilder.prototype.setModelattr = function(modelID,attr,value) {
    var t = this,
        attrs = t.models.models[modelID].attr ;
    var re = new RegExp(attr + '="([.\\s\\S]*?)[^\\\\]"','gm');
    var str = attrs;
    var m;
    if ((m = re.exec(str)) !== null) {
        var find = new RegExp(attr + '="([.\\s\\S]*?)[^\\\\]"','gm');
        var replace = attr + '="'+value+'" ';
        attrs = str.replace(find, replace);
    }else{
		attrs = attrs + ' ' + attr + '="' + value + '" ';
    }

    t.models.models[modelID].attr = attrs;
};

mBuilder.prototype.makeLinksTargetSelf = function(){
    var $links = $('.layout-container header nav a,' +
        '.layout-container header a.logo,' +
        '.layout-container footer a,' +
        '.layout-container .sidebar a,' +
        '.layout-container .portfolio a.button,' +
        'header .icons-pack .elem-container, header .logo a, header a.logo' +
        '.gather-overlay .menu a');
    $links.on('click', function (e) {
        $(this).attr('target', '_self');
        if ($(this).not('.layout-container .portfolio a.button, header .icons-pack .elem-container').length) {
            e.preventDefault();
            if ($(this).attr('href') == '#' || $(this).attr('href') == '' || $(this).attr('href') == undefined){
                return;}

            var href = $(this).attr('href');
            if ($('body').hasClass('changed') || pixflow_customizerObj().$('#customize-header-actions #save').val() == 'Save & Publish') {
                var text = mBuilderValues.leaveMsg;
                pixflow_customizerObj().pixflow_messageBox(mBuilderValues.unsaved, 'caution unsaved-caution', text, mBuilderValues.save_leave, function () {
                    pixflow_customizerObj().saveCallbackFunction = function () {
                        setTimeout(function () {
                            pixflow_customizerObj().saveCallbackFunction = null;
                        }, 10);
                        if (pixflow_customizerObj().wp.customize.previewer.previewUrl() != href) {
                            pixflow_customizerObj().pixflow_customizerLoading();
                            pixflow_customizerObj().wp.customize.previewer.previewUrl(href);
                            if (pixflow_customizerObj().wp.customize.previewer.previewUrl() != href) {
                                window.open(href);
                                pixflow_customizerObj().$('.customizer-loading').css({'display': 'none'});
                            }
                        } else {
                            $('html').animate({opacity: 0.7}).animate({opacity: 1})
                        }
                    };
                    pixflow_customizerObj().$('#save-btn').click();
                    pixflow_customizerObj().pixflow_closeMessageBox();
                }, 'Just Leave', function () {
                    pixflow_customizerObj().pixflow_closeMessageBox();
                    setTimeout(function () {
                        if (pixflow_customizerObj().wp.customize.previewer.previewUrl() != href) {
                            pixflow_customizerObj().pixflow_customizerLoading();
                            pixflow_customizerObj().wp.customize.previewer.previewUrl(href);
                            if (pixflow_customizerObj().wp.customize.previewer.previewUrl() != href) {
                                window.open(href);
                                pixflow_customizerObj().$('.customizer-loading').css({'display': 'none'});
                            }
                        } else {
                            $('html').animate({opacity: 0.7}).animate({opacity: 1})
                        }
                    }, 500);
                }, function () {
                });
                return false;
            } else {
                var siteUrl =   pixflow_customizerObj().wp.customize.previewer.previewUrl();
                var linkUrl =  $(this).attr('href');

                if (pixflow_customizerObj().wp.customize.previewer.previewUrl() != $(this).attr('href') && (linkUrl[siteUrl.length] != "#")) {
                    pixflow_customizerObj().pixflow_customizerLoading();
                    pixflow_customizerObj().wp.customize.previewer.previewUrl($(this).attr('href'));
                    if (pixflow_customizerObj().wp.customize.previewer.previewUrl() != href) {
                        window.open(href);
                        pixflow_customizerObj().$('.customizer-loading').css({'display': 'none'});
                    }
                } else {
                    $('html').animate({opacity: 0.7}).animate({opacity: 1})
                }
                return false;
            }
        }
    });
    window.onbeforeunload = null;
};

// builder instance
var builder = null;
$(function(){
    builder = new mBuilder();
    builder.makeLinksTargetSelf();
    // Alert if changes not saved before leave/reload page
    window.onbeforeunload = function(event) {
        if($('body').hasClass('changed')){
            event.returnValue = true;
        }
    };


    $('[data-mbuilder-el]').each(function(){
        var type = $(this).attr('data-mbuilder-el');
        if(typeof builder[type+"Shortcode"] == 'function') {
            builder[ type + 'Shortcode']($(this));
        }
    });

});
