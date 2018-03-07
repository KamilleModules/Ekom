if (false === ('PhoneCountry' in window)) {


    window.PhoneCountry = function (jContext, phoneCountries) {

        this.phoneSelector = jContext.find(".phone-line-container .phone-prefix-selector");
        this.phonePrefixInput = jContext.find(".phone-prefix-input");
        this.phoneFlag = jContext.find(".phone-flag");
        this.prefix = jContext.find(".phone-prefix");

        var classPhoneToRemove = [];
        for (var i in phoneCountries) {
            var info = phoneCountries[i];
            var country = info.country;
            classPhoneToRemove.push('flag-' + country);
        }

        this.classPhoneToRemove = classPhoneToRemove;


    };

    PhoneCountry.prototype = {
        select: function () {

            this.phoneSelector.addClass('selecting');
            this.listenToPhoneSelect();
        },
        listenToPhoneSelect: function () {


            var zis = this;
            $(document).on('click.phoneCountryWidget', function (e) {
                var jTarget = $(e.target);
                $(document).off('click.phoneCountryWidget');



                if (jTarget.hasClass("phone-choice")) {

                    var jLi = jTarget.closest("li");
                    var prefix = jLi.attr("data-prefix");
                    var country = jLi.attr("data-country");
                    var label = jLi.find(".phone").text();



                    zis.phonePrefixInput.attr("value", prefix);
                    zis.phoneFlag.removeClass(zis.classPhoneToRemove.join(" "));
                    zis.phoneFlag.addClass('flag-' + country);
                    zis.prefix.text(label);
                }
                zis.phoneSelector.removeClass("selecting");
            });
        }

    };
}