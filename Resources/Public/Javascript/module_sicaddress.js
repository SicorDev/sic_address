var sicQuery = {};

require(['jquery'], function(jQuery, cookie, ui) {
    
    sicQuery = jQuery;

    SicAddress.checkboxListener();

    jQuery(function() {
        jQuery('.btn.delete').click(SicAddress.btnDeleteClicked);
    });

});

var SicAddress = {

    checkboxListener: function() {
        sicQuery('.export input:checkbox').change(function() {
            sicQuery(this).next().next().find('input:checkbox').prop('checked', sicQuery(this).prop("checked"));
        });
    },
    
    checkAll: function() {
        sicQuery('.export input:checkbox').prop('checked', 'true');
    },

    btnDeleteClicked: function() {
        var link = this;
        var ajax = sicQuery(this).data('ajax');
        var deleteUri = sicQuery(this).data('deleteUri');
        var modalType = sicQuery(this).data('modalType');
        var modalContent = sicQuery(this).data('modalContent');
        var modalTitle = sicQuery(this).data('modalTitle');
        var modalButtonCancel = sicQuery(this).data('modalButtonCancel');
        var modalButtonDelete = sicQuery(this).data('modalButtonDelete');
        top.TYPO3.Modal.confirm(modalTitle, modalContent, modalType=='error' ? top.TYPO3.Severity.error : top.TYPO3.Severity.warning, [
            {
                text: modalButtonDelete,
                btnClass: 'btn btn-danger',
                active: true,
                trigger: function() {
                    top.TYPO3.Modal.dismiss();
                    if(ajax) {
                        jQuery.get(deleteUri,{},function(){
                            sicQuery(link.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode).remove();
                        });
                    } else location.href = deleteUri;
                }
            }, {
                text: modalButtonCancel,
                btnClass: 'btn btn-default',
                trigger: function() {
                    top.TYPO3.Modal.dismiss();
                }
            }
        ]);
    }

}