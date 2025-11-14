// Common navigation functions
function navigateTo(url) {
    window.location.href = url;
}

function goBack() {
    history.back();
}

// Form submission handlers - attach to forms by ID
document.addEventListener('DOMContentLoaded', function() {
    // Generic form validation attachment
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

    // Attach click handlers to buttons
    attachButtonHandlers();
});

function attachButtonHandlers() {
    // Navigation buttons
    document.querySelectorAll('[data-navigate]').forEach(button => {
        button.addEventListener('click', function() {
            navigateTo(this.getAttribute('data-navigate'));
        });
    });

    // Back buttons
    document.querySelectorAll('[data-back]').forEach(button => {
        button.addEventListener('click', goBack);
    });
}
