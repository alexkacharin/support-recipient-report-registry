
$('#btn_add').click((event) => {
    event.preventDefault();
     const inn = $('#register-inn').val();
     var adres,name;
     var CORS = 'https://cors-anywhere.herokuapp.com/';
     $.ajax({
        url: CORS +"https://api-fns.ru/api/egr",
        headers: {
             'Content-Type': 'application/x-www-form-urlencoded',
             'X-Requested-With': 'XMLHttpRequest'
         },
         type: "GET",
         dataType: "json",
         data: {
             req: inn,
             key: "d154409221db3ac44ddf1069a8d835cf5c2525c0"
         },
         success: function(data) {
             adres = (data.items[0].ЮЛ.Адрес.АдресПолн);
             name = (data.items[0].ЮЛ.НаимСокрЮЛ);
             $('#profile-location').val(adres);
             $('#profile-name').val(name);
             $('#btn_add').hide();
         },
         error: function () {
             console.log("error");
             $('#profile-location').val('Запроса');
             $('#profile-name').val('Ошибка');
             $('#btn_add').hide();
         }
     });

});