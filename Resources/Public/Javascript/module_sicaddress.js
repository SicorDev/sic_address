TYPO3.jQuery(function() {

});

var checkAll = function() {
    TYPO3.jQuery('.export input:checkbox').prop('checked', function(_, attr){ return !attr})
};

