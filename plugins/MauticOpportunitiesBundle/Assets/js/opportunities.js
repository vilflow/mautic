Mautic.opportunityOnLoad = function (container, response) {
    var tablePanel = mQuery(container + ' #opportunity-contacts-table');
    if (!tablePanel.length) {
        return;
    }

    var selectEl  = mQuery(container + ' #contact-search');
    var attachBtn = mQuery(container + ' #attach-contacts')[0];

    var searchUrl = selectEl.data('search-url');
    var listUrl   = tablePanel.data('contacts-url');
    var attachUrl = attachBtn.getAttribute('data-attach-url');
    var detachTpl = tablePanel.data('detach-url-template');
    var removeLbl = tablePanel.data('remove-label');
    var placeholder = selectEl.data('search-placeholder');
    var csrfToken = window.mauticAjaxCsrf;

    selectEl.ajaxChosen({
        type: 'GET',
        url: searchUrl,
        dataType: 'json',
        afterTypeDelay: 300,
        minTermLength: 1,
        jsonTermKey: 'q',
        dataCallback: function(data) {
            data.exclude = selectEl.val();
            return data;
        }
    }, function(data) {
        return data.results.map(function(c){
            return {value: c.id, text: c.name + (c.email ? ' (' + c.email + ')' : '')};
        });
    }, {
        width: '100%',
        placeholder_text_multiple: placeholder,
        allow_single_deselect: true
    });

    selectEl.on('change', function() {
        attachBtn.disabled = !selectEl.val() || selectEl.val().length === 0;
    });

    function refreshContactsTable() {
        return fetch(listUrl, {credentials: 'same-origin'})
            .then(function(r){
                if (!r.ok) {
                    throw new Error('Failed to load contacts');
                }
                return r.json();
            })
            .then(function(data){
                var contacts = data.contacts || [];
                var tableHtml = renderContactsTable(contacts);
                tablePanel[0].innerHTML = tableHtml;
                
                // Update the total contacts count
                var totalContactsEl = mQuery('#total-contacts');
                if (totalContactsEl.length) {
                    totalContactsEl.text(contacts.length);
                }
                
                // Attach event listeners to remove buttons
                mQuery(tablePanel).find('.btn-remove-contact').on('click', function(e){
                    e.preventDefault();
                    var contactId = mQuery(this).data('contact-id');
                    var contactName = mQuery(this).data('contact-name');
                    if (confirm('Are you sure you want to remove ' + contactName + '?')) {
                        detach(contactId);
                    }
                });
            });
    }
    
    function renderContactsTable(contacts) {
        if (!contacts.length) {
            return '<div class="panel"><div class="panel-body text-center">' +
                   '<h4>No attached contacts</h4>' +
                   '<p class="text-muted">Use the form above to attach contacts to this opportunity.</p>' +
                   '</div></div>';
        }
        
        var html = '<div class="table-responsive">' +
                   '<table class="table table-hover" id="opportunityContactsTable">' +
                   '<thead><tr>' +
                   '<th width="1%"><input type="checkbox" data-toggle="checkall" data-target="#opportunityContactsTable"></th>' +
                   '<th>Name</th>' +
                   '<th class="visible-md visible-lg">Email</th>' +
                   '<th class="visible-md visible-lg">Location</th>' +
                   '<th>Stage</th>' +
                   '<th class="visible-md visible-lg">Points</th>' +
                   '<th class="visible-md visible-lg">Last Active</th>' +
                   '<th class="visible-md visible-lg">ID</th>' +
                   '<th width="1%" class="text-center">Actions</th>' +
                   '</tr></thead><tbody>';
        
        contacts.forEach(function(contact) {
            var name = contact.name || 'Anonymous';
            var email = contact.email || '';
            var location = [contact.city, contact.state || contact.country].filter(Boolean).join(', ');
            var lastActive = contact.lastActive ? new Date(contact.lastActive).toLocaleDateString() : '';
            
            html += '<tr>' +
                    '<td><input type="checkbox" name="ids[]" value="' + contact.id + '"></td>' +
                    '<td><a href="/s/contacts/view/' + contact.id + '">' +
                    '<div>' + name + '</div>' +
                    '<div class="small">' + email + '</div></a></td>' +
                    '<td class="visible-md visible-lg">' + email + '</td>' +
                    '<td class="visible-md visible-lg">' + location + '</td>' +
                    '<td class="text-center">' + (contact.stage ? '<span class="label label-default">' + contact.stage + '</span>' : '') + '</td>' +
                    '<td class="visible-md visible-lg text-center"><span class="label label-default">' + (contact.points || 0) + '</span></td>' +
                    '<td class="visible-md visible-lg">' + lastActive + '</td>' +
                    '<td class="visible-md visible-lg">' + contact.id + '</td>' +
                    '<td class="text-center">' +
                    '<button class="btn btn-sm btn-danger btn-remove-contact" data-contact-id="' + contact.id + '" data-contact-name="' + name + '" title="' + removeLbl + '">' +
                    '<i class="ri-close-line"></i></button></td>' +
                    '</tr>';
        });
        
        html += '</tbody></table></div>';
        return html;
    }

    function attach() {
        var ids = selectEl.val();
        if (!ids || !ids.length) return;
        fetch(attachUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken
            },
            body: JSON.stringify({contactIds: ids})
        }).then(function(){
            selectEl.val([]).trigger('chosen:updated');
            attachBtn.disabled = true;
            return refreshContactsTable();
        }).then(function(){
            var flashMessage = Mautic.addInfoFlashMessage('Contacts added successfully.');
            Mautic.setFlashes(flashMessage);
        }).catch(function(){
            var flashMessage = Mautic.addErrorFlashMessage('Failed to add contacts.');
            Mautic.setFlashes(flashMessage);
        });
    }

    function detach(id) {
        var url = detachTpl.replace('CONTACT_ID', id);
        fetch(url, {
            method: 'DELETE',
            credentials: 'same-origin',
            headers: {'X-CSRF-Token': csrfToken}
        }).then(function(){
            return refreshContactsTable();
        }).then(function(){
            var flashMessage = Mautic.addInfoFlashMessage('Contact removed successfully.');
            Mautic.setFlashes(flashMessage);
        }).catch(function(){
            var flashMessage = Mautic.addErrorFlashMessage('Failed to remove contact.');
            Mautic.setFlashes(flashMessage);
        });
    }

    mQuery(attachBtn).on('click', attach);
    refreshContactsTable();
};