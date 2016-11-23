jQuery(function() {
    checkboxListener();
});

var checkboxListener = function() {
    jQuery('.export input:checkbox').change(function() {
        jQuery(this).next().next().find('input:checkbox').prop('checked', jQuery(this).prop("checked"));
    });
};

var checkAll = function() {
    jQuery('.export input:checkbox').prop('checked', 'true')
};

