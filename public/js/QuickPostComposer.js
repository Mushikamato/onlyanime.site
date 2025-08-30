/**
 * QuickPost Composer - Integrates with your existing file upload system
 * Uses SAME endpoints and methods as normal post creation
 */

// Wait for jQuery and other scripts to load properly
$(document).ready(function() {
    console.log('QuickPost: Document ready, initializing...');
    
    // Wait for other scripts to load
    setTimeout(function() {
        initQuickPostSystem();
    }, 1000);
});

function initQuickPostSystem() {
    console.log('QuickPost: Initializing system...');
    
    // Initialize attachments if not exists (same as normal post)
    if (typeof window.attachments === 'undefined') {
        window.attachments = [];
    }
    
    // Create QuickPostSimple object
    window.QuickPostSimple = {
        
        init: function() {
            console.log('QuickPostSimple: Initialized');
            this.bindEvents();
            this.setupFileUpload();
        },
        
        bindEvents: function() {
            var self = this;
            
            // Expand composer when clicking collapsed area
            $(document).on('click', '.composer-collapsed', function(e) {
                e.preventDefault();
                console.log('QuickPost: Expanding composer');
                self.expand();
            });
            
            // Collapse when clicking header or close button
            $(document).on('click', '.composer-header h6, .composer-header .btn-link', function(e) {
                e.preventDefault();
                console.log('QuickPost: Collapsing composer');
                self.collapse();
            });
            
            // Prevent form elements from causing page reload - AGGRESSIVE prevention
            $(document).on('change', '#quickPostForm select, #quickPostForm input[type="checkbox"]', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                console.log('QuickPost: Form field changed (prevented reload):', $(this).attr('name'), $(this).val());
                return false;
            });
            
            // Specifically target content type dropdown
            $(document).on('change', '#quick_content_type', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                console.log('QuickPost: Content type changed to:', $(this).val());
                return false;
            });
            
            // Prevent any accidental form submission
            $(document).on('keypress', '#quickPostForm input, #quickPostForm textarea, #quickPostForm select', function(e) {
                if (e.which === 13 && e.target.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                    console.log('QuickPost: Prevented accidental form submission via Enter key');
                }
            });
            
            // Form submission - ONLY via submit button click
            $(document).on('submit', '#quickPostForm', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('QuickPost: Form submitted');
                self.submitPost();
            });
            
            // Clear form button
            $(document).on('click', '.draft-clear-button', function(e) {
                e.preventDefault();
                self.clearForm();
            });
        },
        
        setupFileUpload: function() {
            var self = this;
            
            // Connect to your existing Files button (from post-create-actions)
            $(document).on('click', '.file-upload-button', function(e) {
                e.preventDefault();
                console.log('QuickPost: Files button clicked');
                
                // Create temporary file input (since we don't have dropzone)
                var fileInput = $('<input type="file" multiple accept="image/*,video/*,audio/*" style="display:none;">');
                
                // Handle file selection
                fileInput.on('change', function(e) {
                    var files = e.target.files;
                    console.log('QuickPost: Files selected:', files.length);
                    
                    if (files.length > 0) {
                        self.uploadFiles(files);
                    }
                });
                
                // Trigger file picker
                fileInput.click();
            });
        },
        
        uploadFiles: function(files) {
            var self = this;
            console.log('QuickPost: Starting file upload for', files.length, 'files');
            
            // Show upload progress area
            $('.uploaded-files-preview').html(`
                <div class="upload-progress">
                    <h6>Uploading ${files.length} file(s)...</h6>
                </div>
            `);
            
            // Upload each file
            Array.from(files).forEach(function(file, index) {
                self.uploadSingleFile(file, index);
            });
        },
        
        uploadSingleFile: function(file, index) {
            var self = this;
            console.log(`QuickPost: Uploading file ${index + 1}:`, file.name);
            
            // Add file preview
            var previewHtml = `
                <div class="file-upload-item" data-index="${index}">
                    <div class="file-info d-flex align-items-center mb-2">
                        <div class="file-icon mr-2">
                            ${file.type.startsWith('image/') ? '🖼️' : file.type.startsWith('video/') ? '🎥' : '📄'}
                        </div>
                        <div class="file-details flex-grow-1">
                            <div class="file-name small font-weight-bold">${file.name}</div>
                            <div class="file-status small text-muted">Uploading...</div>
                            <div class="file-size small text-muted">${(file.size / 1024 / 1024).toFixed(2)} MB</div>
                        </div>
                        <div class="file-actions">
                            <div class="upload-progress-bar">
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('.uploaded-files-preview .upload-progress').append(previewHtml);
            
            // Prepare form data (same as your normal post upload)
            var formData = new FormData();
            formData.append('file', file);
            
            // Get CSRF token (same way as normal post)
            var token = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val();
            
            // Upload using SAME endpoint as normal post creation
            $.ajax({
                url: '/attachment/upload/post',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': token
                },
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    // Progress handler
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = (evt.loaded / evt.total) * 100;
                            $(`.file-upload-item[data-index="${index}"] .progress-bar`).css('width', percentComplete + '%');
                        }
                    }, false);
                    return xhr;
                },
                success: function(response) {
                    console.log(`QuickPost: Upload success for ${file.name}:`, response);
                    
                    // Update status
                    var fileItem = $(`.file-upload-item[data-index="${index}"]`);
                    fileItem.find('.file-status').html('<span class="text-success">✓ Uploaded successfully</span>');
                    fileItem.find('.progress-bar').addClass('bg-success').css('width', '100%');
                    
                    // Store attachment (SAME format as normal post)
                    if (response && response.attachmentID) {
                        var attachment = {
                            id: response.attachmentID,
                            attachmentID: response.attachmentID,
                            type: response.type || file.type,
                            path: response.path || '',
                            filename: file.name
                        };
                        
                        window.attachments.push(attachment);
                        console.log('QuickPost: Attachment stored:', attachment);
                        console.log('QuickPost: Total attachments:', window.attachments.length);
                        
                        // Add remove button
                        fileItem.find('.file-actions').append(`
                            <button type="button" class="btn btn-sm btn-outline-danger remove-file" data-attachment-id="${response.attachmentID}">
                                <i class="fas fa-trash"></i>
                            </button>
                        `);
                        
                    } else {
                        console.error('QuickPost: Invalid upload response:', response);
                        fileItem.find('.file-status').html('<span class="text-danger">✗ Upload failed - invalid response</span>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(`QuickPost: Upload failed for ${file.name}:`, xhr.responseText);
                    
                    var fileItem = $(`.file-upload-item[data-index="${index}"]`);
                    fileItem.find('.file-status').html(`<span class="text-danger">✗ Upload failed: ${error}</span>`);
                    fileItem.find('.progress-bar').addClass('bg-danger').css('width', '100%');
                }
            });
            
            // Handle file removal
            $(document).on('click', '.remove-file', function(e) {
                e.preventDefault();
                var attachmentId = $(this).data('attachment-id');
                var fileItem = $(this).closest('.file-upload-item');
                
                // Remove from attachments array
                window.attachments = window.attachments.filter(function(att) {
                    return att.id !== attachmentId;
                });
                
                // Remove from UI
                fileItem.remove();
                
                console.log('QuickPost: File removed. Remaining attachments:', window.attachments.length);
            });
        },
        
        submitPost: function() {
            console.log('QuickPost: Submitting post...');
            console.log('QuickPost: Current attachments:', window.attachments);
            
            var form = $('#quickPostForm')[0];
            var formData = new FormData(form);
            
            // Add attachments in the correct Laravel array format
            if (window.attachments && window.attachments.length > 0) {
                // Don't send as JSON string - send as individual array elements
                window.attachments.forEach(function(attachment, index) {
                    formData.append(`attachments[${index}][id]`, attachment.id);
                    formData.append(`attachments[${index}][attachmentID]`, attachment.attachmentID);
                    if (attachment.type) {
                        formData.append(`attachments[${index}][type]`, attachment.type);
                    }
                });
                console.log('QuickPost: Added attachments as form array fields');
            }
            
            // Debug: show what we're sending
            console.log('QuickPost: Form data being sent:');
            for (let [key, value] of formData.entries()) {
                console.log(`${key}:`, value);
            }
            
            // Show loading state
            var submitBtn = $('.post-create-button');
            submitBtn.prop('disabled', true).text('Posting...');
            
            // Submit using SAME endpoint as normal post
            $.ajax({
                url: '/posts/save',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log('QuickPost: Post creation response:', response);
                    
                    if (response.success) {
                        // Success - just reload the page to show new post
                        console.log('QuickPost: Post created successfully, reloading page...');
                        location.reload();
                    } else {
                        console.error('QuickPost: Post creation failed:', response);
                        alert('Failed to create post: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function(xhr) {
                    console.error('QuickPost: Post submission error:', xhr);
                    
                    var errorMsg = 'Failed to create post';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMsg += ': ' + Object.values(xhr.responseJSON.errors).flat().join(', ');
                    }
                    
                    alert(errorMsg);
                },
                complete: function() {
                    submitBtn.prop('disabled', false).text('Save');
                }
            });
        },
        
        expand: function() {
            $('.composer-collapsed').hide();
            $('.composer-expanded').removeClass('d-none').show();
            $('#quickPostText').focus();
            console.log('QuickPost: Composer expanded');
        },
        
        collapse: function() {
            $('.composer-expanded').hide().addClass('d-none');
            $('.composer-collapsed').show();
            console.log('QuickPost: Composer collapsed');
        },
        
        clearForm: function() {
            // Clear form fields
            $('#quickPostForm')[0].reset();
            
            // Clear file previews
            $('.uploaded-files-preview').empty();
            
            // Clear attachments
            window.attachments = [];
            
            console.log('QuickPost: Form cleared');
        }
    };
    
    // Initialize the system
    window.QuickPostSimple.init();
    
    console.log('QuickPost: System initialized and ready');
}