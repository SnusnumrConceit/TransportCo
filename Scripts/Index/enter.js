$(document).ready(function () {  
    var btnEnter = $('#btn-enter'),
        loginForm = $('#login'),
        passForm = $('#pass');

    loginForm.blur(function () {  
        var login = loginForm.val();
        ValidateLogin(login);
    })

    passForm.blur(function () {  
        var pass = passForm.val();
        ValidatePass(pass);
    })
    
    btnEnter.click( function () {
        var login = loginForm.val(),
            pass = passForm.val();
        if (ValidateLogin(login) && ValidatePass(pass)) {
            var user = new User(login, pass);
            user = JSON.stringify(user);
            $.post('index.php', {user: user}, function (response, xhr) {
                if (xhr === 'success') {
                    window.location.href = 'Admin/admin.php';
                }
                if (response.length != 0) {
                    loginForm.removeClass('is-valid');
                    passForm.removeClass('is-valid');
                    loginForm.addClass('is-invalid');
                    passForm.addClass('is-invalid');
                    $('.valid-feedback').remove();
                    try {
                        errors = JSON.parse(response);
                        if (errors.login !== null && errors.login !== undefined && errors.login != 0) {
                            loginForm.after('<div class="invalid-feedback">'+errors.login+'</div>'); 
                        }
                        if (errors.pass !== null && errors.pass !== undefined && errors.pass != 0) {
                            passForm.after('<div class="invalid-feedback">'+errors.pass+'</div>');
                        }
                    } catch (error) {
                        passForm.after('<div class="invalid-feedback">'+response+'</div>');
                    }
                } else {
                    
                }
             })
            function User(login, pass) { 
                this.login = login,
                this.pass = pass;
             }
        }
    });

    function ValidateLogin(login) {
        loginForm.siblings('.invalid-feedback').remove();
        loginForm.siblings('.valid-feedback').remove();
        try {
            if (login !== null && login !== undefined && login.length != 0) {
                if (login.length >= 5 && login.length <= 24) {
                    if (/[a-zA-Z][a-zA-Z0-9]+/.exec(login) !== null) {
                        if (/[a-zA-Z][a-zA-Z0-9]+/.exec(login)[0] === login) {
                            if (loginForm.hasClass('is-invalid')) {
                                loginForm.removeClass('is-invalid');
                                loginForm.addClass('is-valid');    
                            } else {
                                loginForm.addClass('is-valid');
                            }
                            loginForm.parent().append('<div class="valid-feedback">Введённые данные соответствуют установленным требованиям!</div>');
                            return true;
                        } else {
                            throw new Error('Uncorrect Login Error');
                        }
                    } else {
                        throw new Error('Uncorrect Login Error');
                    }
                } else {
                    throw new Error('Length Login Error');
                }
            } else {
                throw new Error('Empty Login Error');
            }
        } catch (error) {
            if (error.message === 'Empty Login Error') {
                loginForm.addClass('is-invalid');
                loginForm.parent().append('<div class="invalid-feedback">Вы не ввели логин!</div>')
            }
            if (error.message === 'Length Login Error') {
                loginForm.addClass('is-invalid');
                loginForm.parent().append('<div class="invalid-feedback">Логин должен быть длиной от 5 до 24 символов!</div>')
            }
            if (error.message === 'Uncorrect Login Error') {
                loginForm.addClass('is-invalid');
                loginForm.parent().append('<div class="invalid-feedback">Логин не может начинаться с цифры и должен состоять из цифр и английских букв!</div>')
            }
        }
    }

    function ValidatePass(pass) {
        passForm.siblings('.invalid-feedback').remove();
        passForm.siblings('.valid-feedback').remove();
        try {
            if (pass !== null && pass !== undefined && pass.length != 0) {
                if (pass.length >= 5 && pass.length <= 24) {
                    if (/[a-zA-Z0-9]+/.exec(pass) !== null) {
                        if (/[a-zA-Z0-9]+/.exec(pass)[0] === pass) {
                            if (passForm.hasClass('is-invalid')) {
                                passForm.removeClass('is-invalid');
                                passForm.addClass('is-valid');    
                            } else {
                                passForm.addClass('is-valid');
                            }
                            passForm.parent().append('<div class="valid-feedback">Введённые данные соответствуют установленным требованиям!</div>');
                            return true;
                        } else {
                            throw new Error('Uncorrect Pass Error');
                        }
                    } else {
                        throw new Error('Uncorrect Pass Error');
                    }
                } else {
                    throw new Error('Length Pass Error');
                }
            } else {
                throw new Error('Empty Pass Error');
            }
        } catch (error) {
            if (error.message === 'Empty Pass Error') {
                passForm.addClass('is-invalid');
                passForm.parent().append('<div class="invalid-feedback">Вы не ввели пароль!</div>')
            }
            if (error.message === 'Length Pass Error') {
                passForm.addClass('is-invalid');
                passForm.parent().append('<div class="invalid-feedback">Пароль должен быть длиной от 5 до 24 символов!</div>')
            }
            if (error.message === 'Uncorrect Pass Error') {
                passForm.addClass('is-invalid');
                passForm.parent().append('<div class="invalid-feedback">Пароль должен состоять из цифр и английских букв!</div>')
            }
        }
    }
})