ContentScheduler = ContentScheduler || {};
ContentScheduler.Admin = ContentScheduler.Admin || {};

ContentScheduler.Admin.Publish = (function ($) {

    function Publish() {
        if ($('#misc-publishing-actions').length === 0) {
            return;
        }

        this.initDatepicker();
    }

    Publish.prototype.initDatepicker = function () {
        $('#aa, #mm, #jj').hide();

        var timestamp_wrap_text = $('.misc-pub-curtime .timestamp-wrap').html();
        timestamp_wrap_text = timestamp_wrap_text.replace(/(,|@)/g, '');
        $('.misc-pub-curtime .timestamp-wrap').html(timestamp_wrap_text);

        $('#hh').before('<span class="municipio-admin-datepicker-time dashicons dashicons-clock"></span>')

        $('#timestampdiv').prepend('<div id="timestamp-datepicker" class="municipio-admin-datepicker"></div>');
        $('#timestamp-datepicker').datepicker({
            firstDay: 1,
            dateFormat: "yy-mm-dd",
            onSelect: function (selectedDate) {
                selectedDate = selectedDate.split('-');

                $('#aa').val(selectedDate[0]);
                $('#mm').val(selectedDate[1]);
                $('#jj').val(selectedDate[2]);
            }
        });

        var initialDate = $('#aa').val() + '-' + $('#mm').val() + '-' + $('#jj').val();
        $('#timestamp-datepicker').datepicker('setDate', initialDate);
    };

    return new Publish();

})(jQuery);
