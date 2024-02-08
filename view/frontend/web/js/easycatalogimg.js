define([
    'jquery',
    'Magento_Ui/js/modal/modal' // 2.3.3: create 'jquery-ui-modules/widget' dependency
], function ($) {
    'use strict';

    $.widget('swissup.easycatalogimg', {
        component: 'Swissup_Easycatalogimg/js/easycatalogimg',

        _create: function () {
            if (this.options.hide_when_filter_is_used) {
                this.hideAfterAjaxFilters();
            }
        },

        hideAfterAjaxFilters: function () {
            $(document).on('swissup:ajaxlayerednavigation:reload:after', (event, data) => {
                setTimeout(() => {
                    var applyMultiple = $('#layered-filter-apply-tooltip');

                    if (applyMultiple.length && applyMultiple[0].style.left !== '-1000px') {
                        return;
                    }

                    if (data.response.state || $('[data-page-number]').last().data('page-number') > 1) {
                        this.element.hide();
                    } else {
                        this.element.show();
                    }
                }, 20);
            });
        }
    });

    return $.swissup.easycatalogimg;
});
