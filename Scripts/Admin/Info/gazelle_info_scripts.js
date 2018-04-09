$(document).ready(function (){
    btnSender = $('#btn-send'),
    numberForm = $('#number'),
    routeForm = $('#route'),
    statementForm = $('#statement'),
    photoForm = $('#photo');

    btnSender.click(function () {  
        var id = window.location.search.split('='),
            number = numberForm.val(),
            photo = photoForm.prop('files')[0],
            route = routeForm.val(),
            statement = statementForm.val(),
            formData = new FormData();

            if (ValidateNumber(number) && ValidatePhoto(photo) && ValidateRoute(route)) {
                var gazelle = new Gazelle(id, number, route, statement);
                    formData.append('photo', photo);
                    gazelle = JSON.stringify(gazelle);
                    formData.append('gazelle', gazelle);
                
                    $.ajax({
                        type: 'POST',
                        data: formData,
                        url: 'gazelleinfo.php',
                        contentType: false,
                        processData: false,
                        success: function (response) {  
                            if (response.length != 0) {
                                alert(response);
                            } else {
                                window.location.href = '../gazelles.php';
                            }
                        }
                    })
                    
                

                function Gazelle (id, number, route, statement) {
                    this.id =id[1],
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
                alert('Номер маршрутного такси долен быть длиной 4 символа!');
            }
            
            if (error.message === 'Uncorrect Number Error') {
                alert('Номер маршрутного такси должен состоять из цифр!');
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
        
            if (photo !== undefined && photo !== null && photo.length != 0) {
                return true;
            } else {
                photo = null;
                return true;
            }
        
    }
})