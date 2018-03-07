<?php


use Theme\Lee\Ekom\Helper\ListFilterHelper;
use Theme\Lee\ThisApp\EkomEvents\LeeCalendarHelper;
use Theme\LeeTheme;

LeeTheme::useLib("leeCalendar");
$dateStartName = $v["dateStartName"];
$dateEndName = $v["dateEndName"];
$availableDates = $v["availableDates"];
$currentDate = $v['currentDate'];

?>
<div
        id="widget-listfilter-events-calendar"
        class="widget widget-listfilter-events widget-listfilter widget-listfilter-calendar">
    <div class="listfilter-box">
        <?php ListFilterHelper::renderListFilterTitle("Date"); ?>
        <div class="listfilter-body">

            <div class="calendar">
                <form method="get" action="">
                    <?php LeeCalendarHelper::renderCalendar("calendar-one", 'fr'); ?>
                </form>
            </div>
        </div>
    </div>
</div>


<script>


    window.jqueryComponent.ready(function () {
        window.leeCalendarMonths = [
            'Janvier',
            'Février',
            'Mars',
            'Avril',
            'Mai',
            'Juin',
            'Juillet',
            'Août',
            'Septembre',
            'Octobre',
            'Novembre',
            'Décembre'
        ];

        var focusDates = <?php echo json_encode($availableDates) ?>;

        var calOne = new LeeCalendar();


        var jContext = $('#widget-listfilter-events-calendar');
        var jForm = $('form', jContext);


        function CalendarWidget(jContext) {
            var jCalendarOne = jContext.find('.calendar-one');

            jCalendarOne.on('click', function (e) {

                var jTarget = $(e.target);
                if ('TD' === jTarget[0].tagName) {
                    var range = jTarget.attr("data-date-range");
                    if ('undefined' !== typeof range) {


                        /**
                         * Here, we use the bionic mechanisms (encoded in lee-calendar.js)
                         * to post the form, merging with uri params.
                         */
                        var p = range.split('--');
                        var dateStart = p[0];
                        var dateEnd = p[1];
                        jForm.append('<input type="hidden" name="<?php echo $dateStartName; ?>" value="' + dateStart + '">');
                        jForm.append('<input type="hidden" name="<?php echo $dateEndName; ?>" value="' + dateEnd + '">');
                        jForm.submit();
                    }
                    return false;

                }
            });


            this.init = function () {
                calOne.init(".calendar-one", "<?php echo $currentDate; ?>", focusDates);
            };

        }


        var oCalendar = new CalendarWidget(jContext);
        oCalendar.init();


    });
</script>

