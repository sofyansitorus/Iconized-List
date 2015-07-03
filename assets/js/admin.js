(function($){
    $(document).ready(function() {
        var iconizedPreview = function(){
            $( ".iconized" ).each(function() {
                var dropdown_select = $(this);
                var icon_size = dropdown_select.closest('.iconized-list-container').find('.iconized-list-size').val();
                dropdown_select.closest('.iconized-list-wrapper').find('i').removeClass().addClass('fa ' + icon_size + ' ' + dropdown_select.val());
            });
        }
        iconizedPreview();
        $(document).on("change",".iconized, .iconized-list-size",function(e){
            iconizedPreview();
        });
        var iconizedMax = function(){
            $( ".iconized-list-max" ).each(function() {
                var list_max = $(this).val();
                $(this).closest('.iconized-list-container').find('.iconized-list-item').each(function() {
                    var item_counter = $(this).data('counter');
                    if(item_counter <= list_max){
                        $(this).show("slow");
                    }else{
                        $(this).hide("slow");
                    }
                });
            });
            iconizedPreview();
        }
        iconizedMax();
        $(document).on("change",".iconized-list-max",function(e){
            iconizedMax();
        });
    });
})(jQuery);