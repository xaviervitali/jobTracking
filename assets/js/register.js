document.addEventListener("DOMContentLoaded", function () {
    const passwordInput =  document.querySelector('input[name*="[password][first]"]');
    const passwordStrengthMeter = document.getElementById('password-strength-meter');
    const passwordStrengthText = document.getElementById('password-strength-text');

    passwordInput.addEventListener('input', updatePasswordStrength);

    function updatePasswordStrength() {
        const value = passwordInput.value;
        const strength = getPasswordStrength(value);
        passwordStrengthMeter.style.width = strength.percent + '%';
        passwordStrengthMeter.style.backgroundColor = strength.color;
        passwordStrengthText.textContent = strength.message;
    }

    function getPasswordStrength(password) {
        let score = 0;
        let message = '';
        let color = 'red';

        if (password.length >= 8) score += 1;
        if (password.match(/[a-z]/)) score += 1;
        if (password.match(/[A-Z]/)) score += 1;
        if (password.match(/[0-9]/)) score += 1;
        if (password.match(/[\W]/)) score += 1;

        switch (score) {
            case 5:
                message = 'Très fort';
                color = 'green';
                break;
            case 4:
                message = 'Fort';
                color = 'limegreen';
                break;
            case 3:
                message = 'Moyen';
                color = 'orange';
                break;
            case 2:
                message = 'Faible';
                color = 'orange';
                break;
            default:
                message = 'Très faible';
                color = 'red';
                break;
        }

        return { percent: (score / 5) * 100, message, color };
    }
});

document.querySelector('form').addEventListener('submit', function(event) {
    const passwordInput = document.querySelector('input[name="form[password][first]"]');
    const confirmPasswordInput = document.querySelector('input[name="form[password][second]"]');
    
    const password = passwordInput.value;
    const confirmPassword = confirmPasswordInput.value;
    let errors = [];

    // Validate password length
    if (password.length < 8) {
        errors.push('Le mot de passe doit contenir au moins 8 caractères.');
    }

    // Validate that passwords match
    if (password !== confirmPassword) {
        errors.push('Les mots de passe ne correspondent pas.');
    }

    // Check for lowercase, uppercase, number, special character
    if (!/[a-z]/.test(password)) {
        errors.push('Le mot de passe doit contenir au moins une lettre minuscule.');
    }
    if (!/[A-Z]/.test(password)) {
        errors.push('Le mot de passe doit contenir au moins une lettre majuscule.');
    }
    if (!/[0-9]/.test(password)) {
        errors.push('Le mot de passe doit contenir au moins un chiffre.');
    }
    if (!/[\W]/.test(password)) {
        errors.push('Le mot de passe doit contenir au moins un caractère spécial.');
    }

    if (errors.length > 0) {
        event.preventDefault(); // Stop form submission
        alert(errors.join("\n")); // Display errors as an alert (you can enhance this)
    }
});

