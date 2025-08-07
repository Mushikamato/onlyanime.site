/**
 * Post create (helper) component
 */
"use strict";
/* global app, Post, user, FileUpload, updateButtonState, launchToast, trans, redirect, getWebsiteFormattedAmount, passesMinMaxPPPostLimits */

$(function () {
    $("#post-price").keypress(function(e) {
        if(e.which === 13) {
            PostCreate.savePostPrice();
        }
    });
});

var PostCreate = {
    // Paid post price
    postPrice : 0,
    isSavingRedirect: false,
    postNotifications: false,
    postReleaseDate: null,
    postExpireDate: null,
    pollAnswers: [],

    /**
     * Toggles post notification state
     */
    togglePostNotifications: function(){
        let buttonIcon = '';
        if(PostCreate.postNotifications === true){
            PostCreate.postNotifications = false;
            buttonIcon = `<div class="d-flex justify-content-center align-items-center mr-1"><ion-icon class="icon-medium" name="notifications-off-outline"></ion-icon></div>`;
        }
        else{
            buttonIcon = `<div class="d-flex justify-content-center align-items-center mr-1"><ion-icon class="icon-medium" name="notifications-outline"></ion-icon></div>`;
            PostCreate.postNotifications = true;
        }
        $('.post-notification-icon').html(buttonIcon);
    },

    /**
     * Shows up the post price setter dialog
     */
    showSetPricePostDialog: function(){
        $('#post-set-price-dialog').modal('show');
    },

    /**
     * Saves the post price into the state
     */
    savePostPrice: function(){
        PostCreate.postPrice = $('#post-price').val();
        let hasError = false;
        if(!passesMinMaxPPPostLimits(PostCreate.postPrice)){
            hasError = 'min';
        }
        if(PostCreate.postExpireDate !== null){
            hasError = 'ppv';
        }
        if(hasError){
            $('.post-price-error').addClass('d-none');
            $('#post-set-price-dialog .'+hasError+'-error').removeClass('d-none');
            $('#post-price').addClass('is-invalid');
            return false;
        }
        $('.post-price-label').html('('+getWebsiteFormattedAmount(PostCreate.postPrice)+')');
        $('#post-set-price-dialog').modal('hide');
        $('#post-price').removeClass('is-invalid');
    },
    /**
     * Clears up post price
     */
    clearPostPrice: function(){
        PostCreate.postPrice = 0;
        $('#post-price').val(0);
        $('.post-price-label').html('');
        $('#post-set-price-dialog').modal('hide');
        $('#post-price').removeClass('is-invalid');
    },

    /**
     * Initiates the post draft data, if available
     * @param data
     * @param type
     */
    initPostDraft: function(data, type = 'draft'){
        Post.initialDraftData = Post.draftData;
        if(data){
            Post.draftData = data;
            if(type === 'draft'){
                FileUpload.attachments = data.attachments;
            }
            else{
                data.attachments.map(function (item) {
                    FileUpload.attachments.push({attachmentID: item.id, path: item.path, type:item.attachmentType, thumbnail:item.thumbnail});
                });
            }
            $('#dropzone-uploader').val(Post.draftData.text);
        }
    },

    /**
     * Clears up post draft data
     */
    clearDraft: function(){
        // Clearing attachments from the backend
        Post.draftData.attachments.map(function (value) {
            FileUpload.removeAttachment(value.attachmentID);
        });
        // Removing previews
        $('.dropzone-previews .dz-preview ').each(function (index, item) {
            $(item).remove();
        });
        // Clearing Fileupload class attachments
        FileUpload.attachments = [];
        // Clearing up the local storage object
        PostCreate.clearDraftData();
        // Clearing up the text area value
    },

    /**
     * Saves post draft data
     */
    saveDraftData: function(){
        Post.draftData.attachments = FileUpload.attachments;
        Post.draftData.text = $('#dropzone-uploader').val();
        localStorage.setItem('draftData', JSON.stringify(Post.draftData));
    },

    /**
     * Clears up draft data
     * @param callback
     */
    clearDraftData: function(callback = null){
        localStorage.removeItem('draftData');
        Post.draftData = Post.initialDraftData;
        if(callback !== null){
            callback;
        }
        $('#dropzone-uploader').val(Post.draftData.text);
    },


    /**
     * Populates create/edit post form with draft data
     * @returns {boolean|any}
     */
    populateDraftData: function(){
        const draftData = localStorage.getItem('draftData');
        if(draftData){
            return JSON.parse(draftData);
        }
        else{
            return false;
        }
    },

    /**
     * Save new / update post
     * @param type
     * @param postID
     */
    save: function (type = 'create', postID = false, forceSave = false) {
        if((FileUpload.isLoading === true || FileUpload.isTranscodingVideo === true) && forceSave === false){
            let dialogMessage = '';
            if(FileUpload.isLoading === true){
                dialogMessage = `${trans('Some attachments are still being uploaded.')} ${trans('Are you sure you want to continue?')}`;
            }
            if(FileUpload.isTranscodingVideo === true){
                dialogMessage = `${trans('A video is currently being converted.')} ${trans('Are you sure you want to continue without it?')}`;
            }
            $('#confirm-post-save .modal-body p').html(dialogMessage);
            $('.confirm-post-save').unbind('click');
            $('.confirm-post-save').on('click',function () {
                PostCreate.save(type, postID, true);
            });
            $('#confirm-post-save').modal('show');
            return false;
        }

        if(PostCreate.postPrice > 0 && FileUpload.attachments.length === 0){
            if(!app.isTextOnlyPPVAllowed){
                $('#no-attachments-locked-post').modal('show');
                return false;
            }
        }

        updateButtonState('loading',$('.post-create-button'));
        PostCreate.savePostScheduleSettings();
        let route = app.baseUrl + '/posts/save';
        let data = {
            'attachments': FileUpload.attachaments, // Corrected typo from 'attachaments'
            'text': $('#dropzone-uploader').val(),
            'price': PostCreate.postPrice,
            'postNotifications' : PostCreate.postNotifications,
            'postReleaseDate': PostCreate.postReleaseDate,
            'postExpireDate': PostCreate.postExpireDate,
            'pollAnswers' : PostCreate.pollAnswers,
            'is_adult_content': $('#is_adult_content').is(':checked') ? 1 : 0,
            'content_type': $('#content_type').val()
        };
        if(type === 'create'){
            data.type = 'create';
        }
        else{
            data.type = 'update';
            data.id = postID;
        }
        $.ajax({
            type: 'POST',
            data: data,
            url: route,
            success: function () {
                if(type === 'create'){
                    PostCreate.isSavingRedirect = true;
                    PostCreate.clearDraftData(redirect(app.baseUrl+'/'+user.username));
                }
                else{
                    redirect(app.baseUrl+'/posts/'+postID+'/'+user.username);
                }
                updateButtonState('loaded',$('.post-create-button'), trans('Save'));
                $('#confirm-post-save').modal('hide');
            },
            error: function (result) {
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').html('');
                if (result.status === 422 || result.status === 500) {
                    const errors = result.responseJSON.errors;
                    if (errors && typeof errors === 'object') {
                        for (const key in errors) {
                            if (Array.isArray(errors[key]) && errors[key].length > 0) {
                                const errorMessage = errors[key][0];
                                const targetInput = $(`#${key}`);
                                const targetFeedback = targetInput.next('.invalid-feedback');
                                if (targetInput.length) {
                                    targetInput.addClass('is-invalid');
                                    if (targetFeedback.length) {
                                        targetFeedback.html(errorMessage);
                                    }
                                } else {
                                    launchToast('danger', trans('Validation Error'), errorMessage);
                                }
                            }
                        }
                    }
                } else if (result.status === 403) {
                    launchToast('danger', trans('Error'), 'Post not found.');
                }
                $('#confirm-post-save').modal('hide');
                updateButtonState('loaded', $('.post-create-button'), trans('Save'));
            }
        });
    },

    showPostScheduleDialog: function(){
        $('#post-set-schedule-dialog').modal('show');
    },

    savePostScheduleSettings: function(){
        if(PostCreate.postPrice !== 0 && $('#post_expire_date').val().length > 0){
            $('#post_expire_date').addClass('is-invalid');
            return false;
        }
        PostCreate.postReleaseDate = $('#post_release_date').val().length ? $('#post_release_date').val() : null;
        PostCreate.postExpireDate = $('#post_expire_date').val().length ? $('#post_expire_date').val() : null;
        $('#post-set-schedule-dialog').modal('hide');
        $('#post_expire_date').removeClass('is-invalid');
    },

    clearPostScheduleSettings: function(){
        PostCreate.postReleaseDate = null;
        PostCreate.postExpireDate = null;
        $('#post_release_date').val('');
        $('#post_expire_date').val('');
        $('#post_expire_date').removeClass('is-invalid');
    },

    showPollEditDialog: function(){
        $('#post-set-poll-dialog').modal('show');
    },

    appendNewPollQuestion: function () {
        $('.poll-questions-wrapper').append(pollQuestionInputElement());
    },

    deletePollAnswer: function (el){
        $(el).parent().parent().remove();
    },

    savePoll: function (){
        const questionsArray = [];
        let allValid = true;
        $('.poll-questions-wrapper input[name="questions"]').each(function() {
            const input = $(this);
            const value = input.val().trim();
            const questionId = input.attr('id') || null;
            if (!value) {
                input.addClass('is-invalid');
                allValid = false;
            } else {
                input.removeClass('is-invalid');
                questionsArray.push({
                    id: questionId,
                    value: value
                });
            }
        });
        if (allValid) {
            $('#post-set-poll-dialog').modal('hide');
            PostCreate.pollAnswers = questionsArray;
        }
    },

    clearPoll: function (){
        PostCreate.pollAnswers = [];
        $('.poll-questions-wrapper input[name="questions"]').val('');
        $('.poll-questions-wrapper .form-group:has(.h-pill)').remove();
        $('#post-set-poll-dialog').modal('hide');
    }
};

function pollQuestionInputElement() {
    return `<div class="form-group">
                <div class="d-flex align-items-center">
                    <input class="form-control" name="questions" placeholder="${trans("Enter a poll question")}">
                    <div class="ml-1 h-pill h-pill-primary rounded react-button w-32 d-flex align-items-center" data-toggle="tooltip" data-placement="top" title="${trans("Cancel")}" onclick="PostCreate.deletePollAnswer(this)">
                        <ion-icon name="close-outline"></ion-icon>
                   </div>
               </div>
            </div>`;
}