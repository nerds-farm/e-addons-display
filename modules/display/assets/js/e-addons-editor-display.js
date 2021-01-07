/* 
 * DISPLAY
 * e-addons.com
 */

var e_model_cid = false;

jQuery(window).on('load', function () {

    elementor.hooks.addAction('panel/open_editor/section', function (panel, model, view) {
        e_model_cid = model.cid;
    });
    elementor.hooks.addAction('panel/open_editor/column', function (panel, model, view) {
        e_model_cid = model.cid;
    });
    elementor.hooks.addAction('panel/open_editor/widget', function (panel, model, view) {
        e_model_cid = model.cid;
    });
});

/******************************************************************************/

function e_display_enable_save_button() {
// enable save buttons
//console.log('enable save button');
    jQuery('#elementor-panel-saver-button-publish, #elementor-panel-saver-button-save-options, #elementor-panel-saver-menu-save-draft').removeClass('elementor-saver-disabled').removeClass('elementor-disabled').prop('disabled', false).removeProp('disabled');
    return true;
}

function e_display_is_hidden(cid) {
    if (cid && elementorFrontend.config.elements.data[cid]) {
        var settings = elementorFrontend.config.elements.data[cid].attributes;
        //console.log(cid);console.log(settings);
        if (settings['e_display_mode']) { // && settings['e_display_hidden']) {
            return true;
        }
    }
    return false;
}
function e_display_toggle(cid, change_data) {
    var new_data = false;
    var settings = elementorFrontend.config.elements.data[cid].attributes;
    if (change_data) {
        if (settings['e_display_mode'] == 'none') {
            new_data = '';
            elementorFrontend.config.elements.data[cid].attributes['e_display_mode'] = new_data;
        } else {
            new_data = 'none';
            elementorFrontend.config.elements.data[cid].attributes['e_display_mode'] = new_data;
        }
    }

    e_menu_list_item_toggle(cid);
    e_navigator_element_toggle(cid);

    // color element hidden
    var preview_iframe = jQuery("iframe#elementor-preview-iframe").contents();
    if (settings['e_display_mode']) {
        preview_iframe.find('.elementor-element[data-model-cid=' + cid + ']').addClass('e-display-hidden');
    } else {
        preview_iframe.find('.elementor-element[data-model-cid=' + cid + ']').removeClass('e-display-hidden');
    }
    e_display_enable_save_button();
    return new_data;
}
function e_menu_list_item_add(cid) {
    // add context menu item
    var context_menu = jQuery('.elementor-context-menu:visible');
    if (!context_menu.find('.elementor-context-menu-list__item-display').length && cid) {
        context_menu.attr('data-model-cid', cid);
        context_menu.find('.elementor-context-menu-list__group-delete').append(
            '<div class="elementor-context-menu-list__item elementor-context-menu-list__item-display"><div class="elementor-context-menu-list__item__icon"><i class="e-icon-display fa fas fa-ghost" aria-hidden="true"></i></div><div class="elementor-context-menu-list__item__title">Hide in frontend</div></div>'
            );
        if (e_display_is_hidden(cid)) {
            e_menu_list_item_toggle(cid);
        }
    }
}
function e_menu_list_item_toggle(cid) {
    var menu_item = jQuery('.elementor-context-menu[data-model-cid=' + cid + '] .elementor-context-menu-list__item-display');
    // update icon
    menu_item.find('.elementor-context-menu-list__item__icon').children('.e-icon-display').toggleClass('e-icon-display-hidden');//.toggleClass('fa-ghost').toggleClass('fa-eye-slash');

    // update text
    var settings = elementorFrontend.config.elements.data[cid].attributes;
    var icon = menu_item.find('.elementor-context-menu-list__item__icon');
    if (settings['e_display_mode'] == 'none') {
        var text =  'Show';
        icon.css('color', '#e52600');
    } else {
        var text = 'Hide';
        icon.css('color', '');
    }    
    menu_item.find('.elementor-context-menu-list__item__title').text(text + ' in frontend');

    return true;
}
function e_navigator_element_toggle(cid) {
    if (e_display_is_hidden(cid)) {
        jQuery('.elementor-navigator__element[data-model-cid=' + cid + '] > .elementor-navigator__item .e-elementor-navigator__element__toggle > .e-icon-display').addClass('e-icon-display-hidden'); //.removeClass('fa-ghost').addClass('fa-eye-slash');
        jQuery('.elementor-navigator__element[data-model-cid=' + cid + ']').addClass('e-display-hidden');
    } else {
        jQuery('.elementor-navigator__element[data-model-cid=' + cid + '] > .elementor-navigator__item .e-elementor-navigator__element__toggle > .e-icon-display').removeClass('e-icon-display-hidden'); //.addClass('fa-ghost').removeClass('fa-eye-slash');
        jQuery('.elementor-navigator__element[data-model-cid=' + cid + ']').removeClass('e-display-hidden');
    }
}

