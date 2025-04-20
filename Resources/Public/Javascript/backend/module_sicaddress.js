import jQuery from 'jquery';
import Sortable from 'sortablejs';
import Modal from '@typo3/backend/modal.js';

var propertiesSelector = 'div#sic_address_properties';

jQuery(document).ready(function() {
    var $propertiesContainer = jQuery(propertiesSelector);
    if ($propertiesContainer.length) {
        new Sortable($propertiesContainer[0], {
            animation: 150,
            onEnd: function(event) {
                SicAddress.sortableUpdateCallback(event);
            }
        });
    }

    jQuery('a#generateAction').click(function() {
        var forms = jQuery('form', $propertiesContainer);
        SicAddress.sendPropertyForms(forms.toArray(), this);
        return false;
    });

    jQuery('.sicaddproperty').click(function() {
        SicAddress.addPropertyClicked();
        return false;
    });

    jQuery('.sicopenproperty').click(function() {
        SicAddress.openPropertyClicked(this);
        return false;
    });

    jQuery('.sicinternexternradio').click(function() {
        this.form.submit();
    });

    jQuery('.sichideproperty').click(function() {
        SicAddress.togglePropertyState(this);
        return false;
    });

    jQuery('.sicexportcheckall').click(function() {
        jQuery('.export input:checkbox').prop('checked', 'true');
        return false;
    });

    jQuery('.sicexportcsv').click(function() {
        onclick="document.getElementById('type').value = 'CSV';"
    });

    jQuery('.sicexporthtml').click(function() {
        onclick="document.getElementById('type').value = 'HTML';"
    });

    jQuery('.sicdoubletcheckboxlistener').click(function() {
        SicAddress.doubletCheckboxListener();
    });

    jQuery('.btn.delete').on('click', SicAddress.createDeleteButtonModal);

    jQuery('.export input:checkbox').change(function() {
        jQuery(this).next().next().find('input:checkbox').prop('checked', jQuery(this).prop("checked"));
    });
});

