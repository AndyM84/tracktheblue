$('[name="collapseGroup"]').on('change',function() {
    if($(this).val() === "yes") {
        $('#collapseOne').collapse('show');
    }else {
        $('collapseOne').collapse('hide');
    }
});