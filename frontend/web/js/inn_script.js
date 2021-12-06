$('#btn_add').click((event) => {
    event.preventDefault();

    const inn = $('#register-inn').val();

    // $.ajax({
    //     url: 'https://cors-anywhere.herokuapp.com',
    //     headers: {
    //         'Content-Type': 'application/x-www-form-urlencoded'
    //     },
    //     type: "GET",
    //     dataType: "json",
    //     data: {
    //         req: inn,
    //         key: "d4636295f5da93cd0807b594ed12e6fc43b23f54"
    //     },
    //     success: function (data) {
    //         console.log('asdasdasd', data);
    //     },
    //     error: function () {
    //         console.log("error");
    //     }
    // });
});