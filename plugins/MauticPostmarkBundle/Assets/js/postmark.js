/**
 * Postmark campaign action JavaScript functionality
 */

// Global function to load templates based on selected server
Mautic.postmarkLoadTemplates = function(serverSelectElement) {
    var serverToken = mQuery(serverSelectElement).val();
    var form = mQuery(serverSelectElement).closest('form');
    var templateSelect = form.find('.postmark-template-select');
    
    if (!templateSelect.length) {
        // If template select doesn't exist, try multiple selectors
        templateSelect = form.find('[name*="template_alias"]');
        if (!templateSelect.length) {
            templateSelect = form.find('select[id*="template_alias"]');
        }
        if (!templateSelect.length) {
            templateSelect = form.find('select').filter(function() {
                return mQuery(this).attr('name') && mQuery(this).attr('name').indexOf('template_alias') >= 0;
            });
        }
    }
    
    if (!templateSelect.length) {
        console.log('Postmark: Template select element not found');
        return;
    }
    
    console.log('Postmark: Found template select element:', templateSelect);
    
    if (!serverToken) {
        // Clear template options if no server selected
        templateSelect.empty().append('<option value="">Please select a server first</option>');
        
        // Trigger chosen update
        if (templateSelect.hasClass('chosen-select') || templateSelect.next('.chosen-container').length > 0) {
            templateSelect.trigger('chosen:updated');
        }
        return;
    }

    // Show loading state
    templateSelect.empty().append('<option value="">Loading templates...</option>');
    templateSelect.prop('disabled', true);

    // Fetch templates from server
    mQuery.ajax({
        url: mauticAjaxUrl + '?action=plugin:postmark:getTemplates',
        type: 'POST',
        data: {
            server_token: serverToken
        },
        dataType: 'json',
        success: function(response) {
            console.log('Postmark: Received response:', response);
            templateSelect.empty();
            templateSelect.prop('disabled', false);

            if (response.success && response.templates && Object.keys(response.templates).length > 0) {
                // Add default option
                templateSelect.append('<option value="">Select a template</option>');
                
                console.log('Postmark: Adding templates to select:', response.templates);
                
                // Add template options - the response format is {"Template Name (alias)": "alias"}
                mQuery.each(response.templates, function(label, alias) {
                    console.log('Postmark: Adding option - Label:', label, 'Value:', alias);
                    templateSelect.append(mQuery('<option></option>')
                        .attr('value', alias)
                        .text(label)
                    );
                });
                
                console.log('Postmark: Final select element HTML:', templateSelect.html());
                
                // Trigger chosen update if the select is using Chosen plugin
                if (templateSelect.hasClass('chosen-select') || templateSelect.next('.chosen-container').length > 0) {
                    console.log('Postmark: Triggering Chosen update');
                    templateSelect.trigger('chosen:updated');
                }
            } else {
                var message = response.message || 'No templates found for this server';
                templateSelect.append('<option value="">' + message + '</option>');
                
                // Trigger chosen update for error message too
                if (templateSelect.hasClass('chosen-select') || templateSelect.next('.chosen-container').length > 0) {
                    templateSelect.trigger('chosen:updated');
                }
            }
        },
        error: function() {
            templateSelect.empty();
            templateSelect.prop('disabled', false);
            templateSelect.append('<option value="">Error loading templates</option>');
            
            // Trigger chosen update for error case
            if (templateSelect.hasClass('chosen-select') || templateSelect.next('.chosen-container').length > 0) {
                templateSelect.trigger('chosen:updated');
            }
        }
    });
};