var SicAddress = {
    sortableUpdateCallback: function(event) {
        var sorting = [];
        jQuery(propertiesSelector).children().each(function(index, form) {
            var item = jQuery('.panel', form)[0];
            sorting.push(item.dataset.uid);
            jQuery('input.sorting', form).val(index);
        });

        SicAddress.callSortAction(sorting);
    },

    callSortAction: function(sorting) {
        var form = jQuery('#sortingForm');
        jQuery('.sorting', form).val(sorting);

        var formData = SicAddress.getFormData(form);
        var formAction = jQuery(form).attr('action');
        jQuery.post(formAction, formData, function(res) {
            // nothing to do
        });
    },

    getPanelObject: function(obj) {
        return jQuery(obj).closest('.panel');
    },

    addPropertyClicked: function() {
        var form = jQuery('form:last-child', propertiesSelector).clone();
        jQuery(propertiesSelector).append(form);
    },

    openPropertyClicked: function(link) {
        jQuery(SicAddress.getPanelObject(link))
            .toggleClass('panel-collapsed')
            .toggleClass('panel-visible');
    },

    togglePropertyState: function(link) {
        var uid = jQuery(link).data('uid');

        var $hiddenField = jQuery('#hidden' + uid);
        var hidden = $hiddenField.val();
        $hiddenField.val(hidden > 0 ? 0 : 1);

        var form = jQuery('#form' + uid);
        var formAction = jQuery(form).attr('action');
        var formData = SicAddress.getFormData(form);

        var panel = jQuery('.panel', form);

        jQuery.post(formAction, formData, function(res) {
            if (hidden > 0) {
                jQuery(link).attr('title', 'Hide record');
                jQuery('svg use', link).attr('xlink:href', '/typo3/sysext/core/Resources/Public/Icons/T3Icons/sprites/actions.svg#actions-toggle-on');
                panel.removeClass('panel-danger');
                panel.addClass('panel-default');
            } else {
                jQuery(link).attr('title', 'Unhide record');
                jQuery('svg use', link).attr('xlink:href', '/typo3/sysext/core/Resources/Public/Icons/T3Icons/sprites/actions.svg#actions-toggle-off');
                panel.removeClass('panel-default');
                panel.addClass('panel-danger');
            }
        });
    },

    sendPropertyForms: function(forms, actionLink) {
        var form = forms.pop();
        if (form) {
            var formData = SicAddress.getFormData(form);
            var formAction = jQuery(form).attr('action');

            jQuery('.panel', form).removeClass('panel-danger').css('opacity', 0.5);
            jQuery('.panel *', form).removeClass('has-error');
            jQuery('.panel .field-error', form).html('');
            jQuery.post(formAction, formData, function(errorMessages) {
                jQuery('.panel', form).css('opacity', 1);
                if (errorMessages.length === 0) {
                    jQuery('.panel', form).remove();
                    jQuery('.field-title input', form).attr('disabled', true);
                    if (forms.length) SicAddress.sendPropertyForms(forms, actionLink);
                    else {
                        var generateActionText = jQuery('#generateAction').text();
                        jQuery('#typo3-inner-docbody').html('<h1 class="generate">' + generateActionText + '...</h1>');

                        location.href = jQuery(actionLink).attr('href');
                    }
                } else {
                    jQuery('.panel', form).addClass('panel-danger');
                    for (var field in errorMessages) {
                        jQuery('.field-' + field, form).addClass('has-error');
                        jQuery('.field-' + field + '-error', form).html(errorMessages[field]);
                    }
                }
            });
        }
    },

    getFormData: function(form) {
        var formData = {};
        jQuery(':input', form).each(function(index, field) {
            var fieldName = jQuery(field).attr('name');
            var fieldType = jQuery(field).attr('type');
            var fieldValue = jQuery(field).val();

            if (fieldType != 'checkbox' || field.checked) {
                formData[fieldName] = fieldValue;
            }
        });

        return formData;
    },

    createDeleteButtonModal: function() {
        var link = this;
        var button = jQuery(this);
        var ajax = button.data('ajax');
        var deleteUri = button.data('deleteUri');
        var modalType = button.data('modalType');
        var modalContent = button.data('modalContent');
        var modalTitle = button.data('modalTitle');
        var modalButtonCancel = button.data('modalButtonCancel');
        var modalButtonDelete = button.data('modalButtonDelete');

        const modal = Modal.advanced({
            title: modalTitle,
            content: modalContent,
            severity: modalType == 'error' ? top.TYPO3.Severity.error : top.TYPO3.Severity.warning,
            buttons: [
                {
                    text: modalButtonCancel,
                    btnClass: 'btn-primary',
                    trigger: function(event, modal) {
                        modal.hideModal();
                    }
                },
                {
                    text: modalButtonDelete,
                    btnClass: 'btn btn-danger',
                    active: true,
                    trigger: function(event, modal) {
                        modal.hideModal();
                        if (ajax) {
                            jQuery.get(deleteUri, {}, function() {
                                jQuery(SicAddress.getPanelObject(link)).remove();
                            });
                        } else location.href = deleteUri;
                    }
                }
            ]
        });
    },

    droppedObj: null,
    doubletCheckboxListener: function() {
        jQuery('form#doublets input').change(function() {
            this.form.submit();
        });
    },

    ajaxShowDoubletteDatasets: function(frm, index) {
        var formAction = jQuery(frm).attr('action');
        var formData = jQuery(frm).serialize();

        jQuery.post(formAction, formData, function(result) {
            result = SicAddress.markDoublets(result);
            jQuery('td', result)
                .on('dragover', function(e) {
                    e.preventDefault();
                })
                .on('dragend', function(e) {
                    if (SicAddress.droppedObj) {
                        var switchFrm = jQuery('form#switchDatasets');
                        jQuery('.target', switchFrm).val(SicAddress.droppedObj.uid);
                        jQuery('.property', switchFrm).val(SicAddress.droppedObj.property);
                        jQuery('.source', switchFrm).val(e.target.dataset.uid);

                        var switchFormData = jQuery(switchFrm).serialize();
                        var switchFormAction = jQuery(switchFrm).attr('action');
                        jQuery.post(switchFormAction, switchFormData, function(res) {
                            SicAddress.ajaxShowDoubletteDatasets(frm, index);
                        });
                    }
                })
                .on('drop dragdrop', function(e) {
                    SicAddress.droppedObj = e.target.dataset;
                });
            jQuery('#ajax' + index)
                .html(result);
        });
    },

    markDoublets: function(html) {
        var values = {};
        var htmlResult = jQuery('<table class="table doublets ajax"></table>');
        jQuery(htmlResult).append('<tbody></tbody>');
        jQuery('tr', html).each(function(trIndex, tr) {
            var trNew = jQuery('<tr></tr>');
            var items = jQuery('th, td', tr);
            var total = items.length;
            jQuery(items).each(function(tdIndex, td) {
                if (values[tdIndex] == undefined) {
                    values[tdIndex] = {};
                }
                var value = td.innerText;
                var isDoublet = values[tdIndex] != undefined && values[tdIndex][value] != undefined;
                if (tdIndex && isDoublet) {
                    jQuery(td).addClass('isDoublet');
                    total--;
                }
                values[tdIndex][value] = 1;
                jQuery(trNew).append(td);
            });
            if (trIndex) jQuery('a', trNew).css('color', (total > 1) ? 'red' : 'green');
            jQuery(htmlResult).append(trNew);
        });

        return htmlResult;
    },

    ajaxActivateField: function(a) {
        jQuery('input.field_' + a.innerText).click();
        jQuery(a).css('color', 'red').attr('href', null);
    },

    deleteDoublet: function(a) {
        jQuery.post(a.dataset.href + '&ts=' + Date.now(), {}, function(result) {
            jQuery(a).closest('tr').html('');
        });
    },

    ajaxClose: function(a) {
        jQuery(a).closest('table').html('');
    }
};

export default SicAddress;
