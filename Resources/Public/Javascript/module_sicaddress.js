var addModuleRow = function() {
    var lastElement = jQuery("#fieldEditor tbody").find("tr:last-child");
    lastElement.clone().insertAfter(lastElement);
};