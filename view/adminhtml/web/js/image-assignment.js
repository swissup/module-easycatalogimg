define(['jquery', 'Magento_Ui/js/modal/alert'], function($, alert) {
    var self,
        url;

    return {
        init: function(ajaxCallUrl, assignBtn) {
            self = this;
            url = ajaxCallUrl;

            $(assignBtn).on('click', function() {
                self.assignImages(0, 0);
            });
        },
        assignImages: function(last_processed, processed) {
            if (!$('#easycatalogimg_automated_image_assignment_thumbnail').prop('checked')) {
                alert({
                    title: $.mage.__('Error'),
                    content: $.mage.__('Please select the checkbox above')
                });
                return;
            }
            $.ajax({
                method: "POST",
                url: url,
                showLoader: true,
                dataType: "json",
                data: {
                    last_processed: last_processed,
                    processed: processed,
                    thumbnail: $('#easycatalogimg_automated_image_assignment_thumbnail').prop('checked') ? 1 : 0,
                    search_in_child_categories: $('#easycatalogimg_automated_image_assignment_search_in_child_categories').prop('checked') ? 1 : 0
                }
            })
            .done(function(data) {
                if (data.error) {
                    alert({
                        title: $.mage.__('Error'),
                        content: data.error
                    });
                    return;
                }
                if (!data.finished) {
                    self.assignImages(data.last_processed, data.processed);
                } else {
                    var message = $.mage.__("Completed. {count} items were processed.");
                    alert({
                        title: $.mage.__('Success'),
                        content: message.replace('{count}', data.processed)
                    });
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                alert({
                    title: $.mage.__('Error'),
                    content: $.mage.__('An error occured:') + errorThrown
                });
            });
        }
    }
});
