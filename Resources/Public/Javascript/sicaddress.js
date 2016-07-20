function submitAtoz (choice) {
    $('#atoz').val(choice);
    $('#sic_address_search_form').submit();
    return false;
}
function submitCategorySel () {
    $('#sic_address_search_form').submit();
}
function submitCategoryBread (choice) {
    $('#category').val(choice);
    $('#sic_address_search_form').submit();
    return false;
}
