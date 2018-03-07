/**
 *
 * Define
 * window.leeCalendarMonths to override the months for your locale
 *
 *
 * https://code.tutsplus.com/tutorials/how-to-build-a-beautiful-calendar-widget--net-12538
 */

window.LeeCalendar = function () {
    var wrap, label, months;
    var month2ActiveDay = {};
    var zis = this;
    var focusDates = null;

    if ('leeCalendarMonths' in window) {
        months = window.leeCalendarMonths;
    }
    else {
        months = [
            "January",
            "February",
            "March",
            "April",
            "May",
            "June",
            "July",
            "August",
            "September",
            "October",
            "November",
            "December"
        ];
    }


    var firstMonth = months[0];
    var lastMonth = months[11];
    var isInit = false;


    function init(newWrap, sqlDate, _focusDates) {
        focusDates = _focusDates;
        isInit = true;
        wrap = $(newWrap || "#cal");
        label = wrap.find("#label");
        wrap.find("#prev").on("click.calendar", function () {
            switchMonth(false);
        });
        wrap.find("#next").on("click.calendar", function () {
            switchMonth(true);
        });
        label.on("click", function () {
            switchMonth(null, new Date().getMonth(), new Date().getFullYear());
        });

        if (sqlDate) {
            zis.selectDay(sqlDate);
        }
        else {
            label.click();
        }
        isInit = false;
    }


    function switchMonth(next, month, year) {


        var curr = label.text().trim().split(" "), calendar, tempYear = parseInt(curr[1], 10);
        month = month || ((next) ? ((curr[0] === lastMonth) ? 0 : months.indexOf(curr[0]) + 1) : ((curr[0] === firstMonth) ? 11 : months.indexOf(curr[0]) - 1));
        year = year || ((next && month === 0) ? tempYear + 1 : (!next && month === 11) ? tempYear - 1 : tempYear);

        if (month < 0) {
            month = 0;
        }


        /**
         * So that external apis can investigate the year and month more easily
         */
        var formattedMonth = month + 1;
        if (13 === formattedMonth) {
            formattedMonth = 1;
        }
        if (formattedMonth < 10) {
            formattedMonth = "0" + formattedMonth;
        }
        wrap.attr('data-year', year);
        wrap.attr('data-month', formattedMonth);


        calendar = createCal(year, month);
        if (true === isInit) {
            $("#cal-frame", wrap)
                .find(".curr")
                .removeClass("curr")
                .addClass("temp")
                .end()
                .prepend(calendar.calendar())
                .find(".temp")
                .remove(); // no animation when the calendar first shows up
        }
        else {

            $("#cal-frame", wrap)
                .find(".curr")
                .removeClass("curr")
                .addClass("temp")
                .end()
                .prepend(calendar.calendar())
                .find(".temp")
                .fadeOut("slow", function () {
                    $(this).remove();
                });
        }

        $('#label', wrap).text(calendar.label);


        if (false === jQuery.isEmptyObject(month2ActiveDay)) {

            var day = parseInt(month2ActiveDay[month]);

            wrap.find('.active').removeClass('active');
            wrap.find('td').each(function () {
                var val = parseInt($(this).text());
                if (day === val) {
                    $(this).addClass('active');
                }
            });
        }
    }

    function createCal(year, month) {
        var day = 1, i, j, haveDays = true,
            startDay = new Date(year, month, day).getDay(),
            daysInMonths = [31, (((year % 4 == 0) && (year % 100 != 0)) || (year % 400 == 0)) ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31],
            calendar = [];

        if (createCal.cache[year]) {
            if (createCal.cache[year][month]) {
                return createCal.cache[year][month];
            }
        } else {
            createCal.cache[year] = {};
        }


        i = 0;
        while (haveDays) {
            calendar[i] = [];
            for (j = 0; j < 7; j++) {
                if (i === 0) {
                    if (j === startDay) {
                        calendar[i][j] = day++;
                        startDay++;
                    }
                } else if (day <= daysInMonths[month]) {
                    calendar[i][j] = day++;
                } else {
                    calendar[i][j] = "";
                    haveDays = false;
                }
                if (day > daysInMonths[month]) {
                    haveDays = false;
                }
            }
            i++;
        }


        if (calendar[5]) {
            for (i = 0; i < calendar[5].length; i++) {
                if (calendar[5][i] !== "") {
                    calendar[4][i] = "<span>" + calendar[4][i] + "</span><span>" + calendar[5][i] + "</span>";
                }
            }
            calendar = calendar.slice(0, 5);
        }


        for (i = 0; i < calendar.length; i++) {
            calendar[i] = "<tr><td>" + calendar[i].join("</td><td>") + "</td></tr>";
        }
        calendar = $("<table>" + calendar.join("") + "</table>").addClass("curr");
        $("td:empty", calendar).addClass("nil");

        if (month === new Date().getMonth()) {
            $('td', calendar).filter(function () {
                return $(this).text() === new Date().getDate().toString();
            }).addClass("today");
        }


        if (null !== focusDates) {
            for (var i in focusDates) {
                var focusRange = focusDates[i];
                var p = focusRange.split("--");


                // start date
                var focusDate = p[0];
                var focusYear = focusDate.substr(0, 4);
                var focusMonth = focusDate.substr(5, 2) - 1; // js characteristic
                var focusDay = parseInt(focusDate.substr(8, 2));
                var focusEndDay = parseInt(p[1].substr(8, 2));


                if (focusYear == year && focusMonth == month) {


                    $('td', calendar).filter(function () {
                        var day = parseInt($(this).text());
                        return day === focusDay;
                    }).addClass("focused-start");

                    $('td', calendar).filter(function () {
                        var day = parseInt($(this).text());
                        return day === focusEndDay;
                    }).addClass("focused-end");

                    $('td', calendar).filter(function () {
                        var day = parseInt($(this).text());
                        return (day >= focusDay && day <= focusEndDay);
                    }).addClass("focused bionic-btn")
                        .attr('data-action', "!post")
                        .attr('data-merge-with-uri-params', "1")
                        .attr('data-date-range', focusRange)
                    ;
                }
            }
        }


        createCal.cache[year][month] = {
            calendar: function () {
                return calendar.clone()
            }, label: months[month] + " " + year
        };

        return createCal.cache[year][month];


    }


    this.selectDay = function (sqlDate) {
        var year = sqlDate.substr(0, 4);
        var month = parseInt(sqlDate.substr(5, 2)) - 1;
        var day = parseInt(sqlDate.substr(8, 2));


        month2ActiveDay[month] = day;
        switchMonth(null, month, year);
    };


    createCal.cache = {};
    return {
        init: init,
        switchMonth: switchMonth,
        createCal: createCal
    };
};

