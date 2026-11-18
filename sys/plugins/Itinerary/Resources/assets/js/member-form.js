$(document).ready(function() {

    var showFreeItemModal = function() {
        var $modal = $('#modalFreeItem');
        $modal.find('.form-control').each(function() {
            $(this).val('');
        });
        $modal.find('select option').get(0).selected = true;
        $modal.modal('show');
    };

    var addFieldItem = function(title, name, type, placeholder, attr, priority) {
        var $ul = $('#listFields');
        var $li = $("<li><span class='badge badge-light'>" + title + "<i class='ml-1 fa fa-close remove remove-field'></i></span></li>")
            .attr('data-name', name)
            .attr('data-type', type)
            .attr('data-placeholder', placeholder)
            .attr('data-attr', attr)
            .attr('data-priority', priority)
            .appendTo($ul);
        $('.remove-field').off('click').on('click', function() {
            $(this).parents('li').remove();
        });
    };

    var getFieldsData = function() {
        var fields = [];
        var $ul = $('#listFields');
        var tmpPriority = 0;
        $ul.find('li').each(function() {
            tmpPriority += 10;
            fields.push({
                name: $(this).data('name'),
                type: $(this).data('type'),
                attr: $(this).data('attr'),
                placeholder: $(this).data('placeholder'),
                priority: $(this).data('priority') ? $(this).data('priority') : tmpPriority
            });
        });
        return fields;
    };

    $('.remove-field').on('click', function() {
        $(this).parents('li').remove();
    });
    $('#btnAddItem').on('click', function() {
        var $select = $('#selectField');
        var val = $select.val();
        if (val == 'misc') {
            showFreeItemModal();
        } else {
            var $option = $select.find("option[value='"+ val + "']");
            if ($option.length) {
                addFieldItem($option.text(), val, $option.data('type'), $option.data('placeholder'), $option.data('attr'), $option.data('priority'));
            }
        }
    });
    $('#btnAddFreeItem').on('click', function() {
        var $modal = $('#modalFreeItem');
        $modal.modal('hide');
       var $form = $('#frmFreeItem');
       var name = $form.find("[name='field_name']").val();
       var type = $form.find("[name='field_type']").val();
       if (name && type) {
           var placeholder = $form.find("[name='field_placeholder']").val();
           var attr = $form.find("[name='field_attributes']").val();
           addFieldItem(name, name, type, placeholder, attr);
       }
    });
    $('#btnSave').on('click', function() {
        var $form = $('#frmMain');
        $form.find("input[name='fields']").remove();
        $("<input type='text' value='" + JSON.stringify(getFieldsData()) + "' name='fields'>").appendTo($form);
        $form.submit();
    });
    $('#btnPreview').on('click', function() {
        var $form = $('#frmMain');
        var originalAction = $form.attr('action');
        $form.find("input[name='fields']").remove();
        $form.find("input[name='preview']").remove();
        $("<input type='text' value='" + JSON.stringify(getFieldsData()) + "' name='fields'>").appendTo($form);
        $("<input type='text' name='preview' value='true'>").appendTo($form);
        $form.attr('target', '_blank');
        $form.submit();
        $form.removeAttr('target');
        $form.find("input[name='fields']").remove();
        $form.find("input[name='preview']").remove();
    });
});
