<?php


use Theme\Lee\Ekom\Helper\ColorsHelper;
use Theme\Lee\Ekom\Helper\ListFilterHelper;
use Theme\Lee\ThisApp\EkomEvents\LeeCalendarHelper;
use Theme\LeeTheme;

LeeTheme::useLib("leeCalendar");
$dateOne = $v['dateOne'];
$dateTwo = $v['dateTwo'];

?>
<div
        id="widget-listfilter-events-calendar"
        class="widget widget-listfilter-events widget-listfilter">
    <div class="listfilter-box">
        <?php ListFilterHelper::renderListFilterTitle("Date"); ?>
        <div class="listfilter-body">


            <div class="date-selector calendar-one-trigger">
                <input
                        class="calendar-one-trigger
                        calendar-one-trigger-input"
                        type="text" placeholder="CHOISIR UNE DATE DE DÉBUT"
                        value="<?php echo $dateOne; ?>"
                >
                <i class="lee-icon lee-icon-calendar calendar-one-trigger"></i>
            </div>

            <div class="date-selector calendar-two-trigger">
                <input
                        class="calendar-two-trigger
                        calendar-two-trigger-input"
                        type="text" placeholder="CHOISIR UNE DATE DE FIN"
                        value="<?php echo $dateTwo; ?>"
                >
                <i class="lee-icon lee-icon-calendar calendar-two-trigger"></i>
            </div>

            <div class="calendars">
                <div class="calendar-slider">
                    <?php
                    LeeCalendarHelper::renderCalendar("calendar-one", 'fr', "Date de début");
                    LeeCalendarHelper::renderCalendar("calendar-two", 'fr', "Date de fin");
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>


<script>

    var _baseUri = '<?php echo $v['baseUriDateRange']; ?>';


    document.addEventListener("DOMContentLoaded", function (event) {
        $(document).ready(function () {

            var dateOneDefault = null;
            var dateTwoDefault = null;

            <?php if(null !== $dateOne): ?>
            dateOneDefault = '<?php echo $dateOne; ?>';
            <?php endif; ?>

            <?php if(null !== $dateTwo): ?>
            dateTwoDefault = '<?php echo $dateTwo; ?>';
            <?php endif; ?>

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

            var focusDates = [
                "2017-11-19--2017-11-21"
            ];

            var calOne = new LeeCalendar();
            var calTwo = new LeeCalendar();


            var jContext = $('#widget-listfilter-events-calendar');


            function CalendarWidget(jContext) {
                var zis = this;
                var position = 'left';
                var jSlider = jContext.find('.calendar-slider');
                var speed = 400;
                var easing = "swing";
                var dateOne = dateOneDefault;

                var jCalendars = jContext.find('.calendars');
                var jCalendarOne = jContext.find('.calendar-one');
                var jCalendarTwo = jContext.find('.calendar-two');
                var jCalendarOneInput = jContext.find('.calendar-one-trigger-input');
                var jCalendarTwoInput = jContext.find('.calendar-two-trigger-input');


                this.slideToLeft = function () {
                    if ('right' === position) {
                        jSlider.animate({
                            left: '0px'
                        }, speed, easing, function () {
                            position = "left";
                        });
                    }
                };

                this.slideToRight = function () {

                    if ('left' === position) {
                        jSlider.animate({
                            left: '-267px'
                        }, speed, easing, function () {
                            position = "right";
                        });
                    }
                };


                function cleanCalendar(jCal) {
                    jCal.find('.active').removeClass('active');
                }

                function formatDate(year, month, day) {
                    if (day < 10) {
                        day = "0" + day;
                    }
                    return year + "-" + month + "-" + day;
                }

                function highlight(jInput) {
                    var jDate = jInput.closest(".date-selector");
                    jDate.addClass('highlighted');
                    setTimeout(function () {
                        jDate.removeClass('highlighted');
                    }, 600);
                }

                jCalendarOne.on('click', function (e) {

                    jCalendarTwoInput.val(""); // reset input two

                    var jTarget = $(e.target);
                    if ('TD' === jTarget[0].tagName) {
                        var day = jTarget.html();
                        cleanCalendar(jCalendarOne);
                        jTarget.addClass('active');
                        var year = jCalendarOne.attr('data-year');
                        var month = jCalendarOne.attr('data-month');
                        var date = formatDate(year, month, day);
                        dateOne = date;
                        jCalendarOneInput.val(date);
                        setTimeout(function () {
                            highlight(jCalendarTwoInput);
                            zis.slideToRight();
                        }, 1000);
                    }
                });


                jCalendarTwo.on('click', function (e) {
                    var jTarget = $(e.target);
                    if ('TD' === jTarget[0].tagName) {
                        var day = jTarget.html();
                        cleanCalendar(jCalendarTwo);
                        jTarget.addClass('active');
                        var year = jCalendarTwo.attr('data-year');
                        var month = jCalendarTwo.attr('data-month');
                        var date = formatDate(year, month, day);
                        jCalendarTwoInput.val(date);


                        if (null !== dateOne) {
                            var dateRange = dateOne + "--" + date;
                            window.location.href = _baseUri.replace('__date__', dateRange);
                        }


                    }
                });


                this.init = function () {

                    jContext.on('click', function (e) {
                        var jTarget = $(e.target);
                        if (jTarget.hasClass("calendar-one-trigger")) {
                            zis.slideToLeft();
                            return false;
                        }
                        else if (jTarget.hasClass("calendar-two-trigger")) {
                            zis.slideToRight();
                            return false;
                        }
                    });


                    calOne.init(".calendar-one", dateOneDefault, focusDates);
                    calTwo.init(".calendar-two", dateTwoDefault, focusDates);
                };

            }


            var oCalendar = new CalendarWidget(jContext);
            oCalendar.init();


        });
    });

</script>

