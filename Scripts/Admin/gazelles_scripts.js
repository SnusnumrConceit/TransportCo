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

    numberForm.inputmask('AA999964RUS');

    btnOpen.click(function () {
        creatorContainer.slideToggle();
    })

    btnDelete.click(function () {
        for (var index = 0; index < btnDelete.length; index++) {
            if (btnDelete[index] == event.target) {
                var position = index +1,
                    gazelle_id = $('tr:nth-child('+position+') .d-none').text();
                $.post('gazelles.php', {id: gazelle_id}, function (response) {
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
                gazelle_id = $('tr:nth-child('+position+') .d-none').text();
                window.location.href = 'Info/gazelleinfo.php?gazelle='+ gazelle_id;
          }
          
      }  
    })

    btnFind.click(function(){
        var number = findForm.val();
            try {
                if (number !== undefined && number !== null && number.length !=0) {
                    window.location.href = 'gazelles.php?number='+number;
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
                var gazelle = new Gazelle(number, route, statement);
                    formData.append('photo', photo);
                    gazelle = JSON.stringify(gazelle);
                    formData.append('gazelle', gazelle);
                
                    $.ajax({
                        type: 'POST',
                        data: formData,
                        url: 'gazelles.php',
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
                    
                

                function Gazelle (number, route, statement) {
                    this.number = number,
                    this.route = route,
                    this.statement = statement
                }
            }
    })

    function ValidateNumber(number) {
        try {
            if (number !== null && number !== undefined && number.length != 0) {
                if (number.length == 11) {
                    if (/([а-я]{2}[0-9]{4}[6][4][R][U][S])/i.exec(number) !== null) {
                        console.log(/([а-я]{2}[0-9]{4}[6][4][R][U][S])/i.exec(number)[0]);
                        if (/([а-я]{2}[0-9]{4}[6][4][R][U][S])/i.exec(number)[0] === number) {
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
                alert('Вы не ввели номер маршрутного такси!');
            }
            
            if (error.message === 'Length Number Error') {
                alert('Номер маршрутного такси долен быть длиной 11 символов!');
            }
            
            if (error.message === 'Uncorrect Number Error') {
                alert('Неверный формат номера маршрутного такси!');
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