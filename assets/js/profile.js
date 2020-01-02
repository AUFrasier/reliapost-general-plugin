var $ = jQuery;

function addCategory() {
    $("#emptyView").html("");
    var value = $("#categorySelector").val();
    console.log("adding " + value);
    var name = categories[value];
    appendCategory(value, name);

    console.log("adding " + value + " to backend");
    postCategoryToBackend(value);
}

function removeCategory(slug) {
    var data = {
        action:'reliapost_removeCategoryFromUser',
        slug:slug
    };

    postDataToBackend(data, function(data) {
        console.log("results:");
        console.log(data);
        var categories = data["categories"];
        //updateList(categories);
    });


}

function appendCategory(value, name) {
    var addedCategories = $("#addedCategories");
    addedCategories.append("<div class='row'>");
    addedCategories.append("<div class='col-md-3'>" + name + "</div>");
    addedCategories.append("<div class='col-md-9'><a href='#' onclick='removeCategory(\"" + value + "\"); return false;'>Remove</a></div>");
    addedCategories.append("</div>");
}

function updateList(slugs) {
    $("#addedCategories").html("");
    slugs.forEach(function(slug) {
        var name = categories[slug];
        appendCategory(slug, name);
    });
}

function addCategoryToUser(slug) {
    var data = {
        action:'reliapost_addCategoryToUser',
        slug:slug
    };

    postDataToBackend(data, function(data) {});
}

function postCategoryToBackend(value) {
    var data = {
        action:'reliapost_addCategoryToUser',
        slug:value
    };

    postDataToBackend(data, function(data) {
        var categories = data["categories"];
        //updateList(categories);
    });
}

function editField(field) {
    $("#" + field + "-readonly").hide();
    $("#" + field + "-edit").show();
    var value = $("#user_" + field).html();
    sessionStorage.setItem('fieldValue', value);
}

function onPasswordSaved() {
    var newPassword = $("#password-field").val();
    if (validatePassword(newPassword)!=VALID || validatePasswordConfirm()!=VALID) {
        return;
    }

    var field = "password";
    $("#" + field + "-edit").hide();
    $("#" + field + "-readonly").show();

    updateProfile(field);
}

function saveField(field) {
    if (field=="password") {
        onPasswordSaved();
        return;
    }

    $("#" + field + "-edit").hide();
    $("#" + field + "-readonly").show();
    $("#user_" + field).text($("#" + field + "-field").val());

    let data = sessionStorage.getItem('fieldValue');
    var fieldinput = $("#" + field + "-field").val();
    var fieldId = $("#" + field + "-field").attr('id');
    if (data !== fieldinput) {
        Swal.fire({
                title: 'Confirm Update',
                text: "Upon confirmation, you will be redirected to the login page and your update will be processed.",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirm',
                cancelButtonText: 'Dismiss'
            }).then((result) => {
                if (result.value) {
                    //handle account update
                    updateProfile(field);
                }
            })
        } else {
            if (fieldId == "name-field") {
                $("#error-note-name").show();
            }
            if (fieldId == "email-field") {
                $("#error-note-email").show();
            }
        }
    
}

function cancelField(field) {
    $("#" + field + "-edit").hide();
    $("#" + field + "-readonly").show();
}

function loginRedirect() {
    location.href="/login";
}

function scrollToNote() {
    var elmnt = document.getElementById("name-redirect");
    elmnt.scrollTop;    
}

function updateProfile(field) {
    
    var data = {
        action:'reliapost_updateProfile',
        fieldName:field,
        value:$("#" + field + "-field").val()
    };

    postDataToBackend(data, function(response) {
        console.log("postDataToBackend()");
        console.log(response);
        var status = response["status"];
        if (status=="success" || status=="success0") {
            Swal.fire({
                title:'Success',
                text:'Thank you for updating your information!  Your update has been processes. You will be redirected back to the login page.'
            }).then((result) => {
                scrollToNote();
                $("#error-note-" + field).hide();
                $("#" + field + "-redirect").show();
                setTimeout(loginRedirect, 5000);
                
            })
        } else {
            Swal.fire({
                title:'Error cancelling account',
                text:'Please contact support'
            });
        }
    });
  
}

function watchPasswordFields() {
    $("#password-field").bind("change paste keyup", function() {
        var currentValue = $("#password-field").val();
        var valid = validatePassword(currentValue);
        console.log("password entered: " + currentValue + " : valid = " + valid);
        switch (valid) {
            case VALID:
                updatePasswordError(" ");
                break;
            case TOO_SHORT:
                updatePasswordError(`Password must be at least ${MINIMUM_CHARACTERS} characters`);
                break;
        }
        validatePasswordConfirm();
    });
    $("#password-confirm-field").bind("change paste keyup", function() {
        validatePasswordConfirm();
    });
}

var PASSWORDS_DONT_MATCH = 100;
function validatePasswordConfirm() {
    var newPassword = $("#password-field").val();
    var confirmPassword = $("#password-confirm-field").val();
    var errorMsg = " ";
    var code = VALID;
    if (newPassword !== confirmPassword) {
        errorMsg = "Passwords do not match";
        code = PASSWORDS_DONT_MATCH;
    }
    $("#password-error-2").text(errorMsg);
    return code;
}

function updatePasswordError(msg) {
    $("#password-error-1").text(msg);
}

var VALID = 0;
var TOO_SHORT = 1;
var MINIMUM_CHARACTERS = 4;
function validatePassword(password) {
    if (password.length<MINIMUM_CHARACTERS) return TOO_SHORT;
    else return VALID;
}
 
function addSaveButtons() {
    $(".save-buttons").each(function(div) {
        var field = $(this).attr("field");
        var html = `<button id="savebutton" onclick="saveField('${field}'); return false;">SAVE</button>
            <button id="cancelbutton" onclick="cancelField('${field}'); return false;">CANCEL</button>`;
        $(this).html(html);
    });
}

function markCategories(slugs) {
    $("#addedCategories").html("");
    slugs.forEach(function(slug) {
        $("#" + slug).prop("checked", true);
        var name = categories[slug];
        //appendCategory(slug, name);
    });
}


$(document).ready(function() {
    watchPasswordFields();
    addSaveButtons();
    markCategories(userCategories);
    $(".categoryCheckbox").change(function() {
        var id = $(this).attr("name");
        console.log(id + " was changed to " + this.checked);
        if (this.checked) addCategoryToUser(id);
        else removeCategory(id);

    });
});