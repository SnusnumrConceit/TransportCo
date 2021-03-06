$(document).ready(function () {
    var btnDelete = $('.btn-danger'),
        btnEdit = $('.btn-warning'),
        btnFind = $('#btn-find'),
        btnOpen = $('#btn-open-container'),
        btnCreator = $('#btn-send'),
        findForm = $('#find-input'),
        creatorContainer = $('.creator-container'),
        numberForm = $('#number'),
        photoForm = $('#photo'),
        routeForm = $('#route'),
        statementForm = $('#statement');
    
    creatorContainer.css('display', 'none');

    btnOpen.click(function () {
        creatorContainer.slideToggle();
    })

    btnDelete.click(function () {
        for (var index = 0; index < btnDelete.length; index++) {
            if (btnDelete[index] == event.target) {
                var position = index +1,
                    trolley_id = $('tr:nth-child('+position+') .d-none').text();
                $.post('trolleies.php', {id: trolley_id}, function (response) {
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
                trolley_id = $('tr:nth-child('+position+') .d-none').text();
                window.location.href = 'Info/trolleyinfo.php?trolley='+ trolley_id;
          }
          
      }  
    })

    btnFind.click(function(){
        var number = findForm.val();
            try {
                if (number !== undefined && number !== null && number.length !=0) {
                    window.location.href = 'trolleies.php?number='+number;
                } else {
                    throw new Error('Empty Find Error');
                }
            } catch (error) {
                if (error.message == 'Empty Find Error') {
                    findForm.addClass('is-invalid');
                    findForm.prop('placeholder', 'Вы не ввели логин');
                }
                else {
                    alert(error.message);
                }
            }
    })

    btnCreator.click(function () {  
        var number = numberForm.val(),
            photo = photoForm.prop('files')[0],
            route = routeForm.val(),
            statement = statementForm.val(),
            formData = new FormData();

            if (ValidateNumber(number) && ValidatePhoto(photo) && ValidateRoute(route)) {
                var trolley = new Trolley(number, route, statement);
                    formData.append('photo', photo);
                    trolley = JSON.stringify(trolley);
                    formData.append('trolley', trolley);
                
                    $.ajax({
                        type: 'POST',
                        data: formData,
                        url: 'trolleies.php',
                        contentType: false,
                        processData: false,
                        success: function (response) {  
                            if (response.length != 0) {
                                alert(response);
                            } else {
                                window.location.reload();
                            }
                        }
                    })
                    
                

                function Trolley (number, route, statement) {
                    this.number = number,
                    this.route = route,
                    this.statement = statement
                }
            }
    })

    function ValidateNumber(number) {
        try {
            if (number !== null && number !== undefined && number.length != 0) {
                if (number.length == 4) {
                    if (/([0-9]{4})/i.exec(number) !== null) {
                        if (/([0-9]{4})/i.exec(number)[0] === number) {
                            return true;
                        } else {
                            throw new Error('Uncorrect Number Error');    
                        }
                    } else {
                        throw new Error('Uncorrect Number Error');
                    }
                        
                } else {
                    throw new Error('Length Number Error');
                }
                
            } else {
                throw new Error('Empty Number Error');
            }
        } catch (error) {
            if (error.message === 'Empty Number Error') {
                alert('Вы не ввели номер троллейбуса!');
            }
            
            if (error.message === 'Length Number Error') {
                alert('Номер троллейбуса долен быть длиной 4 символа!');
            }
            
            if (error.message === 'Uncorrect Number Error') {
                alert('Номер троллейбуса должен состоять из цифр!');
            }
        }
    }

    function ValidateRoute(route) {
        try {
            if (route !== null && route !== undefined && route.length != 0) {
                if (!isNaN(route)) {
                    if (route > 0 && route <= 300) {
                        return true;
                    } else {
                        throw new Error('Length Route Error');
                    }
                } else {
                    throw new Error('Uncorrect Route Error');    
                }
            } else {
                throw new Error('Empty Route Error');
            }
        } catch (error) {
            if (error.message === 'Empty Route Error') {
                alert('Вы не ввели номер маршрута!');
            }
            
            if (error.message === 'Length Route Error') {
                alert('Номер маршрута не должен превышать 300!');
            }
            
            if (error.message === 'Uncorrect Route Error') {
                alert('Номер маршрута должен состоять из цифр!');
            }
        }
    }

    function ValidatePhoto(photo) {
        try {
            if (photo !== undefined && photo !== null && photo.length != 0) {
                return true;
            } else {
                throw new Error('Download Photo Error');
            }
        } catch (error) {
            if (error.message === 'Download Photo Error') {
                alert('Вы не загрузили фотографию!');
            }
        }
    }
})