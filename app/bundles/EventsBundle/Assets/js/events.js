Mautic.eventOnLoad = function (container, response) {
    var panel = mQuery(container + ' #event-contacts');
    if (!panel.length) {
        return;
    }

    var selectEl  = mQuery(container + ' #contact-search');
    var attachBtn = panel.find('#attach-contacts')[0];
    var listEl    = panel.find('#attached-contact-list')[0];
    var emptyEl   = panel.find('#attached-empty')[0];

    var searchUrl = panel.data('search-url');
    var listUrl   = panel.data('contacts-url');
    var attachUrl = panel.data('attach-url');
    var detachTpl = panel.data('detach-url-template');
    var removeLbl = panel.data('remove-label');
    var placeholder = panel.data('search-placeholder');
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

    function refreshList() {
        fetch(listUrl)
            .then(function(r){ return r.json(); })
            .then(function(data){
                listEl.innerHTML = '';
                if (!data.contacts.length) {
                    emptyEl.style.display = 'block';
                    return;
                }
                emptyEl.style.display = 'none';
                data.contacts.forEach(function(c){
                    var li = document.createElement('li');
                    li.className = 'list-group-item d-flex justify-content-between align-items-center';
                    li.textContent = c.name + (c.email ? ' (' + c.email + ')' : '');
                    var btn = document.createElement('button');
                    btn.textContent = removeLbl;
                    btn.className = 'btn btn-sm btn-danger';
                    btn.addEventListener('click', function(){ detach(c.id); });
                    li.appendChild(btn);
                    listEl.appendChild(li);
                });
            });
    }

    function attach() {
        var ids = selectEl.val();
        if (!ids || !ids.length) return;
        fetch(attachUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken
            },
            body: JSON.stringify({contactIds: ids})
        }).then(function(){
            selectEl.val([]).trigger('chosen:updated');
            attachBtn.disabled = true;
            refreshList();
        });
    }

    function detach(id) {
        var url = detachTpl.replace('CONTACT_ID', id);
        fetch(url, {
            method: 'DELETE',
            headers: {'X-CSRF-Token': csrfToken}
        }).then(refreshList);
    }

    panel.find('#attach-contacts').on('click', attach);
    refreshList();
};
