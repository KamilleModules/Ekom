document.addEventListener("DOMContentLoaded", function (event) {
    $(document).ready(function () {
        var jRoundCheckbox = $('input.round-checkbox');

        /**
         * Init: if the input.checkbox is checked, show the checked version of the div.checkbox,
         * otherwise show the unchecked version
         */
        jRoundCheckbox.each(function () {
            var id = $(this).attr('id');
            if ($(this).prop('checked')) {
                $('.round-checkbox[data-id="' + id + '"]').addClass("checked");
            }
            else {
                $('.round-checkbox[data-id="' + id + '"]').removeClass("checked");
            }
        });

        /**
         * If the user clicks the label, it will check/uncheck the input.checkbox.
         * The div.checkbox should reflect this state
         */
        jRoundCheckbox.on('change', function () {
            var id = $(this).attr('id');
            $('.round-checkbox[data-id="' + id + '"]').toggleClass("checked");
            return false;
        });

        /**
         * When the user clicks the div.checkbox, it toggles the checked class;
         * also, the input.checkbox should be synced with it
         */
        $('div.round-checkbox').on('click', function () {
            var id = $(this).attr('data-id');
            var jInput = $('#' + id);
            jInput.prop("checked", !jInput.prop('checked'));
            $(this).toggleClass("checked");
            return false;
        });
    });
});