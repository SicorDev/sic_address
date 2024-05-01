var sicQuery = {};
var propertiesSelector = 'div#sic_address_properties';

require(['jquery', 'TYPO3/CMS/Backend/Modal', 'jquery-ui/sortable'], function (jQuery, Modal, ui) {

    sicQuery = jQuery;
    sicModal = Modal;

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

        jQuery('.btn.delete').on('click', SicAddress.createDeleteButtonModal);
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

    getPanelObject: function(obj) {
        return sicQuery(obj).closest('.panel');
    },

    addPropertyClicked: function() {
        var form = sicQuery('form:last-child',propertiesSelector).clone();
        sicQuery(propertiesSelector).append(form);
    },

    btnOpenClicked: function(link) {
        sicQuery(SicAddress.getPanelObject(link))
        .toggleClass('panel-collapsed')
        .toggleClass('panel-visible');
    },

    headerOpenClicked: function(link) {
        sicQuery(SicAddress.getPanelObject(link))
        .toggleClass('panel-collapsed')
        .toggleClass('panel-visible');
    },

    togglePropertyState: function(link) {
        var uid = sicQuery(link).data('uid');
        var hidden = sicQuery('#hidden'+uid).val();
        sicQuery('#hidden'+uid).val(hidden > 0 ? 0 : 1);

        var form = sicQuery('#form'+uid);
        var formAction = sicQuery(form).attr('action');
        var formData = SicAddress.getFormData(form);

        sicQuery.post(formAction, formData, function(res){
            var html = sicQuery(link).html();
            if(hidden > 0) {
                html = html
                .replace('-unhide', '-hide')
                .replace('-unhide', '-hide')
                .replace('-unhide', '-hide');
            } else {
                html = html
                .replace('-hide', '-unhide')
                .replace('-hide', '-unhide')
                .replace('-hide', '-unhide');
            }
            sicQuery(link).html(html);
        })
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

    createDeleteButtonModal: function () {
        var link = this;
        var button = jQuery(this);
        var ajax = button.data('ajax');
        var deleteUri = button.data('deleteUri');
        var modalType = button.data('modalType');
        var modalContent = button.data('modalContent');
        var modalTitle = button.data('modalTitle');
        var modalButtonCancel = button.data('modalButtonCancel');
        var modalButtonDelete = button.data('modalButtonDelete');

        const modal = sicModal.advanced({
            title: modalTitle,
            content: modalContent,
            severity: modalType == 'error' ? top.TYPO3.Severity.error : top.TYPO3.Severity.warning,
            buttons: [
                {
                    text: modalButtonCancel,
                    btnClass: 'btn-primary',
                    trigger: function (event, modal) {
                        modal.hideModal();
                    }
                },
                {
                    text: modalButtonDelete,
                    btnClass: 'btn btn-danger',
                    active: true,
                    trigger: function (event, modal) {
                        modal.hideModal();
                        if (ajax) {
                            sicQuery.get(deleteUri, {}, function () {
                                sicQuery(SicAddress.getPanelObject(link)).remove();
                            });
                        } else location.href = deleteUri;
                    }
                }
            ]
        });
    },

    droppedObj: null,
    doubletCheckboxListener: function() {
        sicQuery('form#doublets input').change(function(){
            this.form.submit();
        })
    },

    ajaxShowDoubletteDatasets: function(frm,index) {
        var formAction = sicQuery(frm).attr('action');
        var formData = sicQuery(frm).serialize();

        sicQuery.post(formAction, formData, function(result) {
            result = SicAddress.markDoublets(result);
            sicQuery('td',result)
            .on('dragover', function(e){
                e.preventDefault();
            })
            .on('dragend', function(e){
                if(SicAddress.droppedObj) {
                    var switchFrm = sicQuery('form#switchDatasets');
                    sicQuery('.target', switchFrm).val(SicAddress.droppedObj.uid);
                    sicQuery('.property', switchFrm).val(SicAddress.droppedObj.property);
                    sicQuery('.source', switchFrm).val(e.target.dataset.uid);

                    var switchFormData = sicQuery(switchFrm).serialize();
                    var switchFormAction = sicQuery(switchFrm).attr('action');
                    sicQuery.post( switchFormAction, switchFormData, function(res) {
                        SicAddress.ajaxShowDoubletteDatasets(frm,index);
                    })
                }
            })
            .on('drop dragdrop', function(e){
                SicAddress.droppedObj = e.target.dataset;
            });
            sicQuery('#ajax'+index)
            .html(result);
        })
    },

    markDoublets: function(html) {
        var values = {};
        var htmlResult = sicQuery('<table class="table doublets ajax"></table>');
        sicQuery(htmlResult).append('<tbody></tbody>');
        sicQuery('tr', html).each(function(trIndex,tr){
            var trNew = sicQuery('<tr></tr>');
            var items = sicQuery('th,td', tr);
            var total = items.length;
            sicQuery(items).each(function(tdIndex,td){
                if( values[tdIndex] == undefined ) {
                    values[tdIndex] = {};
                }
                var value = td.innerText;
                var isDoublet = values[tdIndex] != undefined && values[tdIndex][value] != undefined;
                if(tdIndex && isDoublet) {
                    sicQuery(td).addClass('isDoublet');
                    total--;
                }
                values[tdIndex][value] = 1;
                sicQuery(trNew).append(td);
            });
            if(trIndex) sicQuery('a', trNew).css('color', (total>1) ? 'red' : 'green');
            sicQuery(htmlResult).append(trNew);
        });

        return htmlResult;
    },

    ajaxActivateField: function(a) {
        sicQuery('input.field_'+a.innerText).click();
        sicQuery(a).css('color','red').attr('href',null);
    },

    deleteDoublet: function(a) {
        sicQuery.post(a.dataset.href+'&ts='+Date.now(), {}, function(result) {
            sicQuery(a).closest('tr').html('');
        })
    },

    ajaxClose: function(a) {
        sicQuery(a).closest('table').html('');
    }

}
