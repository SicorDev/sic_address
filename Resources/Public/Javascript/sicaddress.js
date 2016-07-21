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

function submitQuery () {
    // Reset other selections
    $('#sic_address_atoz').val('alle');
    $('#sic_address_category').val('-1');

    return false;
}

$(document).ready(function() {
    if (typeof documentReadyJsHook === "function") {
        documentReadyJsHook();
    }
});