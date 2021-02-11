(function ($) {
    $(document).ready(function () {

        $("input.picker-datetime").flatpickr({
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
        });

        // Expand textarea according to content
        function textAreaExpand() {
            var element = document.getElementById("onecom_under_construction_info[uc_scripts]");

            element.style.height = "1px";
            element.style.height = (30 + element.scrollHeight) + "px";

            var element = document.getElementById("onecom_under_construction_info[uc_custom_css]");

            element.style.height = "1px";
            element.style.height = (30 + element.scrollHeight) + "px";
        }
        textAreaExpand();

        /* Hide all components on switch disable */
        if (!$('.uc_status input[type=checkbox]').prop('checked')) {
            $('tr[class^="uc_"]:not(.uc_status)').addClass('uc_hide');
            $('.form-table:nth-of-type(2)').addClass('uc_hide');
            $('.postbox-header:nth-of-type(2)').addClass('uc_hide');
            $('.form-table:nth-of-type(3)').addClass('uc_hide');
            $('.postbox-header:nth-of-type(3)').addClass('uc_hide');
        }

        /* Hide/Show all components on switch change */
        $(document).on('change', '.uc_status input[type=checkbox]', function () {
            if (!$(this).prop("checked")) {
                $('tr[class^="uc_"]:not(.uc_status)').addClass('uc_hide');
                $('.form-table:nth-of-type(2)').addClass('uc_hide');
                $('.postbox-header:nth-of-type(2)').addClass('uc_hide');
                $('.form-table:nth-of-type(3)').addClass('uc_hide');
                $('.postbox-header:nth-of-type(3)').addClass('uc_hide');
            } else {
                $('tr[class^="uc_"]:not(.uc_status)').removeClass('uc_hide');
                $('.form-table:nth-of-type(2)').removeClass('uc_hide');
                $('.postbox-header:nth-of-type(2)').removeClass('uc_hide');
                $('.form-table:nth-of-type(3)').removeClass('uc_hide');
                $('.postbox-header:nth-of-type(3)').removeClass('uc_hide');
            }
        });

        /* Disable timer field if switched off */
        if (!$('.uc_timer_switch input[type=checkbox]').prop('checked')) {
            $('.uc_timer').addClass('uc_hide');
        }

        $(document).on('change', '.uc_timer_switch input[type=checkbox]', function () {
            if (!$(this).prop("checked")) {
                $('.uc_timer').addClass('uc_hide');
            } else {
                $('.uc_timer').removeClass('uc_hide');
            }
        });
    });

})(jQuery)