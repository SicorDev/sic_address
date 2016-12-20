function submitAtoz (choice) {
    // Reset other selections
    jQuery('#sic_address_main_category').val('-1');
    jQuery('#sic_address_category').val('-1');
    jQuery('#sic_address_filter').val('-1');
    jQuery('#sic_address_query').val('');

    // Submit Atoz choice
    jQuery('#sic_address_atoz').val(choice);
    jQuery('#sic_address_search_form').submit();
    return false;
}

function submitCategory (choice) {
    // Reset other selections
    jQuery('#sic_address_atoz').val('alle');
    jQuery('#sic_address_query').val('');

    // Submit Category choice
    jQuery('#sic_address_filter').val(choice);
    jQuery('#sic_address_search_form').submit();
    return false;
}

function submitFilter (choice) {
    // Reset other selections
    jQuery('#sic_address_atoz').val('alle');
    jQuery('#sic_address_query').val('');

    // Submit Filter choice
    jQuery('#sic_address_filter').val(choice);
    jQuery('#sic_address_search_form').submit();
    return false;
}

function submitDistance (choice) {
    // Submit Distance choice
    jQuery('#sic_address_distance').val(choice);
    jQuery('#sic_address_search_form').submit();
    return false;
}

function submitMainCategory (choice) {
    // Reset other selections
    jQuery('#sic_address_atoz').val('alle');
    jQuery('#sic_address_query').val('');
    jQuery('#sic_address_category').val('-1');
    jQuery('#sic_address_filter').val('-1');

    // Submit Main Category choice
    jQuery('#sic_address_main_category').val(choice);
    jQuery('#sic_address_search_form').submit();
    return false;
}

function submitQuery () {
    // Reset other selections
    jQuery('#sic_address_atoz').val('alle');
    jQuery('#sic_address_category').val('-1');
    jQuery('#sic_address_filter').val('-1');

    return false;
}

function toggleCategories (checkval) {
    jQuery('.address_category_checkbox').prop('checked', checkval);
    submitCategories ();
}

function submitCategories () {
    // Reset other selections
    jQuery('#sic_address_atoz').val('alle');
    jQuery('#sic_address_query').val('');

    // Build csv string from checked boxes
    var res = "";
    jQuery('.address_category_checkbox').each(function() {
        if(jQuery(this).prop('checked'))
            res += jQuery(this).val() + ',';
    });

    // Submit Category choice
    jQuery('#sic_address_category').val(res.slice(0, -1));
    jQuery('#sic_address_search_form').submit();
    return false;
}

jQuery(document).ready(function() {
    if (typeof documentReadyJsHook === "function") {
        documentReadyJsHook();
    }
    if (typeof fillCheckBoxes === "function") {
        fillCheckBoxes();
    }
});
