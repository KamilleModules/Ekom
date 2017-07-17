<?php


namespace Module\Ekom\Utils;


class EkomJsHelper
{

    /**
     * Howto:
     * --------
     * In your js code, to this:
     *
     *
     * function price(number) {
     *      <?php echo EkomJsHelper::getJsPriceFunctionContent(); ?>;
     * };
     * console.log(price(5000));
     *
     *
     */
    public static function getJsPriceFunctionContent()
    {

        $moneyFormatArgs = json_encode(E::conf("moneyFormatArgs"));
        return <<<EEE

    var moneyFormatArgs = $moneyFormatArgs;


    function number_format(number, decimals, decPoint, thousandsSep) {
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
        var n = !isFinite(+number) ? 0 : +number
        var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
        var sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep
        var dec = (typeof decPoint === 'undefined') ? '.' : decPoint
        var s = ''
        var toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec)
            return '' + (Math.round(n * k) / k)
                    .toFixed(prec)
        }
        // @todo: for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.')
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || ''
            s[1] += new Array(prec - s[1].length + 1).join('0')
        }
        return s.join(dec)
    }


    function formatPrice(number, options) {
        var _options = {
            'alwaysShowDecimals': true,
            'nbDecimals': 2,
            'decPoint': ".",
            'thousandSep': "",
            'moneySymbol': "€",
            'moneyFormat': "vs" // v represents the value, s represents the money symbol
        };
        for (var i in options) {
            _options[i] = options[i];
        }

        var alwaysShowDecimals = _options['alwaysShowDecimals'];
        var nbDecimals = _options['nbDecimals'];
        var decPoint = _options['decPoint'];
        var thousandSep = _options['thousandSep'];
        var moneySymbol = _options['moneySymbol'];
        var moneyFormat = _options['moneyFormat'];

        var v = number_format(number, nbDecimals, decPoint, thousandSep);
        if (false === alwaysShowDecimals && nbDecimals > 0) {
            var p = v.split(decPoint);
            var dec = p.pop();
            if (0 === parseInt(dec)) {
                v = p.join('');
            }
        }

        var ret = moneyFormat.replace('v', v).replace('s', moneySymbol);
        return ret;


    };

    
    return formatPrice(number, moneyFormatArgs);
    
EEE;

    }
}