function submitAtoz (choice) {
    // Reset other selections
    $('#sic_address_category').val('-1');
    $('#sic_address_query').val('');

    // Submit Atoz choice
    $('#sic_address_atoz').val(choice);
    $('#sic_address_search_form').submit();
    return false;
}

function submitCategory (choice) {
    // Reset other selections
    $('#sic_address_atoz').val('alle');
    $('#sic_address_query').val('');

    // Submit Category choice
    $('#sic_address_category').val(choice);
    $('#sic_address_search_form').submit();
    return false;
}

function submitDistance (choice) {
    // Submit Category choice
    $('#sic_address_distance').val(choice);
    $('#sic_address_search_form').submit();
    return false;
}

function submitMainCategory (choice) {
    // Reset other selections
    $('#sic_address_atoz').val('alle');
    $('#sic_address_query').val('');
    $('#sic_address_category').val('-1');

    // Submit Category choice
    $('#sic_address_main_category').val(choice);
    $('#sic_address_search_form').submit();
    return false;
}

function submitQuery () {
    // Reset other selections
    $('#sic_address_atoz').val('alle');
    $('#sic_address_category').val('-1');

    return false;
}

function toggleCategories (checkval) {
    $('.address_category_checkbox').prop('checked', checkval);
    submitCategories ();
}

function submitCategories () {
    // Reset other selections
    $('#sic_address_atoz').val('alle');
    $('#sic_address_query').val('');

    // Build csv string from checked boxes
    var res = "";
    $('.address_category_checkbox').each(function() {
        if($(this).prop('checked'))
            res += $(this).val() + ',';
    });

    // Submit Category choice
    $('#sic_address_category').val(res.slice(0, -1));
    $('#sic_address_search_form').submit();
    return false;
}

$(document).ready(function() {
    if (typeof documentReadyJsHook === "function") {
        documentReadyJsHook();
    }
    if (typeof fillCheckBoxes === "function") {
        fillCheckBoxes();
    }
});