function e_panel_element_toggle(value, cid) {
    var selectedElement = elementor.getCurrentElement();
    //console.log(selectedElement);
    if (selectedElement) {
        var current_cid = selectedElement.model.cid;
    } else {
        var current_cid = e_model_cid;
    }
    if (current_cid == cid) {
        if (jQuery('.elementor-control-e_display_mode').is(':visible')) {
            jQuery('select[data-setting=e_display_mode]').val(value);
            jQuery('select[data-setting=e_display_mode]').change();
        }
    }
}

function e_addons_display() {
    // add navigator element toggle
    jQuery('.elementor-navigator__item').each(function () {
        //if (!jQuery(this).hasClass('e-display__item')) {
        var element = jQuery(this).closest('.elementor-navigator__element');
        var cid = element.data('model-cid');
        // add button to force display
        if (e_display_is_hidden(cid)) {
            if (!jQuery(this).find('.e-elementor-navigator__element__toggle').length) {
                jQuery(this).children('.elementor-navigator__element__indicators').append(
                        '<div class="elementor-navigator__element__indicator e-elementor-navigator__element__toggle" data-section="section_display" original-title="Display"><i class="e-icon-display fa fas fa-ghost"></i></div>'
                        );
            }
            //jQuery(this).addClass('e-display__item');
        } else {
            jQuery(this).find('.e-elementor-navigator__element__toggle').remove();
            //jQuery(this).removeClass('e-display__item');
        }
        //}
    });

    if (window.elementorFrontend) {
        var preview_iframe = jQuery("iframe#elementor-preview-iframe").contents();
        jQuery.each(elementorFrontend.config.elements.data, function (cid, element) {
            // check if element is just hidden
            if (e_display_is_hidden(cid)) {
                //console.log('check hidden for: '+ cid);
                e_navigator_element_toggle(cid);
                preview_iframe.find('.elementor-element[data-model-cid=' + cid + ']').addClass('e-display-hidden');//.addClass('e-display-hidden');                
            }
        });
    }
}

jQuery(window).load(function () {

    if (window.elementorFrontend) {
        elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
            e_addons_display();
        });
    }
    //console.log(elementorFrontend.config.elements.data);
    elementor.hooks.addAction('panel/open_editor/section', function (panel, model, view) {
        e_addons_display();
    });
    elementor.hooks.addAction('panel/open_editor/column', function (panel, model, view) {
        e_addons_display();
    });
    elementor.hooks.addAction('panel/open_editor/widget', function (panel, model, view) {
        e_addons_display();
    });

    // get model CID on mouse dx click
    var preview_iframe = jQuery("iframe#elementor-preview-iframe").contents();
    preview_iframe.on('mouseup', '.elementor-element', function (event) {
        if (event.which == 3) {
            var eid = jQuery(this).data('id');
            var cid = jQuery(this).data('model-cid');
            var type = jQuery(this).data('element_type');
            //console.log(type + ' - ' + eid + ' - ' + cid);            
            setTimeout(function () {
                e_menu_list_item_add(cid);
            }, 10);
            return false;
        }
    });
    preview_iframe.on('mousedown', '.elementor, .elementor-editor-element-settings', function (event) {
        if (event.which == 3) {
            var cid = jQuery(this).closest('.elementor-element').data('model-cid');
            setTimeout(function () {
                e_menu_list_item_add(cid);
            }, 10);
            return false;
        }
    });
    
    jQuery(document).on('mousedown', '.elementor-navigator__element', function (event) {
        if (event.which == 3) {
            var cid = jQuery(this).data('model-cid');
            setTimeout(function () {
                e_menu_list_item_add(cid);
            }, 10);
            return false;
        }
    });
});

jQuery(document).ready(function () {

    jQuery(document).on('click', '.e-elementor-navigator__element__toggle', function () {
        jQuery('.elementor-panel-navigation-tab.elementor-tab-control-e_display').click();
    });

    jQuery(document).on('click', '.elementor-context-menu-list__item-display', function () {
        var cid = jQuery(this).closest('.elementor-context-menu').data('model-cid');
        var new_data = e_display_toggle(cid, true);
        e_panel_element_toggle(new_data, cid);
        e_addons_display();
    });

    jQuery(document).on('change', 'select[data-setting=e_display_mode]', function () {
        //var cid = jQuery(this).attr('id').split('-').pop();
        var selectedElement = elementor.getCurrentElement();
        //console.log(selectedElement);
        if (selectedElement) {
            var cid = selectedElement.model.cid;
        } else {
            var cid = e_model_cid;
        }
        //console.log('e display settings '+cid);
        e_display_toggle(cid, false);
        e_addons_display();
    });

});