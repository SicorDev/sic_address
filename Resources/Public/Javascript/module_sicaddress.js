TYPO3.jQuery(function() {
    checkboxListener();
});

var checkboxListener = function() {
    TYPO3.jQuery('.export input:checkbox').change(function() {
        TYPO3.jQuery(this).next().next().find('input:checkbox').prop('checked', TYPO3.jQuery(this).prop("checked"));
    });
};

var checkAll = function() {
    TYPO3.jQuery('.export input:checkbox').prop('checked', 'true')
};

