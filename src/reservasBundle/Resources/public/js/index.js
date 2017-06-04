//(function(){
  let Utils,Calendar,Servicios,serviciosdata,fechaActual;

  $(document).ready(function(){

    // Si estamos en consultar
    if (window.location.pathname === '/consultar') {

      $('#buscar').click(function(){
        let busqueda = $('#entrada').val();
        Reservas.getReservasByCodReservas(busqueda,function(data){
          let stralergenos = [];
          Reservas.getAlergenos(data.Id,function(alergenos){
              $.each(alergenos,function(index, row){
                stralergenos.push(row.alergeno.Nombre);

              });
              stralergenos.join(',');
              $('#resultadoConsulta').empty().append('<div id="busqueda" class="card">'+
                '<div class="card-content">'+
                '<span class="card-title">'+ data.Nombre + ' ' + data.Apellidos +'</span>'+
                '<div class ="row">'+
                '<p class="col s5 offset-s1 left-align"><strong>Fecha Servicio:</strong> '+ data.Servicio.FechaServicio +'</p> '+
                '<p class="col s5 left-align"><strong>Correo:</strong> '+ data.Correo + '</p>'+
                '</div>'+
                '<div class="row">'+
                '<p class="col s5 left-align offset-s1"><strong>Telefono:</strong> '+ data.Telefono + '</p>'+
                '<p class="col s5 left-align"><strong>Alergenos:</strong> '+ stralergenos + '</p>'+
                '</div>'+
                '<div class="row">'+
                '<p class="col s12"><strong>Observaciones:</strong> ' + data.Observaciones + '</p>'+
                '</div>'+
                '</div>'+
                '<div class="card-action">'+
                '<a id="anular" class="button especial2" href="#modal2">Anular</a>'+
                '<a id="editarReserva" class="button especial" href="#">Editar</a>'+
                '</div>'+
              '</div>');

              $("#editarReserva").click(function(){

                let checkbox = $('#check input'),
                alergenos = "",
                hora = Utils.converToDate(Reservas.consultaCache.HoraLlegada.date);
                console.log(hora);
                $("#busqueda").css("display", "none");
                $('#plazas').val(Reservas.consultaCache.NPersonas);
                $('#horallegada').val(Utils.timeStringFormat(hora));
                $('#name').val(Reservas.consultaCache.Nombre);
                $('#ap').val(Reservas.consultaCache.Apellidos);
                $('#email').val(Reservas.consultaCache.Correo);
                $('#tlfn').val(Reservas.consultaCache.Telefono);
                $('#observaciones').val(Reservas.consultaCache.Observaciones);
                Reservas.getAlergenos(Reservas.consultaCache.Id,function(data){
                  alergenos = data;
                  $.each(checkbox,function(index,value){
                    $.each(alergenos,function(index,row){
                        if (row.alergeno.Nombre === value.getAttribute('id')) {
                            value.checked  = true;
                        }
                    });
                  });
                });
                console.log(alergenos);



                $("#formularioConsulta").css("display", "block");
              }); //getReservasByCodReservas

              $('#salir').click(function(event){
                event.preventDefault();
                $('#formularioConsulta').css('display','none');
                $('#busqueda').css('display','block');
              });
              $('#anular').click(function(event){
                //event.preventDefault();
                let id=Reservas.consultaCache.Id;
                Reservas.delete(id);
                $('.modal').modal();
                $('#modal2').modal('open');
              });
          });

        }); //buscar

        $("#guardar").click(function(event){
          //event.preventDefault();
          let id = Reservas.consultaCache.Id,
            hora = $('#horallegada').val(),
            npersonas = ''+$('#plazas').val(),
            nombre = $("#name").val(),
            apellidos = $("#ap").val(),
            correo = $("#email").val(),
            telefono = $("#tlfn").val(),
            observaciones = $("#observaciones").val(),
            checkbox = $("#check input"),
            servicio = Reservas.consultaCache.Servicio.id;
            console.log(npersonas);
            console.log(hora);
            console.log(nombre);
            console.log(apellidos);
            console.log(correo);
            console.log(telefono);
            console.log(observaciones);
            alergenos = [];



            $.each(checkbox, function(indice, v){
              console.log(v);
              if (v.checked) {
                alergenos.push(v.getAttribute('id'));
              }
            });
            //validar
            if(Reservas.validateInput() && Utils.timeValidate(hora)){
              Reservas.edit(id,nombre,apellidos,correo,telefono,observaciones,alergenos,npersonas,hora,servicio);
              $('.modal').modal();
              $('#modal1').modal('open');
            }


        });
      });

    }//si en consultar
    // Si estamos en reservas
    if (window.location.pathname === '/reservas') {
      let fecha,hora,plazas,nombre,apellidos,correo,telefonos,checkbox,observaciones;
      fechaActual = new Date();
      Calendar.mes = new Date().getMonth();
      Calendar.año = new Date().getFullYear();
      Servicios.Get(function(){
        Calendar.renderCalendar(Servicios.ServiciosCache);
      })
      Calendar.ToggleButtons();

      //Servicios.add(new Date(),25);
      $('#diaAnterior').click(function(event){
        let date;
        event.preventDefault();
        Calendar.mes = Calendar.mes - 1;
        Calendar.ToggleButtons();
        Calendar.renderCalendar(Servicios.ServiciosCache);

      });
      $('#diaSiguiente').click(function(event){
        event.preventDefault();
        Calendar.mes = Calendar.mes+ 1;
        Calendar.ToggleButtons();
        Calendar.renderCalendar(Servicios.ServiciosCache);
      })
      $('#atras0').click(function(){
        $('#datos1').css('display','none');
        Utils.arriba();
        $('#datos0').css('display','block');
      });
        $('#atras1').click(function(){
          $('#datos2').css('display','none');
          Utils.arriba();
          $('#datos0').css('display','block');
        });
        $('#siguiente').click(function(){
          fecha = $('#fecha').val();
          Reservas.horallegada = $('#horallegada').val();

          if (Utils.timeValidate(Reservas.horallegada)==false) {
            $('#errorhora').fadeIn('slow');
          }else{
            $('#datos2').css('display','none');
            $('#checkbox').children('input');
            Utils.arriba();
            $('#datos3').css('display','block');
          }

        });
        $('#atras2').click(function(){
          $('#datos3').css('display','none');
          Utils.arriba();
          $('#datos2').css('display','block');
        });
        $('#reservar').click(function(){

          if(Reservas.validateInput() == false){
            console.log('error');
            return
          }
          $('#datos3').css('display','none');
          $('#parte1').removeClass('ubicacion');
          Reservas.nombre = $('#name').val();
          Reservas.apellidos = $('#ap').val();
          Reservas.correo = $('#email').val();
          Reservas.telefono = $('#tlfn').val();
          Reservas.observaciones = $('#observaciones').val();
          Reservas.plazas = $('#plazas').val();
          checkbox = [];
          $.each($('#check input'),function(index,value){
            if(value.checked  == true){
              checkbox.push(value.id);
            }
          });
          console.log(checkbox);
          Reservas.alergenos = checkbox;
          $('#datosparaconfirmar').empty().append('<p class="col s12"> Nombre: '+ Reservas.nombre +'</p>')
          .append('<p class="col s12">Apellidos: '+ Reservas.apellidos + '</p>')
          .append('<p class="col s12">Correo: '+ Reservas.correo + '</p>')
          .append('<p class="col s12">Telefono: '+ Reservas.telefono + '</p>')
          .append('<p class="col s12">Observaciones: '+ Reservas.observaciones + '</p>');
          p = $('<p class="col s12">Alergenos: </p>');
          Reservas.alergenos.forEach(function(value){
            p.text(p.text() +  value + ',');
          });
          $('#datosparaconfirmar').append(p);
          console.log(Reservas + ' Confirmacion');
          $('#parte2').addClass('ubicacion');
          Utils.arriba();
          $('#confirmacion').css('display','block');
        });
        $('#atras3').click(function(){
          $('#confirmacion').css('display','none');
          $('#parte2').removeClass('ubicacion');
          $('#parte1').addClass('ubicacion');
          Utils.arriba();
          $('#datos3').css('display','block');
        });
        $('#confirmar').click(function(){
          Reservas.add(Reservas.nombre,
            Reservas.apellidos,
            Reservas.correo, Reservas.telefono,
            Reservas.observaciones, Reservas.alergenos
            ,Reservas.idServicio,
            Reservas.EstadoReserva,
            Reservas.plazas, Reservas.horallegada ,function(data){
                console.log(data.error);
                if(data.error === undefined){
                  $('#descargar').click(function(){
                    window.location.pathname = 'pdf/'+data.Id;
                  });
                  $('#confirmacion').css('display','none');
                  $('#parte2').removeClass('ubicacion');
                  $('#parte3').addClass('ubicacion');
                  Utils.arriba();
                  $('#realizado').css('display','block')
                }else{
                  $('#erroradd').text(data.error).fadeIn(1000);
                }
            });

          ;
        });

        $('#salir').click(function(){
          window.location.pathname= '/';
        })
    }
  }); //ready
   //crear el objeto reservas
   Reservas = {
    idServicio:null,
    Nombre: null,
    Apellidos:null,
    Correo: null,
    telefono: null,
    observaciones: null,
    alergenos: null,
    horallegada: null,
    EstadoReserva:null,
    npersonas: null,
    ReservasCache: null,
    consultaCache:null,
    reservaCache: null,
    delete: function(id){
      Utils.postAjax('api/reservas/delete',{
        id:id
      },function(data){
        return data;
      });


    },
    edit: function(id,nombre,apellidos,correo,telefono,observaciones,alergenos,npersonas,horallegada,idservicio){
        Utils.postAjax('api/reservas/edit',{
          id:id,
          nombre:nombre,
          apellidos:apellidos,
          correo:correo,
          telefono:telefono,
          alergenos:alergenos,
          observaciones:observaciones,
          npersonas:npersonas,
          horallegada:horallegada,
          idservicio:idservicio

        },function(data){

          return data;
        })
    },
    getReservasByCodReservas(codigo,success=null){
      Utils.postAjax('api/reservas/codreserva',{
        codigo:codigo
      },function(data){
        Reservas.consultaCache = data;
        success(data);
      });
    },
    // valida los campos del formulario de reservas
    validateInput: function(){
      let correcto = true;
      $('.errores').css('display','none');
      if(/^[a-zA-Z]+(\s*[a-zA-Z]*)*/.test($('#name').val())===false){
        correcto = false;
        console.log("nombre");
        $('#errorname').fadeIn("slow");

      }
      if(/^[a-zA-Z]+(\s*[a-zA-Z]*)*/.test($('#ap').val())===false){
        correcto = false;
        console.log("apellidos");
        $('#errorap').fadeIn("slow");

        }
      if(/^.+@.+\..+/.test($('#email').val())===false){
        correcto = false;
        console.log("correo");
        $('#erroremail').fadeIn("slow");
      }
      if(/^\d\d\d\d\d\d\d\d\d$/.test($('#tlfn').val())===false){
        correcto = false;
        console.log("telefono");
        $('#errortlfn').fadeIn("slow");

      }
      if($('#robot')[0].checked===false){
        correcto = false;
        console.log("robot");
        $('#errorrobot').fadeIn("slow");

      }
      console.log();
      return correcto;

    },
    //se trae las reservas de el servidor
    Get:function(){
      Utils.getAjax('/api/reservas',function(data){
        Reservas.ReservasCache = data;
      });

    },
    // añade reservas
    add:function(nombre,apellidos,correo,telefono,observaciones,alergenos,servicio,estado,npersonas,horallegada,success = null){
      Utils.postAjax('api/reservas/add',{
        nombre: nombre,
        apellidos: apellidos,
        telefono: telefono,
        correo: correo,
        observaciones: observaciones,
        servicio: servicio,
        estado: estado,
        alergenos: alergenos,
        npersonas: npersonas,
        horallegada: horallegada,
      },function(data){
          success(data);
          return data;
      })
    },
    getAlergenos: function(id,success){
      Utils.postAjax('api/reservas/alergenos',{
        id:id
      },function(data){
        Reservas.alergenos = data;
          success(data);
      })

    }

   }
   // Objeto que controla la gestion de los servicios
   Servicios = {
     ServiciosCache: null,//datos de los servicios guardados en cache;
     //devuelve los servicios
     Get: function(success = null){
       Utils.getAjax('/api/servicios',function(data){
         Servicios.ServiciosCache = data;
         success();
       });
     },
     //servicios segun una fecha
     GetByFecha: function(fecha){
       Utils.postAjax('/api/servicios/fecha',{
         fecha:fecha
       },function(data){
         return data;
       });
     },
     //devuelve las fechas de los servicios
     GetFechas: function(){
       let datos = Servicios.ServiciosCache;
       let fechas = [];
       let fecha;
       $.each(datos,function(value){
         fecha = Utils.converToDate(value.FechaServicio);
       })
     },
     //añade un servicio
     add: function(date,plazas){
       Utils.postAjax('api/servicios/add',{
         Plazas:plazas,
         FechaServicio: Utils.datePostformat(date)
       },function(){
         return true
       })
     },
     //devuelve las plazas ocupadas de un servicio
     getPlazasOcupadas: function(id, success = null){
       Utils.postAjax('api/reservas/plazas',{
         id: id
       },function(data){
         success(data);
         console.log(data);
       })
     }
   };
   Calendar = {
     mes: null,
     meses: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
     //habilita o deshabilita los botones del calendario en función del mes
     ToggleButtons: function(){
       if(Calendar.mes <= fechaActual.getMonth()){
         $('#diaAnterior').attr('disabled','disabled');
       }else{
         $('#diaAnterior').removeAttr('disabled');
       }
       if(Calendar.mes >= 11){
         $('#diaSiguiente').attr('disabled','disabled');
       }else{
         $('#diaSiguiente').removeAttr('disabled');
       }
     },
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
            Servicios.getPlazasOcupadas(row.Id, function(fila){
              let img = 'cardImageVerde.jpg';
              if (fila.length > 0) {
                  if (parseInt(fila[0].Plazas)-parseInt(fila[0].POcupadas) <= 0) {
                      img = 'cardImageRojo.jpg'
                  }
              }
              $('#datos1 .row #servicios').append("<div class='cards'><div class='card white horizontal'>"+
                "<div class='card-image'>"+
                  "<img src='bundles/reservas/img/"+img+"'>"+
                "</div>"+
                "<div class='card-stacked'>"+
                  "<div class='card-content'>"+
                    "<p><strong>Disponible</strong></p>"+
                    "<p>"+ row.Fecha.date +"</p>"+
                    "<p>"+ row.Plazas +" plazas</p>"+
                  "</div>"+
                  "<div class='card-action'>"+
                    "<button id='"+row.Id+"' class='button especial next'>Elegir</button>"+
                  "</div>"+
                "</div>"+
              "</div></div>");
              switch(data.length){
                case 1:
                $('#datos1 .row #servicios .cards').addClass('col s12');
                break;
                default:
                $('#datos1 .row #servicios .cards').addClass('col s6');
                break;
              }
              $('.next').click(function(){
                Reservas.idServicio = $(this).attr('id');
                console.log($(this).attr('id') + 'id de el servicio');
                $('#datos1').css('display','none');
                $('#datos2').css('display','block');
              })
            });
          });
          return true;
        });
     },
     colorearServicios: function(id,bservicio){
       Servicios.getPlazasOcupadas(id,function(data){
         if (data.length > 0) {
           let diferencia= (parseInt(data[0].Plazas)-parseInt(data[0].POcupadas)),
           porcentage = parseInt(data[0].Plazas) * 0.8;
           if (diferencia < 0) {
             bservicio.addClass("colorOcupado");
             bservicio.attr('estado','1');
             console.log(bservicio.parent().children('bservicio').length);
             if (bservicio.parent().children('.bservicio').length === 1) {
               bservicio.parent().parent().children('.bcalendario').addClass('colorOcupado');
             }
             console.log("deberia de haber cambiado al color rojo");
           }else{
              if (parseInt(data[0].POcupadas) > porcentage) {
                  bservicio.addClass('colorPocoDisp');
                  bservicio.attr('estado','1');
                  if (bservicio.parent().children('.bservicio').length === 1) {
                    bservicio.parent().parent().children('.bcalendario').addClass('colorPocoDisp');
                  }
              }else{
                  bservicio.addClass('colorDisponible');
                  bservicio.attr('estado','2');
                  if (bservicio.parent().children('.bservicio').length === 1) {
                      bservicio.parent().parent().children('.bcalendario').addClass('colorDisponible');
                  }
              }
           }
         }else{
           bservicio.addClass("colorDisponible");
           bservicio.attr('estado','2');
           if (bservicio.parent().children('.bservicio').length === 1) {
               bservicio.parent().parent().children('.bcalendario').addClass('colorDisponible');

           }

         }
       })
     },
     renderCalendar: function(servicios){
        let max,fecha,fechas, semana;
        fecha = new Date();
        fecha.setDate(1);
        fecha.setMonth(Calendar.mes);
        $('#mesActual').empty().text(Calendar.meses[Calendar.mes]);
        $('#calendar').empty();
        semana = 0;
        switch(Calendar.mes){
          case 0:
          case 2:
          case 4:
          case 6:
          case 7:
          case 9:
          case 11:
              max = 31;
              break;
          case 1:
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
          console.log(fecha.getDate()+'/'+(fecha.getMonth()+1)+' '+fecha.getDay());
          fecha = new Date(fecha.getTime() + (60 * 60 * 24 * 1000));
          if (fecha.getDay()==1) {

            fechas[semana]=auxfechas;
            auxfechas = [];
            semana++;
          }
        }
        if ($.inArray(auxfechas,fechas) == -1 ) {
          fechas[semana] = auxfechas;
        }
        console.log(max);
        console.log(fechas);
        fechas.forEach(function(value,index){
          let domingo= null,colorestado = "green";

          tr = $('<tr id = "'+ index +'"></tr>');
          for (var i = 1; i < 7; i++) {
            if (value[i]) {
              if (Calendar.EnableDate(value[i],servicios)===true) {
                td = $('<td id="'+i+'"><button class="btn-floating btn-tiny bcalendario" >'+value[i]+'</button></td>')
                bservicios = "";
                divservicios = $('<div class="bservicios "></div>');
                servicios.forEach(function(row){

                    let fecha= Utils.converToDate(row.FechaServicio),
                    hora=Utils.timeStringFormat(fecha);
                    if (value[i]=== fecha.getDate() && Calendar.mes=== fecha.getMonth() && fechaActual.getFullYear() === fecha.getFullYear()) {
                      console.log(fecha);

                      bservicios = $('<button id ="'+ row.id +'" class="bservicio btn-floating btn-tiny">'+hora+'</button>');
                      Calendar.colorearServicios(row.id, bservicios);
                      divservicios.append(bservicios);
                   }
                    td.append(divservicios);
                });
                tr.append(td);
              }else{
                tr.append('<td id="'+i+'">'+value[i]+'</td>');
              }
            }else{
              tr.append('<td></td>');
            }
          }
          if (value[0]) {
            if (Calendar.EnableDate(value[0],servicios)===true) {
              td = $('<td id="0"><button class="btn-floating btn-tiny bcalendario" >'+value[0]+'</button></td>')
              bservicios = "";
              divservicios = $('<div class="bservicios"></div>');
              servicios.forEach(function(row){

                  if (value[0]=== fecha.getDate() && Calendar.mes === fecha.getMonth() && fechaActual.getFullYear() === fecha.getFullYear()) {
                    let fecha= Utils.converToDate(row.FechaServicio),
                    hora=Utils.timeStringFormat(fecha);

                    bservicios += '<button id="'+row.id+'" class="bservicio btn-floating btn-tiny">'+hora+'</button>';
                    divservicios.append(bservicios);
                    $('.bservicio').click(function(){
                      Reservas.idServicio = $(this).attr('id');
                      console.log($(this).attr('id') + 'id de el servicio');
                      $('#datos0').css('display','none');
                      $('#datos2').css('display','block');
                    })
                  }
                  td.append(divservicios);
              });
              tr.append(td);
            }else{
              tr.append('<td id="'+i+'">'+value[0]+'</td>')
            }
          }
          $('.bservicio').click(function(){
            Reservas.idServicio = $(this).attr('id');
            Reservas.EstadoReserva = $(this).attr('estado');
            $('#datos0').css('display','none');
            $('#datos2').css('display','block');
          })
          $('#calendar').append(tr);
        });
        /*$(".bcalendario").mouseenter(function(){
          $('.modal').modal();
          $('#modal').modal('open');
        });*/


     }
   };
   Utils = {
    arriba: function(){
      $('html, body').stop().animate({scrollTop:0},"slow");
    },
    timeValidate: function(str){
      $patron = new RegExp("^[0-2][0-3]:[0-5][0-9]$");
      return $patron.test(str)
    },
    timeStringFormat: function(date){
      let hora = ''+date.getHours(),
      minutes = ''+date.getMinutes();
      if(hora.length === 1){
        hora = '0'+hora;
      }
      if (minutes.length === 1) {
        minutes = '0'+minutes;
      }

      return hora +':' + minutes;
    },
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
      fecha = fecha.replace('-','/')
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
