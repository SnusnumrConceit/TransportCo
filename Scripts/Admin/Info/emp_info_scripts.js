$(document).ready( function () {
    var lastNameForm = $('#last-name'),
        firstNameForm = $('#first-name'),
        middleNameForm = $('#middle-name'),
        phoneNumberForm = $('#phone-number'),
        birthdayForm = $('#birthday'),
        btnSender = $('#btn-send'),
        btnOpenTaxes = $('#open-tax-creator-container'),
        btnOpenRoutes = $('#open-route-creator-container'),
        btnTax = $('#btn-tax'),
        btnRoute = $('#btn-route'),
        btnDeleteTax = $('.btn-tax-delete'),
        radioBtns = $('input[type="radio"]'),
        btnDeleteRoute = $('#btn-remove-route');
    
    
    btnOpenTaxes.click(function () {  
        $('.tax-creator-container').slideToggle();
    })

    btnOpenRoutes.click(function () {  
        $('.routes-creator-container').slideToggle();
    })
    
    btnSender.click(function () {  
        var id = window.location.search.split('=')[1],
            lastName = lastNameForm.val(),
            firstName = firstNameForm.val(),
            middleName = middleNameForm.val(),
            birthday = birthdayForm.val(),
            phone = phoneNumberForm.val();

        if (ValidateLName(lastName) && ValidateFName(firstName) && ValidateMName(middleName)
            && ValidateBday(birthday) && ValidatePhone(phone)) {
            var emp = new Emp(id, lastName, firstName, middleName, birthday, phone);
                emp = JSON.stringify(emp);
                $.post('empinfo.php', {emp: emp}, function (response) {  
                    if (response.length != 0) {
                        alert(response);
                    } else {
                        window.location.href = '../emps.php';
                    }
                })

            function Emp(id, lastName, firstName, middleName, birthday, phone) {
                this.id = id,
                this.lastName = lastName,
                this.firstName = firstName,
                this.middleName = middleName,
                this.birthday = birthday,
                this.phone = phone;
            }
        }

        function ValidatePhone(phoneNumber) {  
            try {
                if (phoneNumber !== undefined && phoneNumber !== null && phoneNumber.length !== 0) {
                    if (phoneNumber.length == 15) {
                        if (/[(][9][0-9]{2}[)][-][0-9]{3}[-][0-9]{2}[-][0-9]{2}/.exec(phoneNumber) !== null) {
                            if (/[(][9][0-9]{2}[)][-][0-9]{3}[-][0-9]{2}[-][0-9]{2}/.exec(phoneNumber)[0] === phoneNumber) {
                                return true;
                            } else {
                                throw new Error('Wrong Phone Data');
                            }
                        } else {
                            throw new Error('Wrong Phone Data');
                        }
                    } else {
                        throw new Error('Length Phone Data');
                    }
                } else {
                    throw new Error('Empty Phone Data');
                }
            } catch (error) {
                phoneNumberForm.siblings().remove('.invalid-feedback');
                if (error.message === 'Empty Phone Data') {
                    alert('Вы не ввели номер телефона!');
                }
                if (error.message === 'Length Phone Data') {
                    alert('Наш сервис работает только с операторами РФ!');
                }
                if (error.message === 'Wrong Phone Data') {
                    alert('Наш сервис работает только с операторами РФ!');
                }
            }
        }

        function ValidateLName(lastName) {  
            try {
                if (lastName !== undefined && lastName !== null && lastName.length !== 0) {
                    if (lastName.length >= 3 && lastName.length <= 30) {
                        if (/([А-Я][a-я]{2,})/.exec(lastName) !== null) {
                            if (/([А-Я][a-я]{2,})/.exec(lastName)[0] === lastName) {
                                return true;
                            } else {
                                throw new Error('Wrong LName Data');
                            }
                        } else {
                            throw new Error('Wrong LName Data');
                        }
                    } else {
                        throw new Error('Length LName Data');
                    }
                } else {
                    throw new Error('Empty LName Data');
                }
            } catch (error) {
                lastNameForm.siblings().remove('.invalid-feedback');
                if (error.message === 'Empty LName Data') {
                    alert('Вы не ввели фамилию!');
                }
                if (error.message === 'Length LName Data') {
                    alert('Длина фамилии должна быть от 3 до 30 символов!');
                }
                if (error.message === 'Wrong LName Data') {
                    alert('Фамилия должна состоять из русских букв и начинаться с заглавной буквы!');
                }
            }
        }

        function ValidateFName(firstName) {  
            try {
                if (firstName !== undefined && firstName !== null && firstName.length !== 0) {
                    if (firstName.length >= 4 && firstName.length <= 15) {
                        if (/([А-Я][a-я]{3,})/.exec(firstName) !== null) {
                            if (/([А-Я][a-я]{3,})/.exec(firstName)[0] === firstName) {
                                return true;
                            } else {
                                throw new Error('Wrong FName Data');
                            }
                        } else {
                            throw new Error('Wrong FName Data');
                        }
                    } else {
                        throw new Error('Length FName Data');
                    }
                } else {
                    throw new Error('Empty FName Data');
                }
            } catch (error) {
                firstNameForm.siblings().remove('.invalid-feedback');
                if (error.message === 'Empty FName Data') {
                    alert('Вы не ввели имя!');
                }
                if (error.message === 'Length FName Data') {
                    alert('Длина имени должна быть от 4 до 15 символов!');
                }
                if (error.message === 'Wrong FName Data') {
                    alert('Имя должно состоять из русских букв и начинаться с заглавной буквы!');
                }
            }
        }

        function ValidateMName(middleName) {  
            try {
                if (middleName !== undefined && middleName !== null && middleName.length !== 0) {
                    if (middleName.length >= 6 && middleName.length <= 24) {
                        if (/([А-Я][a-я]{5,})/.exec(middleName) !== null) {
                            if (/([А-Я][a-я]{5,})/.exec(middleName)[0] === middleName) {
                                return true;
                            } else {
                                throw new Error('Wrong MName Data');
                            }
                        } else {
                            throw new Error('Wrong MName Data');
                        }
                    } else {
                        throw new Error('Length MName Data');
                    }
                } else {
                    throw new Error('Empty MName Data');
                }
            } catch (error) {
                middleNameForm.siblings().remove('.invalid-feedback');
                if (error.message === 'Empty MName Data') {
                    alert('Вы не ввели отчество!');
                }
                if (error.message === 'Length MName Data') {
                    alert('Длина отчества должна быть от 6 до 24 символов!');
                }
                if (error.message === 'Wrong MName Data') {
                    alert('Отчество должно состоять из русских букв и начинаться с заглавной буквы!');
                }
            }
        }

        function ValidateBday(birthday) {
            try {
                if (birthday !== null && birthday !== undefined && birthday.length != 0) {
                    if (birthday.length == 10) {
                        var birthdayRegResult = /(([0-9]{2})[\.]([0-9]{2})[\.]([0-9]{4}))/.exec(birthday);
                        if (birthdayRegResult !== null) {
                            if (birthdayRegResult[0] === birthday) {
                                var day = parseInt(birthdayRegResult[2]),
                                    month = parseInt(birthdayRegResult[3]),
                                    year = parseInt(birthdayRegResult[4]);
                                //проверка на день
                                if ((day >= 1) && (day <= 31)) {
                                    if ((month >= 1) && (month <= 12)) {
                                        if (month == 2) {
                                            if (day <= 28) {
                                                        
                                            } else if (day == 29 && year % 4 == 0) {
                                                
                                            } else {
                                                throw new Error('Uncorrect Birthday Error');
                                            }
                                        }
                                        if ((year >= 1950) && (year <= 2000)) {
                                            return true;
                                        } else {
                                            throw new Error('Uncorrect Birthday Error');
                                        }
                                    } else {
                                        throw new Error('Uncorrect Birthday Error');
                                    }
                                } else {
                                    throw new Error('Uncorrect Birthday Error');    
                                }
                            } else {
                                throw new Error('Uncorrect Birthday Error');
                            }
                        } else {
                            throw new Error('Uncorrect Birthday Error');
                        }
                    } else {
                        throw new Error('Length Birthday Error');
                    }
                } else {
                    throw new Error('Empty Birthday Error');
                }
            } catch (error) {
                if (error.message === 'Empty Birthday Error') {
                    alert('Вы не ввели дату!');
                }
                if (error.message === 'Length Birthday Error') {
                    alert('Неверное количество символов!');
                }
                if (error.message === 'Uncorrect Birthday Error') {
                    alert('Неверный формат даты!');
                }
            }
            
        }
    })

    btnTax.click(function () {  
        var arrTaxes = [],
            emp_id = window.location.search.split('=')[1];
        $(':checked').each(function () {  
            arrTaxes.push(this.value);
        })
        var taxes = new Taxes(arrTaxes, emp_id);
        taxes = JSON.stringify(taxes);
        $.post(
            'empinfo.php',
            {taxes: taxes},
            function (response) {  
                if (response.length != 0) {
                    alert(response);
                } else {
                    window.location.reload();
                }
            }
        )
        function Taxes(arrTaxes, emp_id) {
            this.taxes = arrTaxes,
            this.emp_id = emp_id
        }
    })

    btnDeleteTax.click(function () {  
        for (var index = 0; index < btnDeleteTax.length; index++) {
            if (btnDeleteTax[index] == event.target) {
                var position = index + 1;
                    tax_id = $('li:nth-child('+position+') .d-none').text();
                $.post(
                    'empinfo.php', 
                    {tax: tax_id}, 
                    function (response) {  
                        if (response.length != 0) {
                            alert(response);
                        } else {
                            window.location.reload();
                        }
                })
            }
            
        }
    })


    radioBtns.click(function () {  
        /*if (this.checked) {
            this.checked = false;
        } else {*/
            $('input[type="radio"]').prop('disabled', true);
            $('input[type="radio"]:checked').prop('disabled', false);    
        //}
    })


    /*$('section').click(function () {  
        $('input[type="radio"]').prop('disabled', false); 
    })*/

    btnRoute.click(function () {  
        var transport = $('input[type="radio"]:checked').val(),
            emp = window.location.search.split('=')[1],
            route = new Route(emp, transport);
            route = JSON.stringify(route);
            $.post(
                '../routes.php',
                {route: route},
                function (response) {  
                    if (response.length != 0) {
                        alert(response);
                    } else {
                        window.location.reload();
                    }
                }
            )

            function Route(emp, transport) { 
                this.transport = transport,
                this.emp = emp;
             }

    })

    btnDeleteRoute.click(function () {  
        var emp = window.location.search.split('=')[1];
        $.post('../routes.php', {emp: emp}, function (response) {  
            if (response.legnth > 2) {
                alert(response);
            } else {
                window.location.reload();
            }
        })
    })
})