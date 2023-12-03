(function($) {
    "use strict";
    $( document ).ready( function () { 
        var data_class = ["number_format_symbols","number_format_symbols_position","number_format_thousand_sep","number_format_decimal_sep","number_format_num_decimals"];
        $("body").on("change",".wpforms-field-option-row-number_format input",function(){
            if( $(this).is(":checked") ) {
                $.each(data_class, function( index, value ) {
                  $(".wpforms-field-option-row-"+value).removeClass("wpforms-hidden");
                });
            }else{
                $.each(data_class, function( index, value ) {
                  $(".wpforms-field-option-row-"+value).addClass("wpforms-hidden");
                });
            }
        })
        $("body").on("click",".wpforms-field-option-row-formula textarea",function(e){
            var container = $(this).closest('.wpforms-field-option-row');
            var id = $(this).attr("name").match(/\d+/)[0];
            if (typeof wpforms_calculator !== 'undefined' && wpforms_calculator !== null) {
             var tributeAttributes = {
                autocompleteMode: true,
                noMatchTemplate: "",
                values: wpforms_calculator.data,
                selectTemplate: function(item) {
                  if (typeof item === "undefined") return null;
                  if (this.range.isContentEditable(this.current.element)) {
                    return (
                      '<span contenteditable="false"><a>' +
                      item.original.key +
                      "</a></span>"
                    );
                  }

                  return item.original.value;
                },
                menuItemTemplate: function(item) {
                  return item.string;
                }
              };
              var tributeAutocompleteTestArea = new Tribute(
                Object.assign(
                  {
                    menuContainer: document.getElementById("wpforms-field-option-row-"+id+"-formula"),
                    replaceTextSuffix: "",
                  },
                  tributeAttributes
                )
              );
              tributeAutocompleteTestArea.attach(
                document.getElementById("wpforms-field-option-"+id+"-formula")
              );
            }

        })  
    })
})(jQuery);