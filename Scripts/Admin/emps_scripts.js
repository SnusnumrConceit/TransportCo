$(document).ready(function () {  
    var btnDelete = $('.btn-danger'),
        btnEdit = $('.btn-warning'),
        btnFind = $('#btn-find'),
        btnOpen = $('#btn-open-container'),
        btnCreator = $('#btn-send'),
        findForm = $('#find-input'),
        creatorContainer = $('.creator-container'),
        lastNameForm = $('#last-name'),
        firstNameForm = $('#first-name'),
        middleNameForm = $('#middle-name'),
        birthdayForm = $('#birthday'),
        phoneNumberForm = $('#phone-number');


        creatorContainer.css('display', 'none');

    btnOpen.click(function () {
        creatorContainer.slideToggle();
    })

    $('#phone-number').inputmask('(999)-999-99-99');
    $('#birthday').inputmask('99.99.9999');
    btnDelete.click(function () {
        for (var index = 0; index < btnDelete.length; index++) {
            if (btnDelete[index] == event.target) {
                var position = index +1,
                    emp_id = $('tr:nth-child('+position+') .d-none').text();
                $.post('emps.php', {id: emp_id}, function (response) {
                    if (response.length != 0) {
                        alert(response);
                    } else {
                        window.location.reload();
                    }
                })

            }
            
        }
    })

    btnEdit.click(function () {
      for (var index = 0; index < btnEdit.length; index++) {
          if(btnEdit[index] == event.target) {
            var position = index +1,
                emp_id = $('tr:nth-child('+position+') .d-none').text();
                window.location.href = 'Info/empinfo.php?emp='+ emp_id;
          }
          
      }  
    })

    btnFind.click(function(){
        var lastName = findForm.val();
            try {
                if (lastName !== undefined && lastName !== null && lastName.length !=0) {
                    window.location.href = 'emps.php?lastName='+lastName;
                } else {
                    throw new Error('Empty Find Error');
                }
            } catch (error) {
                if (error.message == 'Empty Find Error') {
                    findForm.addClass('is-invalid');
                    findForm.prop('placeholder', 'Вы не ввели описание');
                }
                else {
                    alert(error.message);
                }
            }
    })

    btnCreator.click(function () {  
        var lastName = lastNameForm.val(),
            firstName = firstNameForm.val(),
            middleName = middleNameForm.val(),
            birthday = birthdayForm.val(),
            phone = phoneNumberForm.val();

        if (ValidateLName(lastName) && ValidateFName(firstName) && ValidateMName(middleName)
            && ValidateBday(birthday) && ValidatePhone(phone)) {
            var emp = new Emp(lastName, firstName, middleName, birthday, phone);
                emp = JSON.stringify(emp);
                $.post('emps.php', {emp: emp}, function (response) {  
                    if (response.length != 0) {
                        alert(response);
                    } else {
                        window.location.reload();
                    }
                })

            function Emp(lastName, firstName, middleName, birthday, phone) {
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
                    alert('Фамилия должна состоять из латинских букв или кириллистических букв!');
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
                    alert('Имя должно состоять из латинских букв или кириллистических букв!');
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
                    alert('Отчество должно состоять из латинских букв или кириллистических букв!');
                }
            }
        }

        function ValidateBday(birthday) {
            try {
                if (birthday !== null && birthday !== undefined && birthday.length != 0) {
                    if (birthday.length == 10) {
                        var birthdayReg = /[0-9]{2}[\.][0-9]{2}[\.][0-9]{4}/;
                        if (birthdayReg.exec(birthday) !== null) {
                            if (birthdayReg.exec(birthday)[0] === birthday) {
                                console.log(birthdayReg.exec(birthday));
                                return true;
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
})