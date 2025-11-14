// =====================================================
// Navigation helpers
// =====================================================
function navigateTo(url) {
    window.location.href = url;
}

function goBack() {
    history.back();
}

// =====================================================
// Form validation (default: always true)
// You can expand these later with your own rules.
// =====================================================
function validateLogin() {
    return true;
}

function validateRegister() {
    return true;
}

function validateItemAdd() {
    return true;
}

function validateItemModify() {
    return true;
}

function validateUserModify() {
    return true;
}

// =====================================================
// Attach event listeners after DOM is ready
// =====================================================
document.addEventListener('DOMContentLoaded', function() {
    attachFormValidation();
    attachButtonHandlers();
});

// =====================================================
// Attach validation to forms if they exist
// =====================================================
function attachFormValidation() {
    const formsWithValidation = {
        'login_form': validateLogin,
        'register_form': validateRegister,
        'item_add_form': validateItemAdd,
        'item_modify_form': validateItemModify,
        'user_modify_form': validateUserModify
    };

    for (const [formId, validationFunc] of Object.entries(formsWithValidation)) {
        const form = document.getElementById(formId);
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!validationFunc()) {
                    e.preventDefault();
                    return false;
                }
            });
        }
    }
}

// =====================================================
// Attach click handlers to navigation buttons
// =====================================================
function attachButtonHandlers() {
    // Navigation buttons: <button data-navigate="page.php">
    document.querySelectorAll('[data-navigate]').forEach(button => {
        button.addEventListener('click', function() {
            const url = this.getAttribute('data-navigate');
            navigateTo(url);
        });
    });

    // Back buttons: <button data-back>
    document.querySelectorAll('[data-back]').forEach(button => {
        button.addEventListener('click', goBack);
    });
}

