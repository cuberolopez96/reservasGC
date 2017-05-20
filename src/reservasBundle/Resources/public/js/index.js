//(function(){
  let Utils,Calendar,Servicios,serviciosdata;

  $(document).ready(function(){
  if (window.location.pathname === '/reservas') {
  let
  fecha,hora,plazas,nombre,apellidos,correo,telefonos,checkbox,observaciones; //

  Calendar.mes = new Date().getMonth();
  Calendar.año = new Date().getFullYear();
  Servicios.Get();
  Servicios.getPlazasOcupadas(new Date(2017,04,16,12,0,0));

  //Servicios.add(new Date(),25);
  Reservas.add('Ambrosio','Atapuerca','alcachofa@alcachofa','393220340284','soy de albacete',2);

        $('#atras0').click(function(){
          $('#datos1').css('display','none');
          $('#datos0').css('display','block');
        });
        $('#atras1').click(function(){
          $('#datos2').css('display','none');
          $('#datos1').css('display','block');
        });
        $('#siguiente').click(function(){
          $('#datos2').css('display','none');
          fecha = $('#fecha').val();
          hora = $('#hora').val();
          plazas = $('#plazas').val();
          $('#checkbox').children('input');
          $('#datos3').css('display','block');

        });
        $('#atras2').click(function(){
          $('#datos3').css('display','none');

          $('#datos2').css('display','block');

        });
        $('#reservar').click(function(){
          $('#datos3').css('display','none');
          $('#parte1').removeClass('ubicacion');
          Reservas.nombre = $('#name').val();
          Reservas.apellidos = $('#ap').val();
          Reservas.correo = $('#email').val();
          Reservas.telefono = $('#tlfn').val();
          Reservas.observaciones = $('#observaciones').val();
          checkbox = [];
          $.each($('#check input'),function(index,value){
            console.log(value);
            if(value.checked  == true){
              checkbox.push(value.id);
            }
          });
          $('#datosparaconfirmar').empty().append('<p class="col s12"> Nombre: '+ Rrservas.nombre +'</p>')
          .append('<p class="col s12">Apellidos: '+ Reservas.apellidos + '</p>')
          .append('<p class="col s12">Correo: '+ Reservas.correo + '</p>')
          .append('<p class="col s12">Telefono: '+ Reservas.telefono + '</p>')
          .append('<p class="col s12">Observaciones: '+ Reservas.observaciones + '</p>');




          $('#parte2').addClass('ubicacion');
          $('#confirmacion').css('display','block');

        });
        $('#atras3').click(function(){
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
//crear el objeto reservas
  Reservas = {
    idServicio:null,
    Nombre: null,
    Apellidos:null,
    Correo: null,
    telefono: null,
    observaciones: null,




    Get:function(){
      Utils.getAjax('/api/reservas',function(data){
        console.log(data);
      });

    },
    add:function(nombre,apellidos,correo,telefono,observaciones,servicio,npersonas){
      Utils.postAjax('api/reservas/add',{
        nombre: nombre,
        apellidos: apellidos,
        telefono: telefono,
        correo: correo,
        observaciones: observaciones,
        servicio: servicio,
        npersonas: 25
      },function(){
        return true
      })
    }


  }
   Servicios = {
     ServiciosCache: null,
     Get: function(){
       Utils.getAjax('/api/servicios',function(data){
         Servicios.ServiciosCache = data;
         Calendar.renderCalendar(data);
       });
     },

     GetFechas: function(){
       let datos = Servicios.ServiciosCache;
       let fechas = [];
       let fecha;
       //console.log(Servicios.ServiciosCache);
       $.each(datos,function(value){
         fecha = Utils.converToDate(value.FechaServicio);
       })
     },
     add: function(date,plazas){
       Utils.postAjax('api/servicios/add',{
         Plazas:plazas,
         FechaServicio: Utils.datePostformat(date)

       },function(){
         return true
       })
     },
     getPlazasOcupadas: function(date){
       Utils.postAjax('api/reservas/plazas',{
         fecha: Utils.datePostformat(date)
       },function(data){
         console.log(data);
       })
     }

   };
   Calendar = {
     mes: null,

     //habilitar dias
     EnableDate: function(dia,servicios){
       //Declaramos variables que vayamos a usar
       let paramDate, dateServicio, flag;
       flag = false;
       //Convertimos ese dia en un date
       paramDate=new Date();
       paramDate.setMonth(Calendar.mes);
       paramDate.setDate(dia);
       //Recorremos los servicios
      $.each(servicios,function(index,value){
         // la fecha del Servicio en un date;
         dateServicio = Utils.converToDate(value.FechaServicio);
         // Comparamos date y devolvemos true si es igual
         if (Utils.dateStringFormat(paramDate) === Utils.dateStringFormat(dateServicio)) {

           flag =  true;
         }
       });
       //false si no lo son
       return flag;


     },
     //crear el metodo selectDate;
     SelectDate: function(dia){
       console.log('he entrado');
        let date;
        date = new Date();
        date.setMonth(Calendar.mes);
        date.setDate(dia);
        date.setFullYear(2017);
        Utils.postAjax('api/servicios/fecha',{
          fecha: Utils.dateStringFormat(date)
        },function(data){
          console.log('he entrado 2');
          $('#datos1  .row #servicios').empty();
          data.forEach(function(row){
            console.log(row);
            $('#datos1 .row #servicios').append("<div class='card white'>"+
            "<div class='card-content'>Fecha:"+ row.Fecha.date +"</div>" +
            "<div class='card-content'>Plazas:"+ row.Plazas +"</div>"+
            "<div class='card-footer'>"+
            "<button  id='"+row.id+"' class='button especial next'>Elegir</button>"+
            "</div>");
          });
          switch(data.length){
          case 1:
              $('#datos1 .row #servicios .card').addClass('col s12');
              break;
          case 2:
              $('#datos1 .row #servicios .card').addClass('col s6');
              break;
          case 3:
              $('#datos1 .row #servicios .card').addClass('col s4');
              break;
          default:
              $('#datos1 .row #servicios .card').addClass('col s3');
              break;

          }
          $('.next').click(function(){
            let id = $(this).attr('id');
            $('#datos1').css('display','none');
            $('#datos2').css('display','block');
          });
          return true;
        });
     },
     renderCalendar: function(servicios){
        let max,fecha,fechas, semana;
        fecha = new Date();
        fecha.setDate(1);
        fecha.setMonth(Calendar.mes - 1);

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
              //algoritmo año bisiesto
              break;
          default:
              max = 30;
              break;

        }
        fechas = [];
        auxfechas = [];
        for (var i = 0; i < max; i++) {
          auxfechas[fecha.getDay()] = fecha.getDate();
          fecha = new Date(fecha.getTime() + (60 * 60 * 24 * 1000))
          if (fecha.getDay()==1) {

            fechas[semana]=auxfechas;
            auxfechas = [];
            semana++;
          }
        }
        if (auxfechas.length != 7 && auxfechas.length > 0 ) {
          fechas[semana] = auxfechas;
        }

        fechas.forEach(function(value,index){
          let domingo= null
          tr = $('<tr id = "'+ index +'"></tr>');
          for (var i = 1; i < 7; i++) {
            if (value[i]) {
              if (Calendar.EnableDate(value[i],servicios)===true) {
                tr.append('<td id="'+i+'"><button class="bcalendario">'+value[i]+'</button></td>');
              }else{
                tr.append('<td id="'+i+'">'+value[i]+'</td>')

              }
            }else{
              tr.append('<td></td>');
            }
          }
          if (value[0]) {
            if (Calendar.EnableDate(value[0],servicios)===true) {
              tr.append('<td id="'+i+'"><button class="bcalendario">'+value[0]+'</button></td>');
            }else{
              tr.append('<td id="'+i+'">'+value[0]+'</td>')

            }
          }
          $('#calendar').append(tr);
        });



        $(".bcalendario").click(function(){
          let dia;
          dia = $(this).text();
          console.log(dia);
          console.log(Calendar);
          Calendar.SelectDate(dia);
          $('#datos0').css('display','none');
          $('#datos1').css('display','block');
        });
     }
   };
   Utils = {
    dateStringFormat:function(date){
      return date.getDate() + '-' + (date.getMonth() + 1) + '-' + date.getFullYear();
    },
    datePostformat(date){
      return date.getDate() + '-' + (date.getMonth() + 1) + '-' + date.getFullYear() + ' ' +
       date.getHours() + ':' + date.getMinutes() + ':' + date.getSeconds();

    },
    converToDate: function(fecha){
      let tiempo,day,month,year;
      let date = new Date();
      fecha  = fecha.split(' ');
      tiempo = fecha[1];
      fecha = fecha[0];
      fecha = fecha.split('/');
      day = fecha[2];
      month = fecha[1];
      year = fecha[0];
      tiempo = tiempo.split(':');
      hour = tiempo[0];
      minutes = tiempo[1];
      seconds = tiempo[2];
      date.setDate(day);
      date.setMonth(month - 1);
      date.setFullYear(year);
      date.setHours(hour);
      date.setMinutes(minutes);
      date.setSeconds(seconds);
      return date;
    },
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
     }).done(success);
   }
 };

//})()
