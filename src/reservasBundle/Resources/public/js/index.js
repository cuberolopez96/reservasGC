//(function(){
  let Utils,Calendar;

  $(document).ready(function(){
    if (window.location.pathname === '/reservas') {
        let fecha,hora,plazas,nombre,apellidos,correo,telefonos,checkbox,observaciones;
        Calendar.mes = new Date().getMonth() + 1;
        Calendar.año = new Date().getFullYear();
        Calendar.renderCalendar();
        $('#siguiente').click(function(){
          $('#datos2').css('display','none');
          fecha = $('#fecha').val();
          hora = $('#hora').val();
          plazas = $('#plazas').val();
          $('#checkbox').children('input');
          $('#datos3').css('display','block');

        });
        $('#atras1').click(function(){
          $('#datos3').css('display','none');

          $('#datos2').css('display','block');

        });
        $('#reservar').click(function(){
          $('#datos3').css('display','none');
          $('#parte1').removeClass('ubicacion');
          nombre = $('#name').val();
          apellidos = $('#ap').val();
          correo = $('#email').val();
          telefono = $('#tlfn').val();
          observaciones = $('#observaciones').val();
          checkbox = [];
          $.each($('#check input'),function(index,value){
            console.log(value);
            if(value.checked  == true){
              checkbox.push(value.id);
            }
          });
          $('#datosparaconfirmar').empty().append('<p class="col s12"> Nombre: '+ nombre +'</p>')
          .append('<p class="col s12">Apellidos: '+ apellidos + '</p>')
          .append('<p class="col s12">Correo: '+ correo + '</p>')
          .append('<p class="col s12">Telefono: '+ telefono + '</p>')
          .append('<p class="col s12">Observaciones: '+ observaciones + '</p>');




          $('#parte2').addClass('ubicacion');
          $('#confirmacion').css('display','block');

        });
        $('#atras2').click(function(){
          $('#confirmacion').css('display','none');
          $('#parte2').removeClass('ubicacion');
          $('#parte1').addClass('ubicacion');
          $('#datos3').css('display','block');

        });
        $('#confirmar').click(function(){
          $('#confirmacion').css('display','none');
          $('#parte2').removeClass('ubicacion');
          $('#parte3').addClass('ubicacion');
          $('#realizado').css('display','block');




        });
        $('#salir').click(function(){
          window.location.pathname= '/';
        })
    }
  })


   Calendar = {
     mes: null,
     año: null,

     renderCalendar: function(){
        let max,fecha,fechas, semana;
        fecha = new Date();
        fecha.setDate(1);
        fecha.setMonth(Calendar.mes - 1);
        fecha.setFullYear(Calendar.año);
        semana = 0;
        switch(Calendar.mes){
          case 1:
          case 3:
          case 5:
          case 7:
          case 8:
          case 10:
          case 12:
              max = 31;
              break;
          case 2:
              max = 28;
              break;
          default:
              max = 30;
              break;

        }
        fechas = [];
        auxfechas = [];
        console.log(max);
        for (var i = 0; i < max; i++) {
          console.log(fecha);
          auxfechas[fecha.getDay()] = fecha.getDate();
          console.log(fecha.getDay());
          fecha = new Date(fecha.getTime() + (60 * 60 * 24 * 1000))
          if (fecha.getDay()==1) {

            fechas[semana]=auxfechas;
            auxfechas = [];
            semana++;
          }
        }
        console.log(fechas);




     }
   }
   Utils = {

    getAjax: function(url,success=false) {
     let ajax;
     ajax = $.ajax({
       url:url,



     }).done(success);
   },
   postAjax: function(url,data,success=false){
     let ajax
     ajax = $.ajax({
       url:url,
       data:data,
       method: 'POST'
     }).done(function(data){
       console.log(data);
     });
   }
 };

//})()
