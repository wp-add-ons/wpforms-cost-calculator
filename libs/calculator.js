(function($) {
    "use strict";
    $( document ).ready( function () { 
        function wp_cal_getCurrency() {
            var currency = {
                code: 'USD',
                thousands_sep: ',',
                decimals: 2,
                decimal_sep: '.',
                symbol: '$',
                symbol_pos: 'left',
            };
            // Backwards compatibility.
            if ( typeof wpforms_settings.currency_code !== 'undefined' ) {
                currency.code = wpforms_settings.currency_code;
            }
            if ( typeof wpforms_settings.currency_thousands !== 'undefined' ) {
                currency.thousands_sep = wpforms_settings.currency_thousands;
            }
            if ( typeof wpforms_settings.currency_decimals !== 'undefined' ) {
                currency.decimals = wpforms_settings.currency_decimals;
            }
            if ( typeof wpforms_settings.currency_decimal !== 'undefined' ) {
                currency.decimal_sep = wpforms_settings.currency_decimal;
            }
            if ( typeof wpforms_settings.currency_symbol !== 'undefined' ) {
                currency.symbol = wpforms_settings.currency_symbol;
            }
            if ( typeof wpforms_settings.currency_symbol_pos !== 'undefined' ) {
                currency.symbol_pos = wpforms_settings.currency_symbol_pos;
            }
            return currency;
        }
       function wp_cal_numberFormat ( number, decimals, decimalSep, thousandsSep ) {
            number = ( number + '' ).replace( /[^0-9+\-Ee.]/g, '' );
            var n = ! isFinite( +number ) ? 0 : +number;
            var prec = ! isFinite( +decimals ) ? 0 : Math.abs( decimals );
            var sep = ( 'undefined' === typeof thousandsSep ) ? ',' : thousandsSep;
            var dec = ( 'undefined' === typeof decimalSep ) ? '.' : decimalSep;
            var s;
            var toFixedFix = function( n, prec ) {
                var k = Math.pow( 10, prec );
                return '' + ( Math.round( n * k ) / k ).toFixed( prec );
            };
            // @todo: for IE parseFloat(0.55).toFixed(0) = 0;
            s = ( prec ? toFixedFix( n, prec ) : '' + Math.round( n ) ).split( '.' );
            if ( s[0].length > 3 ) {
                s[0] = s[0].replace( /\B(?=(?:\d{3})+(?!\d))/g, sep );
            }
            if ( ( s[1] || '' ).length < prec ) {
                s[1] = s[1] || '';
                s[1] += new Array( prec - s[1].length + 1 ).join( '0' );
            }
            return s.join( dec );
        }
        function wp_cal_amountSanitize ( amount ) {
            if( amount === undefined ){
                amount = 0;
            }
            var currency = wp_cal_getCurrency();
            amount = amount.toString().replace( /[^0-9.,-]/g, '' );
            if ( currency.decimal_sep === ',' ) {
                if ( currency.thousands_sep === '.' && amount.indexOf( currency.thousands_sep ) !== -1 ) {
                    amount = amount.replace( new RegExp( '\\' + currency.thousands_sep, 'g' ), '' );
                } else if ( currency.thousands_sep === '' && amount.indexOf( '.' ) !== -1 ) {
                    amount = amount.replace( /\./g, '' );
                }
                amount = amount.replace( currency.decimal_sep, '.' );
            } else if ( currency.thousands_sep === ',' && ( amount.indexOf( currency.thousands_sep ) !== -1 ) ) {
                amount = amount.replace( new RegExp( '\\' + currency.thousands_sep, 'g' ), '' );
            }
            return wp_cal_numberFormat( amount, currency.decimals, '.', '' );
        }
        $("body").on("click",".wpforms-number-format",function(){
            $(this).autoNumeric();
            var data = $(this).autoNumeric("get");
            $(this).val(data);
        })
         $.wpforms_calculator = function(form){
            var reg = ["_ngcontent-wqo-c228"];
            $(".wpforms-field",form).each(function () { 
                var id = $(this).data("field-id");
                reg.push('{field_id="'+id+'"}');
               })
            $(".wpforms-field-calculator input",form).each( function(){
                var eq = $(this).data("formula");
                if(eq ==""){
                    return ;
                }
                var form_id = form.data("formid");
                var field = $(this);
                var match;
               var field_regexp = new RegExp( '('+reg.join("|")+')');
               while ( match = field_regexp.exec( eq ) ){ 
                    var vl_regexp  =  /\{field_id="(.*?)"\}/;    
                    var match_vl = match[0].match(vl_regexp);
                    var vl = match_vl[1];
                    var id = $("#wpforms-"+form_id+"-field_"+vl+"-container input");
                    var id_checkbox = $("#wpforms-"+form_id+"-field_"+vl+"-container input[type='checkbox']");
                    var id_radio = $("#wpforms-"+form_id+"-field_"+vl+"-container input[type='checkbox']");
                    if( id_checkbox.length > 0 || id_radio > 0 ){
                        //radio
                         var vl = 0;
                         $(id).each(function () {
                            if( $(this).is(":checked") ){
                                if($(this).hasClass("wpforms-payment-price")) {
                                    vl += new Number(wp_cal_amountSanitize($(this).data("amount")));
                                }else{
                                    vl += new Number($(this).val());
                                }
                            }
                        });
                    }else if($("#wpforms-"+form_id+"-field_"+vl+"-container select").length > 0  ) {
                        var sle = $("#wpforms-"+form_id+"-field_"+vl+"-container select");
                         if(sle.hasClass("wpforms-payment-price")) { 
                            vl = sle.data("amount");
                            vl = wp_cal_amountSanitize(vl);
                         }else{
                            vl = sle.val();
                         }
                    }else{
                       if( id.hasClass("wpforms-number-format") ){
                            id.autoNumeric();
                            vl = id.autoNumeric("get");
                        }else{
                            if( id.hasClass("wpforms-payment-price") || id.hasClass("wpforms-payment-total")){ 
                                vl = id.val();
                                vl = wp_cal_amountSanitize(vl);
                            }else{
                                 vl = id.val();
                            }
                            if( id.hasClass("wpforms-field-date-time-date") ) {
                                id.each(function( index ) {
                                  vl = $.wpforms_cover_date_format(vl,this);
                                  return false;
                                });
                                
                            }
                        }
                    }
                    if( vl == ""){
                        vl = 0;
                    }
                    eq = eq.replace( match[0], vl); 
               }
                eq =  eq.toString();
                if(wpforms_calculator.pro == "ok"){
                    eq = $.wpforms_fomulas_elseif(eq);
                    eq = $.wpforms_fomulas_days(eq);
                    eq = $.wpforms_fomulas_months(eq);
                    eq = $.wpforms_fomulas_years(eq);
                    eq = $.wpforms_fomulas_floor(eq);
                    eq = $.wpforms_fomulas_mod(eq);
                    eq = $.wpforms_fomulas_max(eq);
                    eq = $.wpforms_fomulas_min(eq);
                    eq = $.wpforms_fomulas_hours(eq);
                     eq = $.wpforms_fomulas_floor(eq);
                    eq = $.wpforms_fomulas_floor_2(eq);
                    eq = $.wpforms_fomulas_round(eq);
                    eq = $.wpforms_fomulas_round_2(eq);
                    eq = $.wpforms_fomulas_ceil(eq);
                    eq = $.wpforms_fomulas_age(eq);
                    eq = $.wpforms_fomulas_age_2(eq);
                    eq = $.wpforms_fomulas_avg(eq);
                    eq = $.wpforms_fomulas_round_custom(eq);
                    try{  
                        var total = mexp.eval(eq); 
                    }catch(e){
                    }
                }else{
                    try{  
                        var total = eval(eq); 
                    }catch(e){
                        total = eq+" Pro version";
                    }
                }
                
                if( field.hasClass("wpforms-number-format") ){
                    field.autoNumeric();
                    field.autoNumeric("set",total);
                    field.closest('.wpforms-field').find(".wpforms-number-show").autoNumeric();
                    field.closest('.wpforms-field').find(".wpforms-number-show").autoNumeric("set",total);
                    field.trigger('change');
                }else{
                   field.val(total).trigger('change');
                   field.closest('.wpforms-field').find(".wpforms-number-show").html(total);
                }
            })
        }
        $.wpforms_fomulas_avg = function(x){ 
            var re = /avg\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    x = x.replace(/[agv()]/g, '');
                    var elmt = x.split(",");
                   var sum = 0;
                    for( var i = 0; i < elmt.length; i++ ){
                        sum += parseInt( elmt[i], 10 ); //don't forget to add the base
                    }
                     return sum/elmt.length;
                });
            if( x.match(re) ){
                x = $.wpforms_fomulas_avg(x);
            }
            return x;
        }
        $.wpforms_fomulas_round = function(x){ 
            var re = /round\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    x = x.replace(/[round()]/g, '');
                    x = mexp.eval(x);
                     return Math.round(x);
                });
            if( x.match(re) ){
                x = $.wpforms_fomulas_round(x);
            }
            return x;
        }
        $.wpforms_fomulas_round_2 = function(x){ 
            var re = /round2\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    x = x.replace(/[round2()]/g, '');
                    x = mexp.eval(x);
                     return Math.round(x * 100) / 100
                });
            if( x.match(re) ){
                x = $.wpforms_fomulas_round_2(x);
            }
            return x;
        }
        $.wpforms_fomulas_floor = function(x){ 
            var re = /floor\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    x = x.replace(/[floor()]/g, '');
                    x = mexp.eval(x);
                     return Math.floor(x);
                });
            if( x.match(re) ){
                x = $.wpforms_fomulas_floor(x);
            }
            return x;
        }
        $.wpforms_fomulas_floor_2 = function(x){ 
            var re = /floor2\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    x = x.replace(/[floor2()]/g, '');
                    x = mexp.eval(x);
                     return Math.floor(x * 100) / 100
                });
            if( x.match(re) ){
                x = $.wpforms_fomulas_floor_2(x);
            }
            return x;
        }
        $.wpforms_fomulas_ceil = function(x){ 
            var re = /ceil\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    x = x.replace(/[ceil()]/g, '');
                    x = mexp.eval(x);
                     return Math.ceil(x);
                });
            if( x.match(re) ){
                x = $.wpforms_fomulas_ceil(x);
            }
            return x;
        }
        $.wpforms_fomulas_mod = function(x){ 
            var re = /mod\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    x = x.replace(/[mod()]/g, '');
                    var datas = x.split(",");
                     return  datas[0] % datas[1];
                });
            if( x.match(re) ){
                x = $.wpforms_fomulas_mod(x);
            }
            return x;
        }
        $.wpforms_fomulas_elseif = function(x){ 
            var re = /if\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    return $.wpforms_fomulas_if(x);
                });
            if( x.match(re) ){
                x = $.wpforms_fomulas_elseif(x);
            }
            return x;
        }
        $.wpforms_fomulas_if = function(x){
            x = x.replace(/[if()]/g, '');
            var data = x.split(",");
            try {
                  if(eval(data[0])){
                      return mexp.eval(data[1]);
                  }else{
                      return mexp.eval(data[2]);
                  }
            } catch (e) {
               return 0;
            }               
        }
        $.wpforms_fomulas_age = function(x){ 
            var re = /age\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    x = x.replace(/[age()]/g, '');
                    var dob = new Date(x);
                    var today = new Date();
                    return Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000));
                });
            if( x.match(re) ){
                x = $.wpforms_fomulas_age(x);
            }
            return x;
        }
        $.wpforms_fomulas_age_2 = function(x){ 
            var re = /age2\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    x = x.replace(/[age2()]/g, '');
                    var datas = x.split(",");
                    var dob = new Date(datas[0]);
                    var today = new Date(datas[1]);
                    return Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000));
                });
            if( x.match(re) ){
                x = $.wpforms_fomulas_age_2(x);
            }
            return x;
        }
        $.wpforms_fomulas_days = function(x){ 
            var re = /days\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                     x = x.replace(/[days()]/g, '');
                     var datas = x.split(",");

                     if( datas[1] == "now" ){
                        var today = new Date();
                        var day_end1 = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
                     }else{
                        var day_end1= datas[1];
                     }
                     if( datas[0] == "now" ){
                        var today = new Date();
                        var day_start1 = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
                     }else{
                        var day_start1 = datas[0];
                     }
                     var day_end = $.wpforms_fomulas_parse_date(day_end1);
                     var day_start = $.wpforms_fomulas_parse_date(day_start1);
                      if( isNaN(day_end) || isNaN(day_start) ){
                        return 0;
                      }else{
                        return $.wpforms_fomulas_datediff(day_end,day_start);
                      }
                });
            if( x.match(re) ){
                x = $.wpforms_fomulas_days(x);
            }
            return x;
        }
        $.wpforms_fomulas_months = function(x){ 
            var re = /months\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                     x = x.replace(/[months()]/g, '');
                     var datas = x.split(",");
                     if( datas[1] == "now" ){
                        var today = new Date();
                        var day_end1 = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
                     }else{
                        var day_end1= datas[1];
                     }
                     var day_end = $.wpforms_fomulas_parse_date(day_end1);
                     if( datas[0] == "now" ){
                        var today = new Date();
                        var day_start1 = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
                     }else{
                        var day_start1 = datas[0];
                     }
                     var day_start = $.wpforms_fomulas_parse_date(day_start1);
                      if( isNaN(day_end) || isNaN(day_start) ){
                        return 0;
                      }else{
                        return day_start.getMonth() - day_end.getMonth() +  (12 * (day_start.getFullYear() - day_end.getFullYear()))
                      }
                });
            if( x.match(re) ){
                x = $.wpforms_fomulas_months(x);
            }
            return x;
        }
        $.wpforms_fomulas_years = function(x){ 
            var re = /years\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                     x = x.replace(/[years()]/g, '');
                     var datas = x.split(",");
                     if( datas[1] == "now" ){
                        var today = new Date();
                        var day_end1 = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
                     }else{
                        var day_end1= datas[1];
                     }
                     var day_end = $.wpforms_fomulas_parse_date(day_end1);
                     if( datas[0] == "now" ){
                        var today = new Date();
                        var day_start1 = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
                     }else{
                        var day_start1 = datas[0];
                     }
                     var day_start = $.wpforms_fomulas_parse_date(day_start1);
                      if( isNaN(day_end) || isNaN(day_start) ){
                        return 0;
                      }else{
                        return day_start.getFullYear() - day_end.getFullYear();
                      }
                });
            if( x.match(re) ){
                x = $.wpforms_fomulas_years(x);
            }
            return x;
        }
        $.wpforms_fomulas_floor = function(x){ 
            var re = /floor\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    x = x.replace(/[floor()]/g, '');
                    x = mexp.eval(x);
                     return Math.floor(x);
                });
            if( x.match(re) ){
                x = $.wpforms_fomulas_floor(x);
            }
            return x;
        }
        $.wpforms_fomulas_round_custom = function(x){ 
            var re = /custom\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    x = x.replace(/[custom()]/g, '');
                    x = mexp.eval(x);
                    x = x.toString();
                    var values = x.split(".");
                    var qk_c = values[0];
                    if( values.length > 1 ){
                        var qk_l =  values[1].substring(0,1);;
                        if( qk_l != 0 ){
                           if( qk_l < 6 ){
                                qk_l = 5;
                           }else{
                                qk_l = 0;
                                qk_c++;
                           }
                        }
                        var kq= qk_c+"."+qk_l;
                        return kq;
                    }else{
                        return x;
                    }
                });
            if( x.match(re) ){
                x = $.wpforms_fomulas_round_custom(x);
            }
            return x;
        }
        $.wpforms_fomulas_mod = function(x){ 
            var re = /mod\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    x = x.replace(/[mod()]/g, '');
                    var datas = x.split(",");
                     return  datas[0] % datas[1];
                });
            if( x.match(re) ){
                x = $.wpforms_fomulas_floor(x);
            }
            return x;
        }
         $.wpforms_fomulas_max = function(x){ 
            var re = /max\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    x = x.replace(/[max()]/g, '');
                    var datas = x.split(",");
                     datas = datas.map(element => {
                          return element.trim();
                        });
                     return Math.max.apply(null,datas);
                });
            if( x.match(re) ){
                x = $.wpforms_fomulas_max(x);
            }
            return x;
        }
        $.wpforms_fomulas_min = function(x){ 
            var re = /min\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    x = x.replace(/[min()]/g, '');
                    var datas = x.split(",");
                      datas = datas.map(element => {
                          return element.trim();
                        });
                     return Math.min.apply(null,datas);
                });
            if( x.match(re) ){
                x = $.wpforms_fomulas_min(x);
            }
            return x;
        }
        $.wpforms_fomulas_hours = function(x){ 
            var re = /hours\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    x = x.replace(/[hours()]/g, '');
                    var datas = x.split(",");
                    var hour_start = datas[1];
                    var hour_end = datas[0];
                    var hour_start_m =  hour_start.split(":");
                    var hour_end_m =  hour_end.split(":");
                    hour_start_m = parseInt(hour_start_m[0]);
                    hour_end_m = parseInt(hour_end_m[0]);
                    if( hour_start_m >= 22 && hour_end_m <= 7 ){
                        var ok = -1;
                    }else{
                       var ok= $.wpforms_fomulas_hoursiff(hour_start,hour_end); 
                    }
                   return ok;
                });
            if( x.match(re) ){
                x = $.wpforms_fomulas_hours(x);
            }
            return x;
        }
        $.wpforms_fomulas_parse_date = function(str){

            if( str != "" ){
                str= str.trim();
                var strs = str.split("-");
                return new Date(strs[0],strs[1]-1,strs[2],0,0,0);
            }
            return new Date(str);
        }
        $.wpforms_cover_date_format = function(str,id){
            var date = flatpickr(id);
            date = date._initialDate;
            if( date != null ){
                    var m =parseInt(date.getMonth()) + 1;
                    str = date.getFullYear() + "-"+ m + "-" + date.getDate();
               }else{
                  str = 0;
               }
            return str;
        }
        $.wpforms_fomulas_datediff = function(first, second){
            return Math.round((second-first)/(1000*60*60*24));
        }
        $.wpforms_fomulas_hoursiff = function(start, end) {
            start = start.split(":");
            end = end.split(":");
            var startDate = new Date(0, 0, 0, start[0], start[1], 0);
            var endDate = new Date(0, 0, 0, end[0], end[1], 0);
            var diff = endDate.getTime() - startDate.getTime();
            var hours = Math.floor(diff / 1000 / 60 / 60);
            diff -= hours * 1000 * 60 * 60;
            var minutes = Math.floor(diff / 1000 / 60);
            // If using time pickers with 24 hours format, add the below line get exact hours
            if (hours < 0)
               hours = hours + 24;
           var minutes_hour = minutes/60;
           return hours + minutes_hour;
            //return (hours <= 9 ? "0" : "") + hours + ":" + (minutes <= 9 ? "0" : "") + minutes;
        }
        $(".wpforms-form").each(function(){
            $.wpforms_calculator($(this));
        })
        $("body").on("change keyup",".wpforms-form input, .wpforms-form select",function(e){
            var form = $(this).closest("form");
            var field_container = $(this).closest(".wpforms-field-calculator");
            if( field_container.length < 1 ){
                $.wpforms_calculator(form);
            }
        })
        $(document).on("wpformsAmountTotalCalculated",function(e , form, total){
            //$.wpforms_calculator(form);
        })
    })
})(jQuery);