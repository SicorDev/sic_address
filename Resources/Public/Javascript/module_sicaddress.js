TYPO3.jQuery(function() {
    checkboxHelper();
});

var checkboxHelper = function() {
    TYPO3.jQuery("input[type='checkbox']").on("change", function() {
        if(TYPO3.jQuery(this).prop("checked")) {
            TYPO3.jQuery(this).val(1);
        }
        else {
            TYPO3.jQuery(this).val(0);
        }
    });
};
