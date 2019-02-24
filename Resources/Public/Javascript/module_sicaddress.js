var sicQuery = {};
var propertiesSelector = 'div#sic_address_properties';

require(['jquery', 'jquery-ui/sortable'], function(jQuery, cookie, ui) {
    
    sicQuery = jQuery;

    SicAddress.checkboxListener();

    jQuery(function() {
        $propertiesContainer = jQuery(propertiesSelector);
        $propertiesContainer.sortable({
            axis: 'y',
            delay: 150
        });
        $propertiesContainer.on( "sortupdate", function( event, ui ) {
            SicAddress.sortableUpdateCallback(event,ui);
        });

        jQuery('a#generateAction').click(function(){
            var forms = jQuery('form', $propertiesContainer);

            SicAddress.sendPropertyForms(forms.toArray(),this);

            return false;
        });

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

    sortableUpdateCallback: function(a,b) {
        var sorting = [];
        $propertiesContainer.children().each(function(index,form){
            var item = sicQuery('.panel',form)[0];
            sorting.push(item.dataset.uid);
            sicQuery('input.sorting', form).val(index);
        });

        SicAddress.callSortAction(sorting);
    },

    callSortAction: function(sorting) {
        var form = sicQuery('#sortingForm');
        sicQuery('.sorting', form).val(sorting);

        var formData = SicAddress.getFormData(form);
        var formAction = sicQuery(form).attr('action');
        sicQuery.post(formAction, formData, function(res){
            // nothing to do
        })
    },

    addPropertyClicked: function() {
        var form = sicQuery('form:last-child',propertiesSelector).clone();
        sicQuery(propertiesSelector).append(form);
    },

    btnOpenClicked: function(link) {
        sicQuery(link.parentNode.parentNode.parentNode.parentNode.parentNode)
        .toggleClass('panel-collapsed')
        .toggleClass('panel-visible');
    },
    
    headerOpenClicked: function(link) {
        sicQuery(link.parentNode.parentNode.parentNode)
        .toggleClass('panel-collapsed')
        .toggleClass('panel-visible');
    },

    sendPropertyForms: function(forms,actionLink) {
        var form = forms.pop();
        if(form) {
            var formData = SicAddress.getFormData(form);
            var formAction = sicQuery(form).attr('action');

            sicQuery('.panel',form).removeClass('panel-danger').css('opacity',0.5);
            sicQuery('.panel *',form).removeClass('has-error');
            sicQuery('.panel .field-error',form).html('');
            sicQuery.post(formAction, formData, function(errorMessages){
                sicQuery('.panel',form).css('opacity',1);
                if(errorMessages.length == 0) {
                    sicQuery('.panel',form).remove();
                    sicQuery('.field-title input',form).attr('disabled',true);
                    if(forms.length) SicAddress.sendPropertyForms(forms,actionLink);
                    else {
                        var generateActionText = sicQuery('#generateAction').text();
                        sicQuery('#typo3-inner-docbody').html('<h1 class="generate">'+generateActionText+'...</h1>');

                        location.href = sicQuery(actionLink).attr('href');
                    }
                } else {
                    sicQuery('.panel',form).addClass('panel-danger');
                    for(field in errorMessages) {
                        sicQuery('.field-'+field,form).addClass('has-error');
                        sicQuery('.field-'+field+'-error',form).html(errorMessages[field]);
                    };
                }
            });
        }
    },

    getFormData: function(form) {
        var formData = {};
        sicQuery(':input', form).each(function(index,field){
            var fieldName = sicQuery(field).attr('name');
            var fieldType = sicQuery(field).attr('type');
            var fieldValue = sicQuery(field).val();

            if(fieldType != 'checkbox' || field.checked) {
                formData[fieldName] = fieldValue;
            }
        });

        return formData;
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
                text: modalButtonCancel,
                btnClass: 'btn btn-default',
                trigger: function() {
                    top.TYPO3.Modal.dismiss();
                }
            },
            {
                text: modalButtonDelete,
                btnClass: 'btn btn-danger',
                active: true,
                trigger: function() {
                    top.TYPO3.Modal.dismiss();
                    if(ajax) {
                        sicQuery.get(deleteUri,{},function(){
                            sicQuery(link.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode).remove();
                        });
                    } else location.href = deleteUri;
                }
            }
        ]);
    }

}